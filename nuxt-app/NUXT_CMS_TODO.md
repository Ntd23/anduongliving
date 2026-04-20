# Nuxt CMS - tráº¡ng thÃ¡i clone Botble Blade

TÃ i liá»‡u nÃ y chá»‘t tráº¡ng thÃ¡i hiá»‡n táº¡i giá»¯a `Botble Blade` vÃ  `Nuxt`.

NguyÃªn táº¯c Ä‘ang dÃ¹ng:

- `Botble Blade` lÃ  spec
- `Admin hiá»‡n cÃ³` lÃ  source of truth
- `Nuxt` chá»‰ clone renderer/flow, khÃ´ng tá»± má»Ÿ thÃªm source config trong admin náº¿u site hiá»‡n táº¡i khÃ´ng dÃ¹ng tháº­t

---

## 1. Menu

### ÄÃ£ xong

- [x] `package menu` lÃ  source of truth cho Nuxt
- [x] CÃ³ flow canonical cho `main-menu`
- [x] Nuxt fetch `main-menu` theo locale
- [x] `main-menu` render Ä‘Ãºng nguá»“n dá»¯ liá»‡u theo layout:
  - `default` -> header ngang
  - `cms-full-menu` -> header ngang full-width
  - `cms-side-menu` -> sidebar trÃ¡i dáº¡ng dá»c
- [x] `cms-side-menu` láº¥y logo tháº­t / social links tháº­t tá»« theme options
- [x] Äá»•i ngÃ´n ngá»¯ theo SSR sáº½ láº¥y láº¡i `main-menu` Ä‘Ãºng locale

### ChÆ°a xong

- [x] `menu-mobile-collapse` Ä‘Ã£ cÃ³ flow Nuxt riÃªng cho mobile, dÃ¹ng `main-menu` + language switcher trong Ä‘Ãºng mobile header shell
- [ ] `offcanvas-menu` cá»§a theme chÆ°a cÃ³ flow Nuxt tÆ°Æ¡ng Ä‘Æ°Æ¡ng mÃ  khÃ´ng sá»­a thÃªm admin/core
- [x] `CustomMenuWidget` vÃ  `ContactInformationMenuWidget` á»Ÿ `footer_sidebar` Ä‘Ã£ Ä‘i qua renderer Nuxt riÃªng; cÃ¡c widget footer khÃ¡c váº«n fallback HTML

### Káº¿t luáº­n

- `main-menu` Ä‘Ã£ xong pháº§n data flow chÃ­nh
- pháº§n fidelity theo Blade cho mobile/offcanvas váº«n chÆ°a xong

---

## 2. Page / template

### ÄÃ£ xong

- [x] Nuxt hiá»ƒu cÃ¡c template admin Ä‘ang chá»n:
  - `default`
  - `side-menu`
  - `full-menu`
  - `full-width`
  - `blog-sidebar`
- [x] Mapping template Ä‘i qua `app/utils/page-template.ts`
- [x] Homepage canonical cháº¡y qua `homepage_id`
- [x] `/`, `/en`, `/ja` khÃ´ng cÃ²n redirect cá»©ng sang slug ká»¹ thuáº­t
- [x] SEO page/homepage Ä‘Ã£ cÃ³ flow backend + Nuxt riÃªng
- [x] ÄÃ£ gom config/path/url CMS vá» `shared/cms-routing.ts` Ä‘á»ƒ trÃ¡nh hard-code ráº£i rÃ¡c vÃ  sá»­a dá»©t Ä‘iá»ƒm case client navigation gá»i sai origin `/api/cms/*`
- [x] `header-top` cÆ¡ báº£n Ä‘Ã£ Ä‘Æ°á»£c clone á»Ÿ cÃ¡c layout dÃ¹ng header ngang
- [x] Header button (`header_button_label` / `header_button_url`) Ä‘Ã£ Ä‘i qua theme options vÃ  render á»Ÿ header ngang non-full-width
- [x] ÄÃ£ bá» `LanguageSwitcher` floating khá»i app root, chuyá»ƒn vá» flow gáº§n Blade hÆ¡n
- [x] `breadcrumbs` Ä‘Ã£ cÃ³ component Nuxt riÃªng, Ä‘á»c:
  - `page metadata` (`breadcrumb`, `breadcrumb_background`)
  - `theme options` fallback (`breadcrumb_background_image`)
- [x] `footer` Ä‘Ã£ cÃ³ flow Nuxt riÃªng, Ä‘á»c:
  - `theme options` (`background_footer`)
  - widget area `footer_sidebar`

### ChÆ°a xong

- [ ] `full-menu` váº«n thiáº¿u offcanvas/menu mobile parity
- [ ] `side-menu` váº«n thiáº¿u mobile collapse + account block cá»§a Blade
- [x] Homepage sticky bottom menu behavior cá»§a Blade Ä‘Ã£ clone theo theme hiá»‡n táº¡i

---

## 3. Shortcode

### ÄÃ£ xong

- [x] Shortcode Ä‘Ã£ parse qua registry/parser architecture
- [x] CÃ¡c shortcode chÃ­nh Ä‘Ã£ cÃ³ component Nuxt riÃªng
- [x] CÃ³ thá»ƒ chá»‰nh UI shortcode trá»±c tiáº¿p bÃªn Nuxt mÃ  khÃ´ng cáº§n sá»­a HTML admin
- [x] `Newsletter` shortcode Ä‘Ã£ dÃ¹ng composable submit chung, khÃ´ng cÃ²n nhÃ©t logic submit riÃªng trong component

### CÃ²n pháº£i lÃ m

- [ ] Tiáº¿p tá»¥c chuyá»ƒn cÃ¡c shortcode cÃ²n thiáº¿u sang component Nuxt riÃªng náº¿u muá»‘n UI á»•n Ä‘á»‹nh hÆ¡n
- [ ] Giáº£m thÃªm fallback HTML backend cho cÃ¡c block quan trá»ng

---

## 4. Blog vÃ  `blog_sidebar`

### ÄÃ£ xong

- [x] `blog_sidebar` Ä‘Ã£ ná»‘i tháº­t tá»« backend sang Nuxt
- [x] Sidebar khÃ´ng cÃ²n `v-html` nguyÃªn khá»‘i ná»¯a; giá» render theo tá»«ng widget manifest
- [x] Backend widget API tráº£ `items` theo Ä‘Ãºng `position`, cÃ³ `rendered_html` fallback cho widget chÆ°a native
- [x] 6 widget Ä‘Ã£ Nuxt-native hÃ³a:
  - `BlogSearchWidget`
  - `BlogPostsWidget`
  - `BlogCategoriesWidget`
  - `BlogTagsWidget`
  - `NewsletterWidget`
  - `CheckAvailabilityForm`
- [x] `CustomMenuWidget` trong pháº¡m vi `blog_sidebar` Ä‘Ã£ native
- [x] CÃ¡c widget chÆ°a native váº«n fallback qua `rendered_html`, giá»¯ Ä‘Ãºng thá»© tá»± admin
- [x] CÃ³ route blog tá»‘i thiá»ƒu phÃ­a Nuxt:
  - `/blog`
  - `/blog/search`
  - `/blog/category/[slug]`
  - `/blog/tag/[slug]`
  - `/blog/[slug]`
- [x] Blog API Ä‘Ã£ há»— trá»£ locale theo flow hiá»‡n táº¡i
- [x] ÄÃ£ sá»­a case locale máº·c Ä‘á»‹nh `vi` Ä‘á»ƒ khÃ´ng filter sai vÃ o báº£ng translations lÃ m `/blog` rá»—ng

### ChÆ°a xong / cÃ²n giá»›i háº¡n

- [ ] `CheckAvailabilityForm` má»›i native pháº§n UI, káº¿t quáº£ váº«n redirect sang flow room search hiá»‡n táº¡i cá»§a Botble
- [ ] `NewsletterWidget` váº«n táº­n dá»¥ng endpoint Botble, chÆ°a cÃ³ proxy/form contract riÃªng cá»§a Nuxt
- [x] ÄÃ£ cÃ³ pagination UI cÆ¡ báº£n cho `/blog`, category, tag, search pages
- [ ] ÄÃ£ kéo archive card/pagination, blog detail body/navigation/author vÃ  blog breadcrumbs hero shell vá» gáº§n Blade theme; cÃ²n thiáº¿u parity hoÃ n chá»‰nh cho search shell vÃ  má»™t sá»‘ chi tiáº¿t blog detail

---

## 5. Audit nhanh `blog_sidebar`

### ÄÃ£ native hÃ³a

- [x] `BlogSearchWidget`
- [x] `BlogPostsWidget`
- [x] `BlogCategoriesWidget`
- [x] `BlogTagsWidget`
- [x] `NewsletterWidget`
- [x] `CheckAvailabilityForm`
- [x] `CustomMenuWidget`

### CÃ²n fallback / cáº§n rÃ  thÃªm

- [x] `BlogSocialsWidget` -> fallback HTML, rá»§i ro tháº¥p
- [x] `RoomContactWidget` -> fallback HTML, rá»§i ro tháº¥p
- [x] `CoreSimpleMenu` -> fallback HTML, tÆ°Æ¡ng Ä‘á»‘i an toÃ n náº¿u chá»‰ lÃ  menu tÄ©nh
- [x] `ContactInformationMenuWidget` -> Ä‘Ã£ cÃ³ renderer Nuxt riÃªng cho footer/sidebar cÆ¡ báº£n
- [ ] `Text` -> an toÃ n náº¿u text tÄ©nh, khÃ´ng an toÃ n náº¿u admin nhÃ©t shortcode/form Ä‘á»™ng

---

## 6. Viá»‡c cáº§n lÃ m tiáº¿p

## Má»©c Æ°u tiÃªn 1 - clone tiáº¿p shell/layout cá»§a Blade

### 1. HoÃ n thiá»‡n header shell

- [x] ÄÃ£ cÃ³ `header-top` cÆ¡ báº£n cho Nuxt
- [ ] `header-top` váº«n thiáº¿u currency switcher
- [ ] `header-top` váº«n thiáº¿u account/customer block
- [x] `header` Ä‘Ã£ clone homepage bottom sticky behavior theo `platform/themes/riorelax/partials/header.blade.php`
- [x] `menu-mobile-collapse` Ä‘Ã£ Ä‘Æ°á»£c clone á»Ÿ má»©c `Menu + Languages + Contact`, khÃ´ng cÃ²n Ä‘á»ƒ language switcher trÃ´i ngoÃ i flow mobile
- [ ] Mobile shell váº«n chÆ°a cÃ³ `Currencies` vÃ  `Account` parity vÃ¬ Nuxt chÆ°a cÃ³ source/flow public tÆ°Æ¡ng á»©ng

### 2. HoÃ n thiá»‡n cÃ¡c layout cÃ²n thiáº¿u fidelity

- [x] ÄÃ£ cÃ³ `breadcrumbs` cho cÃ¡c layout Ä‘ang dÃ¹ng Blade breadcrumbs
- [x] ÄÃ£ cÃ³ `footer` / `footer_sidebar` cho cÃ¡c layout page thÆ°á»ng
- [x] ÄÃ£ rà spacing/container cơ bản cho `default`, `full-menu`, `blog-sidebar`, `side-menu` sau khi gắn breadcrumbs/footer

### 3. Má»Ÿ rá»™ng menu/widget ngoÃ i `blog_sidebar`

- [ ] `footer_sidebar` Ä‘Ã£ render theo tá»«ng widget, hiá»‡n native `CustomMenuWidget` + `ContactInformationMenuWidget`; cÃ¡c widget cÃ²n láº¡i váº«n fallback HTML
- [ ] Náº¿u cáº§n thá»‘ng nháº¥t kiáº¿n trÃºc hÆ¡n ná»¯a, cÃ¢n nháº¯c gom `CustomMenuWidget` sang shared renderer cho cáº£ sidebar/footer

## Má»©c Æ°u tiÃªn 2 - hoÃ n thiá»‡n blog UX

### 4. HoÃ n thiá»‡n blog pages

- [x] ÄÃ£ cÃ³ pagination cÆ¡ báº£n cho `/blog`, category, tag
- [x] Search UX cÆ¡ báº£n Ä‘Ã£ chuyá»ƒn sang flow paginated qua filters API
- [ ] SEO riÃªng hÆ¡n cho category/tag/search pages náº¿u cáº§n
- [ ] RÃ  route slug locale Ä‘á»ƒ Ä‘áº£m báº£o click tá»« widget luÃ´n Ä‘i Ä‘Ãºng Nuxt path mong muá»‘n

### 5. HoÃ n thiá»‡n `blog_sidebar`

- [x] Remap UI cÆ¡ báº£n cho `ContactInformationMenuWidget` á»Ÿ footer/sidebar
- [ ] RÃ  `CoreSimpleMenu` / `Text` náº¿u admin dÃ¹ng ná»™i dung Ä‘á»™ng

---

## 7. Thá»© tá»± lÃ m Ä‘á» xuáº¥t tá»« bÃ¢y giá»

1. Pagination + UX hoÃ n chá»‰nh cho blog pages
2. Native hÃ³a thÃªm cÃ¡c widget cÃ²n láº¡i trong `footer_sidebar` náº¿u muá»‘n bá» thÃªm fallback HTML
3. RÃ  láº¡i cÃ¡c widget fallback cÃ²n láº¡i (`Text`, `CoreSimpleMenu`, `BlogSocialsWidget`, `RoomContactWidget`)

---

## 8. Káº¿t luáº­n ngáº¯n

### Page

- kiáº¿n trÃºc dá»¯ liá»‡u Ä‘Ã£ khÃ¡ á»•n
- shell/layout Ä‘Ã£ tiáº¿n gáº§n Blade hÆ¡n nhá» `header-top`, `breadcrumbs`, `footer`
- cÃ²n thiáº¿u mobile header parity vÃ  layout fidelity đặc thù

### Main menu

- Ä‘Ã£ xong pháº§n data flow chÃ­nh
- chÆ°a xong pháº§n fidelity theo Blade cho mobile/offcanvas

### Blog / blog sidebar

- Ä‘Ã£ xong pháº§n kiáº¿n trÃºc chÃ­nh
- Ä‘Ã£ cÃ³ route Nuxt vÃ  nhÃ³m widget native quan trá»ng
- váº«n cÃ²n má»™t sá»‘ widget fallback HTML vÃ  pháº§n UX cáº§n polish thÃªm

### Viá»‡c nÃªn lÃ m tiáº¿p ngay

- Æ°u tiÃªn rÃ  láº¡i spacing/container, sau Ä‘Ã³ hoÃ n thiá»‡n blog UX vÃ  footer widget fidelity


