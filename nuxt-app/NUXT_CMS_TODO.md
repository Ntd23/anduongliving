# Nuxt CMS status - clone Botble Blade

Tai lieu nay chot trang thai hien tai giua `Botble Blade` va `Nuxt`.

Nguyen tac:

- `Botble Blade` la spec
- `Admin hien co` la source of truth
- `Nuxt` clone renderer va flow, khong tu mo them source config moi neu admin hien tai khong dung

---

## 1. Muc tieu da xong

### Menu

- [x] `package menu` la source of truth cho Nuxt
- [x] co flow canonical cho `main-menu`
- [x] Nuxt fetch `main-menu` theo locale
- [x] `main-menu` render dung theo layout:
  - `default` -> header ngang
  - `cms-full-menu` -> header ngang full-width
  - `cms-side-menu` -> sidebar trai dang doc
- [x] `cms-side-menu` lay logo that / social links that tu theme options
- [x] doi ngon ngu theo SSR se lay lai `main-menu` dung locale
- [x] `menu-mobile-collapse` da co flow Nuxt rieng cho mobile
- [x] `SidebarMenuDrawer` da co flow Nuxt cho offcanvas / drawer

### Page / template / SEO

- [x] Nuxt hieu cac template admin dang chon:
  - `default`
  - `side-menu`
  - `full-menu`
  - `full-width`
  - `blog-sidebar`
- [x] mapping template di qua `app/utils/page-template.ts`
- [x] homepage canonical chay qua `homepage_id`
- [x] `/`, `/en`, `/ja` khong con redirect cung sang slug ky thuat
- [x] SEO page/homepage da co flow backend + Nuxt rieng
- [x] da gom config/path/url CMS ve `shared/cms-routing.ts`
- [x] `header-top` co flow Nuxt co ban
- [x] header button (`header_button_label` / `header_button_url`) da di qua theme options
- [x] `breadcrumbs` da co component Nuxt rieng
- [x] `footer` da co flow Nuxt rieng va dung `footer_sidebar`

### Shortcode

- [x] shortcode da parse qua registry/parser architecture
- [x] naming shortcode da chuyen ve canonical name theo Blade
- [x] Blade shortcode wrappers mo ho da duoc them `shortcode-*` class rieng
- [x] Nuxt shortcode registry da cover du `34/34` shortcode dang register trong Blade
- [x] tat ca shortcode Blade da co entry trong:
  - `app/utils/shortcode/types.ts`
  - `app/utils/shortcode/registry.ts`
  - `app/components/shortcode/*`
- [x] da tach rieng cac block truoc day dung chung renderer:
  - `featured-rooms`
  - `room-list`
  - `all-rooms`
  - `services`
  - `feature-area`

### Blog / sidebar widgets

- [x] `blog_sidebar` da noi that tu backend sang Nuxt
- [x] sidebar khong con `v-html` nguyen khoi nua; render theo manifest widget
- [x] backend widget API tra `items` theo dung `position`, co `rendered_html` fallback
- [x] co route blog toi thieu phia Nuxt:
  - `/blog`
  - `/blog/search`
  - `/blog/category/[slug]`
  - `/blog/tag/[slug]`
  - `/blog/[slug]`
- [x] blog API da ho tro locale theo flow hien tai
- [x] pagination co ban da co cho `/blog`, category, tag, search
- [x] blog archive, search shell, blog detail, native sidebar widgets da duoc re-skin theo visual system moi

### Native widgets da xong

- [x] `BlogSearchWidget`
- [x] `BlogPostsWidget`
- [x] `BlogCategoriesWidget`
- [x] `BlogTagsWidget`
- [x] `NewsletterWidget`
- [x] `CheckAvailabilityForm`
- [x] `CustomMenuWidget`
- [x] `ContactInformationMenuWidget`
- [x] `BlogSocialsWidget`
- [x] `RoomContactWidget`
- [x] `CoreSimpleMenu`
- [x] `Text`

### Footer widgets

- [x] `footer_sidebar` da render theo tung widget
- [x] `CustomMenuWidget` da native
- [x] `ContactInformationMenuWidget` da native
- [x] `NewsletterWidget` da native
- [x] `CoreSimpleMenu` da native
- [x] `Text` da native
- [x] `BlogSocialsWidget` da native
- [x] `RoomContactWidget` da native

---

## 2. Trang thai tong quan

### Shortcode parity

- `100%`

### CMS clone tong the tren Nuxt

- khoang `90-92%`

Phan con lai khong nam o data flow chinh nua. Chu yeu la:

- QA toan site
- polish UI/fidelity
- mot so block header/account/currency neu muon clone sat Blade hon

---

## 3. Phan con lai chua xong

### QA toan site

- [ ] ra soat tat ca page that theo template:
  - `default`
  - `full-menu`
  - `side-menu`
  - `full-width`
  - `blog-sidebar`
- [ ] test `vi / en / ja`
- [ ] test desktop + mobile
- [ ] bat cac loi edge case khi admin doi du lieu manh:
  - page content
  - menu nodes
  - widget data
  - shortcode attributes

### Header fidelity theo Blade

- [ ] `header-top` van chua co `currency switcher`
- [ ] `header-top` van chua co `account/customer block`
- [ ] `side-menu` van chua co du mobile/account parity nhu Blade

Luu y:
- cac muc nay khong phai bug data flow
- day la phan fidelity / feature parity them
- neu admin/site hien tai khong co source public cho account/currency thi can quyet dinh ro co clone tiep hay khong

### UI polish

- [ ] can mot pass polish cuoi de canh spacing, typography, image treatment tren cac page that
- [ ] can doi chieu lai voi cac page Blade quan trong neu muon fidelity cao hon

### Kien truc bo sung (optional, khong con blocker)

- [ ] neu muon bo han detect theo class HTML, co the mo rong page API de tra `shortcode_blocks` manifest canonical
- [ ] muc nay la hardening, khong con blocker cho viec render shortcode hien tai

---

## 4. Ket luan ngan

### Da xong

- menu flow chinh
- page template flow
- homepage canonical
- SEO page/homepage/blog co ban
- blog routes va blog sidebar flow
- footer flow
- shortcode parity theo Blade
- native hoa tat ca widget chinh dang dung

### Chua xong

- QA pass toan site
- header/account/currency fidelity
- side-menu mobile/account parity
- pass polish UI cuoi

### Viec nen lam tiep ngay

1. QA pass theo route that + locale + mobile
2. fix tung loi page that phat hien trong QA
3. neu can fidelity cao hon nua, lam them header account/currency va side-menu parity
