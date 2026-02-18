#!/usr/bin/env bash
set -euo pipefail

# ----------------------------
# Usage:
#   ./switch-to-rds-postgres.sh \
#     --db-host your-rds.xxxxxx.region.rds.amazonaws.com \
#     --db-name studyorganizer \
#     --db-user dbuser \
#     --db-pass 'supersecret' \
#     --db-port 5432 \
#     --seed
#
# Notes:
# - Run this on the EC2 instance.
# - Assumes Laravel app already deployed and working.
# ----------------------------

APP_DIR="/var/www/laravel"
DB_HOST=""
DB_NAME=""
DB_USER=""
DB_PASS=""
DB_PORT="5432"
DO_SEED="false"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --db-host) DB_HOST="$2"; shift 2 ;;
    --db-name) DB_NAME="$2"; shift 2 ;;
    --db-user) DB_USER="$2"; shift 2 ;;
    --db-pass) DB_PASS="$2"; shift 2 ;;
    --db-port) DB_PORT="$2"; shift 2 ;;
    --seed) DO_SEED="true"; shift 1 ;;
    -h|--help)
      echo "See script header for usage."
      exit 0
      ;;
    *)
      echo "Unknown argument: $1"
      exit 1
      ;;
  esac
done

if [[ -z "$DB_HOST" || -z "$DB_NAME" || -z "$DB_USER" || -z "$DB_PASS" ]]; then
  echo "Missing required args. Need: --db-host --db-name --db-user --db-pass (and optional --db-port, --seed)"
  exit 1
fi

if [[ ! -f "$APP_DIR/artisan" ]]; then
  echo "artisan not found at $APP_DIR. Is this the Laravel app directory?"
  exit 1
fi

ENV_FILE="$APP_DIR/.env"
if [[ ! -f "$ENV_FILE" ]]; then
  echo ".env not found at $ENV_FILE"
  exit 1
fi

echo "==> Detecting PHP + installing pgsql driver if needed..."

# Detect PHP version (major.minor) from CLI, e.g. 8.3
PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || true)"
if [[ -z "$PHP_VER" ]]; then
  echo "PHP not found in PATH."
  exit 1
fi

# Check if pdo_pgsql is loaded
if php -m | grep -qiE '^pdo_pgsql$'; then
  echo "==> pdo_pgsql already installed."
else
  echo "==> Installing Postgres extensions for PHP $PHP_VER..."
  sudo apt-get update -y
  # On Ubuntu, either of these tends to work depending on repo packaging
  sudo apt-get install -y "php${PHP_VER}-pgsql" || sudo apt-get install -y php-pgsql

  # Verify
  if ! php -m | grep -qiE '^pdo_pgsql$'; then
    echo "Failed to install/enable pdo_pgsql. Check PHP version packages and try again."
    exit 1
  fi
fi

# Restart PHP-FPM (try common service names)
echo "==> Restarting PHP-FPM..."
if systemctl list-units --type=service --all | grep -q "php${PHP_VER}-fpm.service"; then
  sudo systemctl restart "php${PHP_VER}-fpm"
elif systemctl list-units --type=service --all | grep -q "php-fpm.service"; then
  sudo systemctl restart "php-fpm"
else
  echo "Warning: Could not find php-fpm service name automatically. Restart it manually if needed."
fi

echo "==> Backing up .env..."
cp "$ENV_FILE" "$ENV_FILE.bak.$(date +%Y%m%d_%H%M%S)"

echo "==> Updating .env for RDS PostgreSQL..."

# Helper to set or add KEY=VALUE in .env
set_env() {
  local key="$1"
  local value="$2"
  local file="$3"

  # Escape backslashes and ampersands for sed replacement
  local escaped
  escaped="$(printf '%s' "$value" | sed -e 's/[\/&]/\\&/g')"

  if grep -qE "^${key}=" "$file"; then
    sed -i -E "s/^${key}=.*/${key}=${escaped}/" "$file"
  else
    echo "${key}=${value}" >> "$file"
  fi
}

# Core DB settings
set_env "DB_CONNECTION" "pgsql" "$ENV_FILE"
set_env "DB_HOST" "$DB_HOST" "$ENV_FILE"
set_env "DB_PORT" "$DB_PORT" "$ENV_FILE"
set_env "DB_DATABASE" "$DB_NAME" "$ENV_FILE"
set_env "DB_USERNAME" "$DB_USER" "$ENV_FILE"
set_env "DB_PASSWORD" "$DB_PASS" "$ENV_FILE"

# Optional but helpful in production
# (Only set if already present; don’t surprise-change app env settings)
# You can uncomment if you want to enforce:
# set_env "APP_ENV" "production" "$ENV_FILE"
# set_env "APP_DEBUG" "false" "$ENV_FILE"

echo "==> Clearing Laravel caches (config cache will otherwise ignore new .env)..."

cd "$APP_DIR"

# Use php explicitly to avoid PATH issues
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# If you use config caching in production, rebuild it now:
php artisan config:cache || true

echo "==> Running migrations..."
php artisan migrate:fresh --force

if [[ "$DO_SEED" == "true" ]]; then
  echo "==> Running seeders..."
  php artisan db:seed --force
fi

echo "==> Quick connectivity check (Laravel thinks it's using):"
php artisan tinker --execute="dump(config('database.default')); dump(DB::connection()->getPdo() ? 'DB OK' : 'DB FAIL');" || true

echo "==> Done."
echo "Reminder: RDS SG must allow inbound 5432 from the EC2 SG (not public)."
