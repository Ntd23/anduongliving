# nuxt-app

Nuxt frontend này dùng kiến trúc:

- `Feature-first modular monolith`
- có `CMS BFF layer` ở phía Nuxt server

Ý nghĩa thực tế:

- `app/pages` và `app/layouts` chỉ là entrypoint mỏng
- UI và data flow được gom theo domain:
  - `features/cms`
  - `features/auth`
  - `features/blog`
  - `features/navigation`
  - `features/shortcodes`
- `server/` đóng vai trò BFF để tích hợp với CMS backend
- `shared/` chỉ giữ contract, route builders, constants và helper thuần

## Cấu trúc chính

```txt
app/
  features/
    auth/
    blog/
    cms/
    navigation/
    shortcodes/
  pages/
  layouts/
  components/        # compatibility wrappers + shared primitives
  composables/       # compatibility wrappers + generic hooks

server/
  api/               # browser-facing handlers
  routes/            # browser-facing proxy bridge
  features/
    auth/
    cms/
    http/

shared/
  cms/
  config/
  routes/
  utils/
```

## Rule kiến trúc

- component không gọi backend trực tiếp
- composable không trộn fetch + parse + layout decision
- `server/api` và `server/routes` không chứa UI logic
- route public và `/cms-proxy/*` là contract ổn định
- `shared/cms-routing.ts` chỉ còn là compatibility barrel; code mới ưu tiên import từ:
  - `shared/routes/*`
  - `shared/cms/*`
  - `shared/utils/*`

## Development

```bash
npm install
npm run dev
```

Local dev thường dùng:

- Nuxt: `http://127.0.0.1:3000`
- site proxy: `http://anduongliving.test`
- backend app: `http://127.0.0.1:8000`

## Build

```bash
npm run build
```
