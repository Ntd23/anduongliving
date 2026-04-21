#!/usr/bin/env bash
set -euo pipefail

DEPLOY_PATH="${DEPLOY_PATH:-/home/anduongliving/domains/demo.anduongliving.com/main}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-main}"
NITRO_APP_NAME="${NITRO_APP_NAME:-anduongliving-nuxt}"
NITRO_DIR="${NITRO_DIR:-$DEPLOY_PATH/nuxt-app}"

echo "Deploy path: $DEPLOY_PATH"
echo "Deploy branch: $DEPLOY_BRANCH"

cd "$DEPLOY_PATH"

git fetch origin "$DEPLOY_BRANCH"
git checkout "$DEPLOY_BRANCH"
git pull --ff-only origin "$DEPLOY_BRANCH"

if command -v composer >/dev/null 2>&1; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

php artisan optimize:clear

cd "$NITRO_DIR"

if command -v pnpm >/dev/null 2>&1; then
  if [ -f pnpm-lock.yaml ]; then
    pnpm install --frozen-lockfile
  else
    pnpm install
  fi
else
  npm install
fi

export NODE_OPTIONS="${NODE_OPTIONS:---max-old-space-size=4096}"

# Clean Nuxt build artifacts to avoid stale manifest/cache errors on server.
rm -rf .nuxt .output node_modules/.cache/nuxt

if command -v pnpm >/dev/null 2>&1; then
  pnpm build
else
  npm run build
fi

pm2 restart "$NITRO_APP_NAME" --update-env
pm2 save

cd "$DEPLOY_PATH"
php artisan optimize:clear

echo "Deploy completed."
