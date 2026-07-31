# Loom Walkthrough Script

Target duration: 6–9 minutes.

## Opening

“Hello, this is Yohan Lukin. For the Backend Developer Challenge, I integrated
n8n with KONEK, a Laravel student marketplace. The workflow audits real KONEK
job listings without allowing n8n to modify marketplace data.”

## Architecture

Show the full canvas.

“There are two entry points. The n8n Form Trigger allows an administrator to
request an audit using a KONEK job ID and explicitly satisfies the form-trigger
requirement. The webhook trigger integrates with KONEK's `JobService`, so newly
created jobs can be audited automatically.”

“Both paths are normalized by a Code node. Invalid IDs are rejected before any
database query. The native MySQL node uses a SELECT-only account to read the
real KONEK `jobs`, `users`, and `categories` tables.”

“A second Code node scores title, description, requirements, budget,
deadline, and possible off-platform contact details. Condition nodes distinguish
missing jobs, unpublished jobs, quality failures, and passing jobs. Every
result is appended through the native Google Sheets integration.”

## Demonstration

1. Submit a known KONEK job ID through the n8n form.
2. Show the MySQL result and calculated score.
3. Show the highlighted Passed or Needs Review path.
4. Show the appended Google Sheets row.
5. Create a synthetic KONEK job through the application.
6. Show the automatic webhook execution.
7. Show that the KONEK job remains unchanged.

## Design decisions

- Laravel is the only writer to marketplace records.
- n8n uses a least-privilege database account.
- Form and webhook input share one normalization path.
- Scoring is deterministic and explainable.
- Google Sheets is an operations view, not a second database.
- Webhook failure never rolls back KONEK job creation.

## Closing

“The workflow meets the challenge requirements while respecting KONEK's
existing service and authorization boundaries. Thank you for reviewing my
submission.”

