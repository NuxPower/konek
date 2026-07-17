# KONEK deployment

This guide assumes a Linux host with Nginx, PHP-FPM, MariaDB/MySQL, Node.js, and a process supervisor.

## Production environment

Start from `docs/production.env.example` and provide real secrets through the hosting platform or a protected `.env` file.

Required settings:

- `APP_ENV=production`
- `APP_DEBUG=false`
- A generated `APP_KEY`
- The public HTTPS `APP_URL`
- Production database credentials
- `SESSION_SECURE_COOKIE=true`
- `KONEK_SEED_DEMO=false`
- A real mail transport if email verification and password resets are enabled

## First deployment

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

Do not run `db:seed` in production unless reference data is required and the seeder configuration has been reviewed.

The web server document root must point to `public/`, never the repository root.

## Nginx outline

```nginx
server {
    listen 443 ssl http2;
    server_name konek.example.edu.ph;
    root /var/www/konek/public;

    index index.php;
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Terminate HTTP at HTTPS and enable HSTS after confirming the domain and certificate configuration.

## Queue worker

Use Supervisor or systemd:

```ini
[program:konek-worker]
command=php /var/www/konek/artisan queue:work --sleep=3 --tries=3 --max-time=3600
directory=/var/www/konek
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/konek-worker.log
```

Restart workers after each deployment:

```bash
php artisan queue:restart
```

## Scheduler

Add one cron entry:

```cron
* * * * * cd /var/www/konek && php artisan schedule:run >> /dev/null 2>&1
```

## Release sequence

```bash
php artisan down --retry=60
git pull --ff-only
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
php artisan queue:restart
php artisan up
```

For zero-downtime hosting, build releases in timestamped directories and switch a `current` symlink after migrations and health checks succeed.

## Verification

```bash
curl --fail https://konek.example.edu.ph/up
php artisan about --only=environment
php artisan migrate:status
composer audit
npm audit --omit=dev
```

Verify login for admin and member accounts, then test both member workflows: posting work and applying to another member's job.

## Backups and rollback

- Back up the database before migrations.
- Retain the previous release directory and built assets.
- Prefer forward-fix migrations. If rollback is safe, use `php artisan migrate:rollback --step=1 --force`.
- Store user uploads on persistent storage outside ephemeral release directories.
