# KONEK

KONEK is a role-based talent marketplace for the Central Mindanao University community. Clients publish opportunities and review candidates, freelancers discover and save jobs, and administrators manage platform activity and reports.

## Features

- Admin, client, and freelancer workspaces
- Role and ownership authorization
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
| Client | `client@cmu.edu.ph` | `password` |
| Freelancer | `freelancer@cmu.edu.ph` | `password` |

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

## Security notes

- Never commit `.env`.
- Set `APP_DEBUG=false` in production.
- Use HTTPS and set `SESSION_SECURE_COOKIE=true`.
- Keep `KONEK_SEED_DEMO=false` in production.
- Run queue workers under a process supervisor.

See [Deployment](docs/DEPLOYMENT.md) for the production checklist.
