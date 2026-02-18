#!/usr/bin/env bash
set -euo pipefail
export DEBIAN_FRONTEND=noninteractive

# ========= Defaults =========
APP_DIR="/var/www/laravel"
BRANCH_DEFAULT="main"
APP_USER_DEFAULT="ubuntu"

# ========= Args =========
# Usage:
#   sudo bash setup.sh -r https://github.com/user/repo.git -d sub.domain.com -e you@email.com
# Optional:
#   -b main
#   -u ubuntu
#   --no-seed (skip migrate:fresh --seed)
#   --no-certbot (skip certbot)
#
# Notes:
# - Repo must be public (no GitHub login).
# - Cloudflare/DNS must already point DOMAIN -> this EC2 public IPv4 (gray cloud) BEFORE certbot.

REPO_URL=""
DOMAIN=""
CERTBOT_EMAIL=""
BRANCH="${BRANCH_DEFAULT}"
APP_USER="${APP_USER_DEFAULT}"
DO_SEED=1
DO_CERTBOT=1

die() { echo "ERROR: $*" >&2; exit 1; }

while [[ $# -gt 0 ]]; do
  case "$1" in
    -r|--repo) REPO_URL="${2:-}"; shift 2 ;;
    -d|--domain) DOMAIN="${2:-}"; shift 2 ;;
    -e|--email) CERTBOT_EMAIL="${2:-}"; shift 2 ;;
    -b|--branch) BRANCH="${2:-}"; shift 2 ;;
    -u|--user) APP_USER="${2:-}"; shift 2 ;;
    --no-seed) DO_SEED=0; shift ;;
    --no-certbot) DO_CERTBOT=0; shift ;;
    -h|--help)
      cat <<'HELP'
Usage:
  sudo bash setup.sh -r <repo_url> -d <domain> -e <certbot_email> [options]

Required:
  -r, --repo     Public GitHub repo URL (https://github.com/...git)
  -d, --domain   Domain/subdomain (e.g. studyorganizer.example.com)
  -e, --email    Email for Let's Encrypt registration

Optional:
  -b, --branch   Git branch (default: main)
  -u, --user     App user (default: ubuntu)
  --no-seed      Skip php artisan migrate:fresh --seed
  --no-certbot   Skip Certbot HTTPS

Important:
  - DNS must already resolve DOMAIN -> this instance public IPv4.
  - Cloudflare must be "DNS only" (gray cloud) during Certbot.
HELP
      exit 0
      ;;
    *) die "Unknown argument: $1" ;;
  esac
done

[[ -n "${REPO_URL}" ]] || die "Missing -r/--repo"
[[ -n "${DOMAIN}" ]] || die "Missing -d/--domain"
[[ -n "${CERTBOT_EMAIL}" ]] || die "Missing -e/--email"

echo "== Inputs =="
echo "APP_DIR=${APP_DIR}"
echo "REPO_URL=${REPO_URL}"
echo "BRANCH=${BRANCH}"
echo "DOMAIN=${DOMAIN}"
echo "APP_USER=${APP_USER}"
echo "CERTBOT_EMAIL=${CERTBOT_EMAIL}"
echo "DO_SEED=${DO_SEED}"
echo "DO_CERTBOT=${DO_CERTBOT}"

# ========= System =========
echo "== Update system =="
apt-get update -y
apt-get upgrade -y

echo "== Install base packages =="
apt-get install -y curl git unzip ca-certificates gnupg lsb-release software-properties-common

echo "== Install Nginx =="
apt-get install -y nginx
systemctl enable --now nginx

echo "== Remove Apache if present (prevents port 80 conflicts) =="
if systemctl list-unit-files | grep -q "^apache2"; then
  systemctl disable --now apache2 || true
  apt-get purge -y apache2 apache2-utils apache2-bin apache2.2-common || true
  apt-get autoremove -y || true
fi

echo "== Install PHP + common extensions (Laravel + Filament + SQLite) =="
apt-get install -y php php-fpm php-cli \
  php-curl php-mbstring php-xml php-bcmath php-zip \
  php-intl php-sqlite3

echo "== Detect PHP-FPM socket =="
PHP_FPM_SOCK="$(find /run/php -maxdepth 1 -type s -name "php*-fpm.sock" | head -n 1 || true)"
[[ -n "${PHP_FPM_SOCK}" ]] || { ls -lah /run/php || true; die "Could not find PHP-FPM socket in /run/php"; }
echo "Using PHP-FPM socket: ${PHP_FPM_SOCK}"

echo "== Install Composer =="
cd /root
curl -sS https://getcomposer.org/installer | php
install -m 0755 composer.phar /usr/local/bin/composer
composer --version

echo "== Install Node.js 20 (for Vite build) =="
apt-get remove -y nodejs npm || true
apt-get autoremove -y || true
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs
node -v
npm -v

# ========= App =========
echo "== Clone repo into ${APP_DIR} =="
mkdir -p "$(dirname "${APP_DIR}")"
rm -rf "${APP_DIR}"
git clone --branch "${BRANCH}" --depth 1 "${REPO_URL}" "${APP_DIR}"

echo "== Ensure ownership for deploy user =="
chown -R "${APP_USER}:${APP_USER}" "${APP_DIR}"

echo "== Install PHP dependencies (as ${APP_USER}, no prompts) =="
sudo -u "${APP_USER}" -H bash -lc "
  cd '${APP_DIR}'
  composer install --no-interaction
"

echo "== Ensure SQLite DB file exists (if using sqlite) =="
sudo -u "${APP_USER}" -H bash -lc "
  mkdir -p '${APP_DIR}/database'
  touch '${APP_DIR}/database/database.sqlite'
"

echo "== Fix Laravel runtime permissions (logs/cache) =="
usermod -aG www-data "${APP_USER}" || true

chown -R "${APP_USER}:www-data" "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" "${APP_DIR}/database"
find "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" "${APP_DIR}/database" -type d -exec chmod 2775 {} \;
find "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" "${APP_DIR}/database" -type f -exec chmod 664 {} \;

touch "${APP_DIR}/storage/logs/laravel.log" || true
chown "${APP_USER}:www-data" "${APP_DIR}/storage/logs/laravel.log" || true
chmod 664 "${APP_DIR}/storage/logs/laravel.log" || true

echo "== Create .env if missing =="
if [ ! -f "${APP_DIR}/.env" ]; then
  cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"
  chown "${APP_USER}:${APP_USER}" "${APP_DIR}/.env"
fi

echo "== Generate app key =="
sudo -u "${APP_USER}" -H bash -lc "
  cd '${APP_DIR}'
  php artisan key:generate --force
"

echo "== Build Vite assets (as ${APP_USER}) =="
sudo -u "${APP_USER}" -H bash -lc "
  cd '${APP_DIR}'
  npm ci
  npm run build
"

echo "== Configure Nginx site for Laravel =="
NGINX_SITE="/etc/nginx/sites-available/laravel"
cat > "${NGINX_SITE}" << EOF
server {
    listen 80;
    server_name ${DOMAIN};

    root ${APP_DIR}/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:${PHP_FPM_SOCK};
    }

    location ~ /\. {
        deny all;
    }
}
EOF

ln -sf "${NGINX_SITE}" /etc/nginx/sites-enabled/laravel
rm -f /etc/nginx/sites-enabled/default || true

nginx -t
systemctl reload nginx

echo "== Laravel caches (as ${APP_USER}) =="
sudo -u "${APP_USER}" -H bash -lc "
  cd '${APP_DIR}'
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true
"

echo "== Set Laravel APP_URL to blank (automatic) =="
if [ -f "${APP_DIR}/.env" ]; then
  if grep -q '^APP_URL=' "${APP_DIR}/.env"; then
    sed -i 's/^APP_URL=.*/APP_URL=/' "${APP_DIR}/.env"
  else
    echo 'APP_URL=' >> "${APP_DIR}/.env"
  fi
fi

sudo -u "${APP_USER}" -H bash -lc "
  cd '${APP_DIR}'
  php artisan config:clear
"

echo "== Restart services =="
systemctl restart nginx
systemctl restart php*-fpm 2>/dev/null || true

echo "== Migrations/Seeding =="
if [ "${DO_SEED}" -eq 1 ]; then
  sudo -u "${APP_USER}" -H bash -lc "
    cd '${APP_DIR}'
    php artisan migrate:fresh --seed --force
  "
else
  sudo -u "${APP_USER}" -H bash -lc "
    cd '${APP_DIR}'
    php artisan migrate --force || true
  "
fi

# ========= HTTPS (Certbot) =========
if [ "${DO_CERTBOT}" -eq 1 ]; then
  echo "== Install Certbot =="
  apt-get install -y certbot python3-certbot-nginx

  echo "== Pre-check: DNS resolves domain to THIS instance public IPv4 =="
  PUB_IP="$(curl -sS --max-time 5 http://checkip.amazonaws.com || true)"
  [[ -n "${PUB_IP}" ]] || PUB_IP="$(curl -sS --max-time 5 https://api.ipify.org || true)"
  [[ -n "${PUB_IP}" ]] || die "Could not determine public IPv4 of this instance"

  RESOLVED_IP="$(getent hosts "${DOMAIN}" | awk '{print $1}' | head -n 1 || true)"
  [[ -n "${RESOLVED_IP}" ]] || die "Domain ${DOMAIN} did not resolve via getent"
  echo "Instance Public IPv4: ${PUB_IP}"
  echo "Domain resolves to  : ${RESOLVED_IP}"

  if [ "${RESOLVED_IP}" != "${PUB_IP}" ]; then
    die "DNS mismatch: ${DOMAIN} resolves to ${RESOLVED_IP} but instance public IP is ${PUB_IP}. Fix Cloudflare A record (gray cloud) then rerun."
  fi

  echo "== Run Certbot (no prompts) =="
  # --redirect enables HTTP->HTTPS redirect
  # --non-interactive prevents prompts
  # --agree-tos required for non-interactive mode
  # --email for registration
  certbot --nginx \
    -d "${DOMAIN}" \
    --non-interactive \
    --agree-tos \
    --email "${CERTBOT_EMAIL}" \
    --redirect

  echo "== Verify Certbot renew timer =="
  systemctl list-timers | grep certbot || true
  certbot renew --dry-run || true

  echo "== Reload Nginx after HTTPS =="
  nginx -t
  systemctl reload nginx
else
  echo "== Skipping Certbot (per --no-certbot) =="
fi

echo
echo "DONE."
echo "Notes:"
echo "- If you changed APP_USER group membership (www-data), SSH out and SSH back in once."
echo "- Cloudflare should be gray-cloud during Certbot. After HTTPS works, you can enable orange cloud."
