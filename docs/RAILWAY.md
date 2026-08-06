# Deploying KONEK to Railway

Web, worker, and scheduler all build from the **same repo** using Railway's
native PHP/Laravel builder (Railpack) — no custom Dockerfile needed for the
KONEK app itself. Railpack auto-detects Laravel from `composer.json`,
installs PHP + Composer + Node, runs `npm run build`, caches config/routes/
views, and serves the app via FrankenPHP + Caddy. Each service just points
at the same repo with a different **Start Command** override.

The `docker/identity-matcher` service is the one exception — it keeps its
own Dockerfile and is deployed as a separate service with its root
directory set to that subfolder (step 5).

> **Note:** `composer.json` previously declared `"php": "^8.2"` while
> `composer.lock` had already drifted to packages requiring PHP >=8.4.1
> (symfony 8.x, nesbot/carbon 3.13). Railpack reads the `composer.json`
> constraint to pick a PHP version, provisioned 8.2, and `composer install`
> failed. This is now fixed — `composer.json` requires `^8.4` to match what's
> actually locked. If a build ever fails again with a "your php version does
> not satisfy that requirement" error, that mismatch is what to check first.

## 1. Database

Add a **MySQL** plugin to the Railway project. Note the reference variables
it exposes (`MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`,
`MYSQLPASSWORD`) — map them to Laravel's `DB_*` vars below using Railway
variable references so they stay in sync if the plugin ever rotates
credentials.

## 2. Web service

Create a service from this GitHub repo, root directory `/` (default).
Railway should show "Detected Php" / "Found Laravel app" in the build logs —
if it instead tries to use a Dockerfile, check Settings → Build → Builder is
set to the default (Railpack), not pinned to Dockerfile.

Variables (Settings → Variables):

```
APP_NAME=KONEK
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<your-railway-domain>
APP_KEY=                         # php artisan key:generate --show (run locally)
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_CHANNEL=stack
LOG_LEVEL=warning

KONEK_SEED_DEMO=false
```

`PORT` is injected and handled by Railpack/FrankenPHP automatically — you
don't need to set or reference it.

Under Settings → Networking, generate a public domain. Confirm the
healthcheck path `/up` (set in `railway.json`) goes green after first
deploy.

### Migrations

Use Railway's **Pre-Deploy Command** (Settings → Deploy → Pre-Deploy
Command), set to:

```
php artisan migrate --force
```

This runs once per deploy, before the new version takes traffic — set it
only on the web service so the worker/scheduler services (same build,
different start command) don't race the schema on simultaneous boots.

## 3. Worker service

New service → same GitHub repo → same root directory. Settings → Deploy →
**Custom Start Command**:

```
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

Copy the same `APP_KEY` / `DB_*` / cache-store variables as the web service
(use Railway [shared/project variables](https://docs.railway.com/guides/variables#shared-variables)
so you don't hand-sync two copies). Leave **Pre-Deploy Command** empty here.
No public domain needed.

## 4. Scheduler service (optional for now)

`routes/console.php` has no `Schedule::` entries yet, so this service is a
placeholder until a scheduled task exists (e.g. OTP cleanup). When needed:
same pattern as the worker, Custom Start Command:

```
php artisan schedule:work
```

Railway has no host cron — `schedule:work` runs Laravel's scheduler loop
in-process instead of the cron-based approach in `docs/DEPLOYMENT.md`.

## 5. Identity matcher service

New service → same GitHub repo, but set **root directory to
`docker/identity-matcher`** so Railway builds that subdirectory's own
Dockerfile instead of using Railpack.

Mirror the env vars from `docker-compose.yml`:

```
IDENTITY_ANALYZER_TOKEN=<shared secret, matches web/worker>
SCHOOL_ID_REGEX=[A-Za-z0-9][A-Za-z0-9-]*\d[A-Za-z0-9-]*
OCR_CONFIDENCE_THRESHOLD=90
BIOMETRIC_SCORE_THRESHOLD=90
MAX_UPLOAD_MB=10
INSIGHTFACE_MODEL=buffalo_l
ENABLE_FACE_MODEL=true
```

On the web and worker services, point at it over Railway's private network
so it's never exposed publicly:

```
IDENTITY_ANALYZER=http
IDENTITY_ANALYZER_URL=http://identity-matcher.railway.internal:8000/analyze-identity
IDENTITY_ANALYZER_TOKEN=<same shared secret>
```

## 6. n8n (optional)

Deploy from Railway's official n8n template rather than hand-rolling a
service for it. Once it's up, configure the job-audit workflow's webhook
there and set on the web service:

```
N8N_NEW_JOB_WEBHOOK_URL=https://<n8n-domain>/webhook/...
N8N_WEBHOOK_TOKEN=<shared secret you set in the n8n workflow>
```

## 7. File storage — read before going live

Railway's filesystem is ephemeral per deploy/restart. With
`FILESYSTEM_DISK=local` (the current default), any uploaded ID photos are
lost the next time the web service redeploys or restarts.

Pick one:

- **Recommended: S3-compatible storage.** Set `FILESYSTEM_DISK=s3` and fill
  `AWS_ACCESS_KEY_ID` / `AWS_SECRET_ACCESS_KEY` / `AWS_DEFAULT_REGION` /
  `AWS_BUCKET` (any S3-compatible provider works — AWS S3, Cloudflare R2,
  Backblaze B2).
- **Interim: Railway Volume.** Attach a volume to the web service only,
  mounted at `storage/app/public`. Only safe with a single web replica, and
  the worker/scheduler services won't see the files (fine if they never read
  uploads directly).

## 8. Verify after first deploy

```bash
curl --fail https://<your-railway-domain>/up
railway run php artisan about --only=environment
railway run php artisan migrate:status
```

Then log in as an admin and a member account, and exercise both member
workflows (posting work, applying to another member's job) end to end.

## 9. Rollback

Use Railway's deployment history to redeploy a prior build. For a bad
migration, `railway run php artisan migrate:rollback --step=1 --force` — back
up the database beforehand, same as `docs/DEPLOYMENT.md`.
