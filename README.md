# ERP Lite

ERP Lite is a Laravel 13 application for outlet, inventory, purchasing, production, sales, accounting, and reporting workflows.

## Local requirements

- PHP 8.3 or newer with `curl`, `dom`, `fileinfo`, `gd`, `intl`, `mbstring`, `pdo_mysql`, `simplexml`, `xml`, `xmlreader`, `xmlwriter`, and `zip`
- Composer 2
- MariaDB/MySQL
- Node.js 20.19+ or 22.12+ and npm

The tested XAMPP environment uses PHP 8.5, MariaDB 10.4, Node.js 24, and npm 11.

## XAMPP setup

Start Apache/MySQL from XAMPP, then run these commands from the project directory in PowerShell:

```powershell
composer install

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    php artisan key:generate
}
```

Configure the database connection in `.env`. For an existing database, create a backup before running the normal, non-destructive migration command:

```powershell
php artisan backup:database
php artisan migrate
php artisan storage:link
```

Never use `migrate:fresh`, `db:wipe`, or seeders against a database containing business data.

Install and build the frontend:

```powershell
npm ci
npm run build
```

Start the local application:

```powershell
php artisan optimize:clear
php artisan serve --host=127.0.0.1 --port=8000
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

For frontend development with hot reload, run `npm run dev` in a second terminal.

## Background work

The application uses database-backed queues and schedules an hourly database backup. Run these in separate terminals when those features are needed:

```powershell
php artisan queue:work
php artisan schedule:work
```

The backup command automatically finds `C:\xampp\mysql\bin\mysqldump.exe` on Windows. For another MySQL installation, add its full executable path to `.env`:

```dotenv
MYSQLDUMP_PATH=C:\path\to\mysqldump.exe
```

Backups are written to `storage/app/backups`. The command keeps the latest five archives by default; use `php artisan backup:database --keep=10` to retain more.

## Verification

```powershell
composer validate --no-check-publish
composer check-platform-reqs
composer audit
php artisan test
npm run build
npm audit
```

Useful application checks:

```powershell
php artisan about
php artisan route:list
php artisan migrate:status
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize:clear
```

## Docker

The included PHP-FPM image targets PHP 8.5. Docker Compose expects an externally reachable database configured through `.env`; the XAMPP workflow above is the primary local development setup.
