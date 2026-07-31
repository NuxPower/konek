# Build and Evidence Checklist

## Automated checks

- n8n workflow tests: _10/10 passed on 2026-07-30_
- Laravel webhook tests: _3/3 passed, 3 assertions, on 2026-07-30_
- Full Laravel suite: _101/101 passed, 331 assertions_
- Pint: _153/153 files passed_
- Vite production build: _60 modules transformed successfully_
- n8n 2.32.6 import: _successful_
- n8n HTTP health check: _healthy_
- MariaDB TCP reachability from n8n: _verified_
- Composer audit: _pending explicit approval for Packagist metadata egress_
- npm audit: _pending explicit approval for npm registry metadata egress_

## Manual configuration

- [x] Start n8n with the repository Compose service.
- [x] Create the read-only `n8n_audit` MariaDB account.
- [x] Configure the native MySQL credential.
- [x] Configure the Webhook Header Auth credential.
- [x] Set `N8N_NEW_JOB_WEBHOOK_URL` and `N8N_WEBHOOK_TOKEN` locally.
- [x] Create the `KONEK Job Audits` Google Sheet.
- [x] Configure Google Sheets OAuth and all 13 column mappings.

## Required execution evidence

| Case | Expected result | Verified |
|---|---|---|
| Invalid form job ID | Invalid Request; no database query | [ ] |
| Unknown positive job ID | Job Not Found | [ ] |
| Existing draft KONEK job | Not Published | [ ] |
| Low-quality published job | Needs Review | [x] |
| Complete published job | Passed | [ ] |
| New KONEK job webhook | Automatic KONEK webhook execution | [ ] |

## Final counts

- n8n executions verified: _1/6_
- Google Sheets audit rows: _at least 1 verified_
- KONEK rows changed by n8n: _must remain 0_
- Loom URL: _pending_
- Submission form: _pending_
- Reply email: _pending_

> [!IMPORTANT]
> Prove that the MySQL credential is read-only and explain in the Loom video
> that Laravel remains the only writer to KONEK's core tables.
