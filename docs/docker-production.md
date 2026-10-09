# Production Docker deployment

Run these commands from the StudyOrganizer checkout. The app runs on PHP 8.4
with Apache, PostgreSQL 17, a queue worker, and a scheduler. Apache serves only
`public/`; PHP-FPM is not exposed as an HTTP backend. Vite and Filament assets
are built into the image. Dependencies come from the committed lock files.

The old `docker-compose.yaml` is the development database setup. Always specify
the production files with `-f`. The `studyorganizer-production` project name
keeps the new production database and volume separate from development.
Do not override this with the development project's `COMPOSE_PROJECT_NAME`.
This guide does not transfer an existing installation's data automatically.

## Choose the proxy mode

| Proxy installation | Compose files | Proxy upstream |
| --- | --- | --- |
| Nginx installed on the VM | prod + native | `http://127.0.0.1:3060` |
| Nginx in Docker | prod + proxy | `http://studyorganizer-web:80` on `web-proxy` |

Use exactly one proxy override. The base file publishes no ports. PostgreSQL
has no host port, and only the HTTP service joins `web-proxy`. Native mode binds
the HTTP port to loopback; ports 80 and 443 remain available for the shared proxy.

## 1. Prepare the environment and images

```bash
cp .env.production.example .env.production
chmod 600 .env.production
nano .env.production
```

Set `APP_IMAGE`, `APP_URL`, `APP_KEY`, `DB_PASSWORD`, and `TRUSTED_PROXIES`.
`APP_URL` must be the public HTTPS origin, without a trailing slash, and match
the StudyOrganizer domain in the proxy configuration. Keep
`SESSION_SECURE_COOKIE=true` for HTTPS. `APP_DEBUG` is forced off in Compose.

For a new installation, generate a key and a separate database password:

```bash
printf 'base64:'; openssl rand -base64 32
openssl rand -hex 32
```

Paste the first result into `APP_KEY` and the second into `DB_PASSWORD`.
Preserve the existing Laravel key when moving an existing installation. Keep
the key stable on updates. Secrets are supplied at runtime and excluded from
the Docker build context. Configure real SMTP settings when password-reset or
verification emails should be delivered; the default mailer only logs emails.

Build and publish an image from your development machine:

```bash
docker buildx build --platform linux/amd64 \
  --target runtime -t your-dockerhub-user/studyorganizer:1.0.0 --push .
```

Use `linux/arm64` for an ARM VM, or
`--platform linux/amd64,linux/arm64` to publish both architectures. Set
`APP_IMAGE` to that published version in the VM's `.env.production`. Publish a
new version for each release; pin a digest when immutable deployments are needed.

Alternatively, build on the deployment host: set `APP_IMAGE=studyorganizer:local`
and add `-f docker-compose.build.yml` to the Compose command below. Run
`dc build web` instead of `dc pull`. That builds the same image used by all
four application services.

## 2. Select Compose files and trusted proxies

For **host-installed Nginx**, define this Bash function in the VM terminal:

```bash
dc() {
  docker compose --env-file .env.production \
    -f docker-compose.prod.yml -f docker-compose.native.yml "$@"
}
```

For **Docker Nginx**, create the shared network if it does not already exist,
then use the other override:

```bash
docker network inspect web-proxy >/dev/null 2>&1 || docker network create web-proxy
dc() {
  docker compose --env-file .env.production \
    -f docker-compose.prod.yml -f docker-compose.proxy.yml "$@"
}
```

Both Nginx modes overwrite the incoming forwarding headers. Laravel trusts
only the addresses specified in `TRUSTED_PROXIES`, including after config
caching. This makes HTTPS URL generation, redirects, signed URLs and client IPs
work through the proxy. See [Laravel's trusted proxy documentation](https://laravel.com/docs/12.x/requests#configuring-trusted-proxies).

For **native Nginx**, create the Compose network before starting containers:

```bash
docker network inspect studyorganizer-production_default >/dev/null 2>&1 || docker network create studyorganizer-production_default \
  --label com.docker.compose.project=studyorganizer-production \
  --label com.docker.compose.network=default
docker network inspect studyorganizer-production_default --format '{{range .IPAM.Config}}{{.Gateway}}{{end}}'
```

Use the gateway printed above in `TRUSTED_PROXIES` (an individual IPv4 address
or `/32`). On a Linux VM, connections from native Nginx through the loopback
port mapping normally reach the container from this gateway. If you changed
`COMPOSE_PROJECT_NAME`, use that project's network name instead. Confirm the
observed source against `dc logs web` when checking HTTPS below.

For **Docker Nginx**, inspect the proxy's address on the shared network:

```bash
docker inspect production-web-proxy \
  --format '{{(index .NetworkSettings.Networks "web-proxy").IPAddress}}'
docker network inspect web-proxy --format '{{range .IPAM.Config}}{{.Subnet}}{{end}}'
```

Set `TRUSTED_PROXIES` to the proxy's address, or to that network's subnet if all
containers on it are trusted. A subnet survives proxy container recreation;
an individual address is narrower but must be updated when it changes. Multiple
values are comma-separated. `*` is available for fully trusted private setups,
but allows any peer that can reach the app to supply forwarding headers.

## 3. Start the application

```bash
dc config --quiet
dc pull
dc up -d --wait postgres
dc run --rm migrate
dc up -d --wait web worker scheduler
dc ps
```

Run the migration step successfully before starting the web app or workers.
Migrations are explicit tools commands, never automatic on a web restart.
PostgreSQL health checks gate startup, and workers wait for the web health check.
`/up` verifies both Laravel boot and database connectivity.
See [Compose startup order](https://docs.docker.com/compose/how-tos/startup-order/).

For native Nginx, check the backend on the VM:

```bash
curl --fail http://127.0.0.1:3060/up
```

If you change `STUDYORGANIZER_HTTP_PORT`, change
`STUDYORGANIZER_UPSTREAM` in the proxy's `.env.native` to match. The default
already matches the existing proxy scripts.

## 4. Enable the Nginx site and HTTPS

For native Nginx, from the nginx-reverse-proxy checkout, set the real
`ACME_EMAIL` and `STUDYORGANIZER_DOMAIN` in `.env.native`, then:

```bash
sudo ./scripts/native/issue-studyorganizer-certificate.sh
sudo ./scripts/native/generate-studyorganizer-conf.sh --install
```

Point the domain's DNS to the VM first and allow inbound 80/443. Existing sites
stay managed by their own app scripts. For Docker Nginx, use that proxy's
existing bootstrap/certificate workflow with its `STUDYORGANIZER_DOMAIN`; its
StudyOrganizer template already points to the correct alias and HTTP port.
After deploying the proxy configuration changes from this checkout, run
`./scripts/reload.sh` in the proxy checkout to apply the corrected timeout
inheritance. This changes only Docker Nginx; native Nginx already uses 120s.

From a client machine, verify the public origin:

```bash
curl --fail https://studyorganizer.ljupchoangelovski.com/up
curl -sSI https://studyorganizer.ljupchoangelovski.com/login
curl -sSI https://studyorganizer.ljupchoangelovski.com/dashboard
```

Confirm the login response sets a cookie with `Secure`, and the unauthenticated
dashboard redirects to an `https://` login URL. Verify login and an upload in
the browser. If the redirect uses HTTP, check `TRUSTED_PROXIES` against the
backend's observed source address, then recreate services to refresh config.

The proxy and Apache accept a total request body up to 64 MiB. PHP permits
20 MiB per file. Existing Laravel validation permits 20 MiB course materials
and 10 MiB per submission attachment; the container setup retains those rules.
Only `storage/app/public` is publicly linked. Course materials remain private
and are delivered through the application's authorized controllers.

## Updates and maintenance

Before an update, take a database and file backup. Set a new `APP_IMAGE` tag,
then use the same `dc` function:

```bash
dc pull
dc exec --user www-data web php artisan down --retry=60
dc stop worker scheduler
dc run --rm migrate
dc up -d --wait --force-recreate web
dc exec --user www-data web php artisan up
dc up -d --force-recreate worker scheduler
```

If migrations fail, resolve the error before proceeding. If the web health
check fails, inspect `dc logs web postgres`; the maintenance marker lives in
the persistent storage volume and is removed by `artisan up`. All app services
must use the same image version. Config is cached independently in each
container using its runtime environment. No production secret is cached in the
image. Code changes require rebuilding the image, including OPcache refresh.

Queue workers use a 60-second timeout and a 90-second shutdown grace period;
the database queue's default retry interval is 90 seconds. The scheduler runs
`schedule:work`; only scale it to one instance. Use `dc logs --tail=100 web worker
scheduler postgres` for diagnostics. Do not run `artisan optimize:clear` during
normal updates: the shared database cache also backs application runtime state.

## Persistent data and backups

The `studyorganizer-production_postgres_data` volume contains the production database.
`studyorganizer-production_app_storage` contains uploads, private materials, generated
files, and Laravel's runtime storage. They survive recreation and ordinary
`dc down`. `dc down -v` deletes production data.

Create a database dump in the ignored `backups/` directory:

```bash
mkdir -p backups
chmod 700 backups
dc exec -T postgres sh -c 'pg_dump -U "$POSTGRES_USER" -d "$POSTGRES_DB" -Fc' \
  > "backups/studyorganizer-db-$(date +%Y%m%d-%H%M%S).dump"
```

For a matching database and uploads backup, pause writes first with
`artisan down` and `dc stop worker scheduler`. Then make the DB dump and archive
storage:

```bash
dc exec -T --user www-data web tar -C /var/www/html -czf - storage \
  > "backups/studyorganizer-storage-$(date +%Y%m%d-%H%M%S).tar.gz"
dc exec --user www-data web php artisan up
dc up -d worker scheduler
```

Keep encrypted copies off the VM, preserve `.env.production` separately, and
test restoring to a separate database before relying on a backup. Changing
`DB_PASSWORD` after database initialization does not change the existing
PostgreSQL role password; change the role password deliberately before updating
and recreating application containers.

## Move an existing installation

Preserve the existing `APP_KEY` and transfer both the database and
`storage/app` (including `materials` and `private`). Existing file records
reference those disk paths; copying only `public/storage` loses private files.
Freeze writes while taking the final transfer backup.

For PostgreSQL 14 or an existing RDS database, use a logical dump and restore
into the new PostgreSQL 17 database, then run migrations. Never mount a version
14 data directory into version 17. PostgreSQL requires a logical dump/restore
or `pg_upgrade` across major versions; see [the versioning policy](https://www.postgresql.org/support/versioning/).

For SQLite, a PostgreSQL deployment requires a separate data transfer; Laravel
migrations create tables but do not copy rows. The existing transition script
changes connection settings and runs migrations; it is not a substitute for
verifying the transferred data. Keep the old instance and backup until users,
courses, submissions, and file downloads have been checked in the new deployment.

## Validation

`dc config --quiet` checks the selected Compose configuration without starting
containers. On a working Docker engine, build the image and run the deployment
against a disposable database, then check `/up`, HTTPS redirects, login, assets,
and file persistence across recreation before enabling production traffic.

PHP regression tests for proxy header trust are in
`tests/Feature/ProxyHeadersTest.php`. With local development dependencies
installed, run `php artisan test --filter=ProxyHeadersTest`. Production images
omit development packages and test files.
