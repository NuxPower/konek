# AGENTS.md

Shared brief for any AI coding tool working in this repo (Claude Code, Codex, etc).
Keep this file short — it's pointers, not a tutorial. See `README.md` for full setup/usage docs and `docs/DEPLOYMENT.md` for production.

## What this is

KONEK — a student marketplace for Central Mindanao University. Laravel 12, PHP ^8.2, MariaDB/MySQL, Vite + Tailwind frontend. Admin and Member roles; job posting/application workflows; identity verification via a separate Dockerized matcher service (`docker/identity-matcher`); SMS OTP and email verification.

## Stack facts that matter

- Authorization goes through Policies (`app/Policies/*`) and Form Request `authorize()` methods — follow that pattern, don't add ad-hoc controller checks.
- Business logic lives in `app/Services/*`, not controllers, where it already exists (`JobService`, `ApplicationService`, `ReportService`).
- No queued jobs exist anywhere despite `QUEUE_CONNECTION=database` in prod and a documented Supervisor worker. Slow I/O (identity analyzer HTTP call, SMS send) currently runs inline in the request — known gap, not a pattern to copy.

## Commands

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve                # or: composer run dev (concurrent server+queue+logs+vite on :8002)
```

Checks (run all before calling anything done):
```bash
php artisan test                 # or: composer test (clears config first)
./vendor/bin/pint --test
npm run build
composer audit
npm audit
```

**Test DB quirk:** `phpunit.xml` sets `DB_DATABASE=konek_test`, `DB_USERNAME=root`, socket `/run/mysqld/mysqld.sock`, but omits `DB_PASSWORD` — Laravel falls back to whatever `.env.testing` has for that key. If `.env.testing` doesn't exist or its password doesn't match the local MariaDB root password, tests fail on auth, not on real bugs. Check `.env.testing` before assuming a DB failure is a code problem.

## Known open issues (from most recent full audit)

Prioritized punch list — pick from here rather than re-discovering the same things:
1. Queue the identity-analyzer HTTP call and SMS send instead of running them inline in the request.
2. `AdminDashboardController`/`MemberDashboardController` `index()` methods are 60-100 line god-methods running 15-20+ ad-hoc queries inline (including a 12-iteration month-by-month count loop) — extract to a `DashboardService`, but only after test coverage exists (see next).
3. No tests for `Admin\JobManagementController`, `Admin\ActivityLogController`, `Admin\ApplicationManagementController`, `HomeController`, or freelancer job browsing; `tests/Unit/` is empty besides the stock example. Add coverage before refactoring the controllers above.

## Collaboration rules

- One task = one branch/PR. Commit messages state the *why*, not just the what — the next tool/session picks up context from `git log`, not chat history.
- Don't auto-apply fixes (`pint` without `--test`, `npm audit fix`, etc.) without a reviewable diff — always leave the actual change to be reviewed before merge.
- Put test evidence in the PR/commit body as real numbers (e.g. "95/95 pass, 320 assertions"), not "tests pass".
- Non-obvious decisions (e.g. "deleted X instead of fixing it because Y") go in the commit message or a `docs/decisions/` note — not left implicit.
- Never commit `.env` or real secrets. `KONEK_SEED_DEMO` must stay `false` in anything production-adjacent.
