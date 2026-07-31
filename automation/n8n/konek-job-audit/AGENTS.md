# AGENTS.md

Scoped instructions for KONEK's n8n Backend Developer Challenge workflow. The
repository root `AGENTS.md` remains authoritative for shared project rules.

## Purpose

Audit real KONEK job listings through n8n while keeping Laravel as the only
writer to core marketplace tables.

The workflow has two entry points:

- an n8n Form Trigger for manual job-ID audits, required by the challenge; and
- a Webhook Trigger called by `App\Services\JobService` after job creation.

Both paths read KONEK's MariaDB using the native MySQL node, calculate an
explainable quality score in Code nodes, branch using IF nodes, and append the
result to Google Sheets.

## Safety boundaries

- The n8n database credential must have `SELECT` only on `jobs`, `users`, and
  `categories`.
- Never let n8n insert, update, or delete core KONEK records.
- Laravel Policies, Form Requests, and Services remain the mutation boundary.
- Never commit database passwords, Google OAuth secrets, webhook tokens,
  credential exports, or configured workflow exports.
- Keep the checked-in workflow inactive and credential-free.
- Keep the two source copies under `code/` synchronized with the embedded Code
  nodes in `workflow/konek-job-audit.json`.

## Structure

```text
konek-job-audit/
├── AGENTS.md
├── README.md
├── code/
│   ├── evaluate-job-quality.js
│   └── normalize-audit-request.js
├── docs/
│   ├── BUILD-CHECKLIST.md
│   ├── LOOM-SCRIPT.md
│   ├── MANUAL-SETUP.md
│   └── SUBMISSION.md
├── samples/
│   ├── draft-job.json
│   ├── manual-request.json
│   ├── needs-review-job.json
│   ├── quality-pass-job.json
│   └── webhook-request.json
├── tests/
│   └── workflow.test.mjs
└── workflow/
    └── konek-job-audit.json
```

## Verification

```bash
rtk proxy node tests/workflow.test.mjs
rtk php artisan test --filter=N8nJobWebhookTest
```

Before submission, record exact test, database, Sheet, and n8n execution
evidence in `docs/BUILD-CHECKLIST.md`.

