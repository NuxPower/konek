# KONEK

KONEK is a student marketplace for the Central Mindanao University community. Members can post work, apply to work, save jobs, and manage applications from one account, while administrators manage platform activity and reports.

## Features

- Admin and member workspaces
- Ownership and action-based authorization
- Job publishing and application review workflows
- Saved jobs and skill-based recommendations
- In-app notifications
- Actionable dashboards
- Filterable admin reports with CSV export
- Responsive green-themed interface

## Requirements

- PHP 8.2 or newer
- Composer 2
- Node.js 20 or newer
- npm
- MariaDB/MySQL
- PHP extensions required by Laravel, including `pdo_mysql`, `mbstring`, and `openssl`

## Local setup

```bash
git clone <repository-url> konek
cd konek
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure the database in `.env`, then run:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`.

## Demo accounts

Demo data is enabled by default outside production.

| Role | Email | Password |
|---|---|---|
| Admin | `admin@cmu.edu.ph` | `password` |
| Member | `poster@cmu.edu.ph` | `password` |
| Member | `applicant@cmu.edu.ph` | `password` |

Change `KONEK_DEMO_PASSWORD` before seeding if the application is accessible beyond a local development machine.

Production seeding excludes demo users and marketplace data unless `KONEK_SEED_DEMO=true` is explicitly configured.

## Development checks

```bash
php artisan test
./vendor/bin/pint --test
npm run build
composer audit
npm audit
```

The current test environment uses the MariaDB database and socket configured in `phpunit.xml`. Adjust those values for your local test database.

## Mail, SMS, and ID proof services

Email verification uses Laravel mail. For real delivery, configure SMTP in `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=no-reply@cmu.edu.ph
MAIL_FROM_NAME="${APP_NAME}"
```

Send a one-off test email:

```bash
php artisan tinker --execute='Illuminate\Support\Facades\Mail::raw("KONEK mail test", fn ($message) => $message->to("yuzuh710@gmail.com")->subject("KONEK mail test"));'
```

Phone OTP can use the local log driver or SMS API PH. Local default:

```dotenv
SMS_DRIVER=log
```

Production SMS API PH:

```dotenv
SMS_DRIVER=smsapiph
SMSAPIPH_ENDPOINT=https://smsapiph.onrender.com/api/v1/send/sms
SMSAPIPH_API_KEY=
SMSAPIPH_TIMEOUT=15
```

Send a one-off SMS test after setting `SMS_DRIVER=smsapiph` and `SMSAPIPH_API_KEY`:

```bash
php artisan tinker --execute='app(App\Services\Sms\SmsSender::class)->send("+639666172691", "KONEK SMS test");'
```

SMS API PH may show a message as `Pending` after KONEK sends it. That means the API accepted the request, but carrier delivery has not been confirmed yet. Check `storage/logs/laravel.log` for the provider message ID and status.

ID proof biometric/OCR matching is handled by the Dockerized matcher service. Start it locally:

```bash
docker compose up --build identity-matcher
curl http://127.0.0.1:8001/health
```

Use these Laravel `.env` values when the matcher is running locally:

```dotenv
IDENTITY_ANALYZER=http
IDENTITY_ANALYZER_URL=http://127.0.0.1:8001/analyze-identity
IDENTITY_ANALYZER_TOKEN=local-dev-token
IDENTITY_OCR_CONFIDENCE=90
IDENTITY_BIOMETRIC_SCORE=90
```

For Railway, deploy `docker/identity-matcher/Dockerfile` as its own service and set Laravel `IDENTITY_ANALYZER_URL` to the Railway service URL. Set `ENABLE_FACE_MODEL=true` in Railway to enable real InsightFace biometric matching.

## Security notes

- Never commit `.env`.
- Set `APP_DEBUG=false` in production.
- Use HTTPS and set `SESSION_SECURE_COOKIE=true`.
- Keep `KONEK_SEED_DEMO=false` in production.
- Run queue workers under a process supervisor.

See [Deployment](docs/DEPLOYMENT.md) for the production checklist.
