# Development guide

## Requirements

- PHP 8.2 or newer and Composer; the production image uses PHP 8.4.
- Node.js 24 and npm for the Vite build.
- SQLite for the default local setup, or PostgreSQL for deployment parity.

The Dockerfile lists the PHP extensions used by Laravel, Filament, file handling,
and PostgreSQL. Install both Composer and npm dependencies from the lock files.
The application uses Laravel 12, Filament 5, and Livewire 4.

## Local setup

Run from the repository checkout:

```bash
composer install
test -f .env || cp .env.example .env
```

For a new local environment, generate the Laravel key. Preserve an existing key
if the environment already contains data you need to keep:

```bash
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm ci
npm run build
php artisan serve
```

SQLite is the default in `.env.example`. For frontend development, run
`npm run dev` in a second terminal while `php artisan serve` handles HTTP.
`composer run dev` can also start the app, queue listener, logs, and Vite together;
it requires development Composer dependencies.

The original `docker-compose.yaml` starts only a PostgreSQL development service.
It reads database settings from `.env`; choose an unused host `DB_PORT` there.
When Laravel runs directly on the host, use `DB_HOST=127.0.0.1` and that published
port. For production containers, the connection is always `postgres:5432`.

## Environment files

| File | Reader | Purpose |
| --- | --- | --- |
| `.env` | Host-run Laravel and development Compose | Local configuration. |
| `.env.production` | Production Compose | Runtime secrets and deployment settings. |
| `.env.example` | Developer copying a template | Public, secret-free local defaults. |
| `.env.production.example` | Operator copying a template | Public production template with blank secrets. |

The shared proxy is a separate repository. Its native Nginx scripts read
`.env.native`; its Docker workflow reads `.env`. These are not StudyOrganizer's
environment files. Run the native config script again after editing the upstream.

Real environment files, database files, private keys, backups, session data,
and uploaded files stay outside Git. Do not include their contents or production
command output in commits, issues, release notes, or screenshots. Generate
production keys/passwords locally; the docs contain only placeholders and
reserved example addresses. The Docker build excludes real environment files.

## Administrator accounts

Create a real admin account with:

```bash
php artisan admin:create
```

It prompts for name, email, password, and confirmation. Passwords are hidden,
must contain at least 12 characters, and are hashed by the User model. The
command sets `UserRole::ADMIN` explicitly and rejects duplicate emails without
overwriting or promoting existing users. Name and email may be supplied with
`--name` and `--email`; password entry remains interactive.

In a deployed container:

```bash
dc exec --user www-data web php artisan admin:create
```

Use the `dc` helper from [the production guide](docker-production.md).
Keep the default interactive terminal; do not add `-T` to an admin-creation
invocation, and do not use `--no-interaction`. Log in at `/login`, then open
`/admin`. Filament's production access contract and the existing admin middleware
both require the admin role.

`make:filament-user` creates name/email/password only. In this app, the database
defaults such accounts to students. It is not a substitute for `admin:create`.
Existing users can be promoted using the separate Tinker instructions in the
production guide; duplicate emails intentionally fail in the creation command.

The general `DatabaseSeeder` creates demonstration accounts and academic data.
Use it only for disposable development environments, with development
dependencies installed. It is not a production admin provisioning command.

## Tests

```bash
npm run build
php artisan test
```

The suite uses an in-memory SQLite database through `phpunit.xml`. Keep
development tests separate from production credentials and data. Focused
deployment/admin regressions can be run with:

```bash
php artisan test --filter='CreateAdminCommandTest|AdminPanelAccessTest|ProxyHeadersTest|HttpBootstrapTest'
```

The admin tests exercise production-mode panel authorization and verify that
students/professors cannot access it. Command tests cover hashing, role
assignment, invalid input, confirmation, duplicate accounts, and noninteractive
invocation. Proxy tests cover trusted and untrusted headers. The bootstrap test
checks the HTTP kernel before Laravel configuration has loaded.

## Deployment and releases

Use [the production guide](docker-production.md) for Compose selection, ports,
database migrations, HTTPS, uploads, backup procedures, and troubleshooting.
Use [the release guide](releasing.md) to publish one image for all application
services and tie it to a GitHub release tag.
