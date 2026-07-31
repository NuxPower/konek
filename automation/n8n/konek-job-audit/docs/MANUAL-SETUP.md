# KONEK-Integrated n8n Setup

This runbook configures the imported **KONEK Job Quality Audit** workflow
against the repository's existing MariaDB database.

> [!WARNING]
> Do not use KONEK's root database account in n8n. Create the SELECT-only
> account below. Do not display passwords, Google secrets, or the webhook token
> in the Loom recording.

## 1. Start KONEK and n8n

From `/home/hanyu/Projects/konek`:

```bash
rtk docker compose up -d n8n
rtk php artisan serve
```

Open:

- KONEK: `http://127.0.0.1:8000`
- n8n editor: `http://localhost:5678`

> [!IMPORTANT]
> Always open the n8n editor through `localhost`, not `127.0.0.1`, while
> configuring Google OAuth. The redirect returns to `localhost`; mixing the
> two hostnames creates separate browser cookies and causes n8n to reject the
> callback as Unauthorized. Laravel may still call the production webhook at
> `127.0.0.1:5678`.

The Compose service uses host networking for local development. This allows
n8n's MySQL node to reach the same host-only MariaDB instance used by Laravel
without publishing port 3306.

Open **KONEK Job Quality Audit** in n8n and keep it inactive during
configuration.

## 2. Create a read-only KONEK database account

Generate a password locally:

```bash
rtk openssl rand -base64 24
```

Keep it in a password manager temporarily. Do not add it to a committed file.

Open MariaDB as an administrator:

```bash
rtk mysql -u root -p
```

Replace `<generated-password>` in the following SQL before running it:

```sql
CREATE USER IF NOT EXISTS 'n8n_audit'@'localhost'
    IDENTIFIED BY '<generated-password>';
CREATE USER IF NOT EXISTS 'n8n_audit'@'127.0.0.1'
    IDENTIFIED BY '<generated-password>';

GRANT SELECT ON konek.jobs TO 'n8n_audit'@'localhost';
GRANT SELECT ON konek.users TO 'n8n_audit'@'localhost';
GRANT SELECT ON konek.categories TO 'n8n_audit'@'localhost';

GRANT SELECT ON konek.jobs TO 'n8n_audit'@'127.0.0.1';
GRANT SELECT ON konek.users TO 'n8n_audit'@'127.0.0.1';
GRANT SELECT ON konek.categories TO 'n8n_audit'@'127.0.0.1';

FLUSH PRIVILEGES;
```

Verify the grants:

```sql
SHOW GRANTS FOR 'n8n_audit'@'localhost';
SHOW GRANTS FOR 'n8n_audit'@'127.0.0.1';
EXIT;
```

Only `SELECT` grants on those three tables should appear.

## 3. Configure the native MySQL node

In n8n, open **Read KONEK Job** and create `KONEK Read-Only MariaDB`:

| Field | Value |
|---|---|
| Host | `127.0.0.1` |
| Database | `konek` |
| User | `n8n_audit` |
| Password | generated password |
| Port | `3306` |
| SSL | disabled for the local host-only connection |
| SSH tunnel | disabled |

Test and save the credential.

Confirm the node:

- uses **Execute Query**;
- begins with `SELECT`;
- joins `jobs`, `users`, and `categories`;
- uses `WHERE jobs.id = $1`; and
- maps the normalized job ID through Query Parameters using
  `{{ [$json.job_id] }}` so n8n supplies an array for the `$1` placeholder.

Do not grant or add INSERT, UPDATE, or DELETE access.

## 4. Configure authenticated KONEK webhooks

Generate another random value:

```bash
rtk openssl rand -hex 32
```

Add these values to the local uncommitted `.env`:

```text
N8N_NEW_JOB_WEBHOOK_URL=http://127.0.0.1:5678/webhook/konek-job-audit
N8N_WEBHOOK_TOKEN=<generated-webhook-token>
```

Clear Laravel's cached configuration:

```bash
rtk php artisan config:clear
```

In n8n:

1. Open **KONEK Job Created Webhook**.
2. Keep authentication set to **Header Auth**.
3. Create a credential named `KONEK Webhook Token`.
4. Header name: `X-Konek-Webhook-Token`.
5. Header value: the same `N8N_WEBHOOK_TOKEN`.
6. Save the node.

The production webhook URL works only while the workflow is active. Use the
Form Trigger for initial testing, then activate the workflow before testing
automatic job creation.

## 5. Create the Google Sheet

Create a spreadsheet named `KONEK Job Audits`. Rename its first tab to exactly
`Job Audits`.

Paste this tab-separated header row into cell A1:

```text
Requested At	Job ID	Title	Posted By	Category	Job Status	Request Source	Requested By	Review Reason	Quality Score	Audit Status	Next Action	Audit Flags
```

The values should occupy columns A through M.

Copy the spreadsheet ID from:

```text
https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/edit
```

## 6. Configure Google Sheets OAuth

Open **Append Job Audit to Google Sheets**.

If n8n provides **Connect my account**, sign in with the Google account that
owns the Sheet and name the credential `KONEK Job Audit Sheets`.

If Client ID and Client Secret are required:

1. Create a project in [Google Cloud Console](https://console.cloud.google.com).
2. Enable **Google Sheets API** and **Google Drive API**.
3. Configure Google Auth Platform/OAuth consent as External and Testing.
4. Add your own Google account as a test user.
5. Create a Web application OAuth client.
6. Copy n8n's exact redirect URL into **Authorized redirect URIs**. It normally
   looks like:

```text
http://localhost:5678/rest/oauth2-credential/callback
```

7. Paste the Client ID and Client Secret into n8n.
8. Connect the Google account and approve access.

Then configure the node:

- Operation: **Append Row**
- Document: the `KONEK Job Audits` spreadsheet
- Sheet: `Job Audits`
- Mapping: **Map Each Column Manually**

Verify all 13 imported mappings correspond to the header row. Refresh the
field list after selecting the document if necessary.

## 7. Test the Form Trigger paths

Use **Test Workflow** and open the Form Trigger's test form.

### Invalid Request

- Job ID: `0`
- Requested by: `Yohan Lukin`
- Reason: `Routine audit`

Expected: Invalid Request. The MySQL node must not run.

### Job Not Found

- Job ID: `999999999`

Expected: the read-only query returns no row and the workflow records Job Not
Found.

### Not Published

1. In KONEK, create and save a job as a draft.
2. Copy its numeric ID from the job URL.
3. Submit that ID through the n8n audit form.

Expected: Not Published.

### Needs Review

Create a published KONEK job that still passes Laravel validation but is
intentionally sparse:

- title between 5 and 9 characters;
- description between 30 and 99 characters;
- requirements between 20 and 49 characters; and
- a test contact such as `applicant@example.com` in the description.

Submit its ID. Expected score: below 70 and Needs Review.

### Passed

Create or select a published job with:

- a descriptive title;
- description of at least 100 characters;
- requirements of at least 50 characters;
- a valid budget range;
- a future deadline more than three days away; and
- no off-platform contact details.

Expected score: 70 or higher and Passed.

## 8. Test the automatic KONEK webhook

1. Confirm the MySQL, Header Auth, and Google Sheets credentials are saved.
2. Activate **KONEK Job Quality Audit**.
3. Create a new synthetic job through KONEK.
4. Open n8n's Executions page.
5. Confirm the execution began at **KONEK Job Created Webhook**.
6. Confirm Request Source is `KONEK webhook`.
7. Confirm the audit row appears in Google Sheets.
8. Confirm the KONEK job still exists and was not changed by n8n.

If no execution appears:

```bash
rtk php artisan config:clear
rtk docker compose logs --tail=100 n8n
```

Also verify the workflow is active and the token values match.

## 9. Evidence and Loom preparation

Before recording:

- close all credential dialogs and `.env`;
- disable notifications;
- show the complete workflow canvas;
- keep one Form Trigger execution and one KONEK webhook execution ready;
- keep the Google Sheet audit rows visible;
- prepare a read-only database query showing the KONEK source job; and
- follow `docs/LOOM-SCRIPT.md`.

Record actual results in `docs/BUILD-CHECKLIST.md`, then use
`docs/SUBMISSION.md` for the email response.
