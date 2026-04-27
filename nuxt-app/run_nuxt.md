cd /path/to/anduongliving/nuxt-app
pnpm install --frozen-lockfile
NODE_OPTIONS=--max-old-space-size=4096 pnpm exec nuxt build --dotenv .env.production
pm2 delete anduongliving || true
pm2 start ecosystem.config.cjs --env production --update-env
pm2 save
pm2 status
pm2 logs anduongliving --lines 100
