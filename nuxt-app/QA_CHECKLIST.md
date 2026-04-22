# Checklist QA Nuxt CMS

Tài liệu này dùng để test nhanh trước khi commit/push production.

## 1. Smoke test local

- [ ] Mở `http://anduongliving.test:3000`
- [ ] Homepage load xong, không treo, không `500`
- [ ] `GET /api/cms/pages/homepage?lang=vi_VN` trả `200`
- [ ] `GET /api/cms/theme/header-extras?lang=vi_VN` trả nhanh, không loop
- [ ] `npm run build` pass

## 2. Header / menu / account

- [ ] Top bar hiện đúng language switcher
- [ ] Currency switcher hiện đúng danh sách đơn vị
- [ ] Guest state hiện `Login / Register`
- [ ] Mobile menu hiện account block và currency block
- [ ] Side-menu template hiện đúng account/currency block
- [ ] Menu desktop không active sai
- [ ] Menu mobile không có text thừa, không bị overflow

## 3. Route chính

- [ ] `/`
- [ ] `/{slug}` cho page thường
- [ ] `/blog`
- [ ] `/blog/{slug}`
- [ ] `/blog/category/{slug}`
- [ ] `/blog/tag/{slug}`
- [ ] `/services/{slug}`
- [ ] Các route trên không có warning `No match found`

## 4. Shortcode / page content

- [ ] `SimpleSlider` render đúng ảnh + caption
- [ ] `CuisineShowcase` hiện đủ text
- [ ] `ForestFacilityShowcase` hiện đủ text
- [ ] `BlogPosts` pagination không vỡ UI
- [ ] Không còn khoảng trắng giả do `Fallback`

## 5. Asset / media

- [ ] Ảnh từ `/storage/...` load đúng
- [ ] Ảnh từ `/themes/...` load đúng
- [ ] Logo / favicon load đúng
- [ ] Không có `404` asset trong tab Network

## 6. Console / log

- [ ] Không còn `500` ở các route `/api/cms/*`
- [ ] Không còn warning i18n key thiếu quan trọng
- [ ] Không còn warning router cho route chính
- [ ] Không còn lỗi hydration nghiêm trọng

## 7. State cần test

### Guest

- [ ] Homepage
- [ ] Blog
- [ ] Service detail
- [ ] Header / mobile menu / side-menu

### Customer đã đăng nhập

- [ ] Top bar hiện account/avatar hoặc tên
- [ ] Có `Logout`
- [ ] Mobile menu hiện đúng state customer
- [ ] Side-menu hiện đúng state customer

## 8. Known behavior / known gap

- `Login / Register` hiện vẫn đi vào UI Blade của Laravel nếu chưa có Nuxt auth pages riêng.
- Nếu đổi currency mà giá vẫn giữ USD, cần kiểm tra flow session/cookie từ Laravel sang các request `/api/cms/*`.
