# Shortcode Design Rules

## Mục tiêu
Tài liệu này quy định rule redesign shortcode cho Nuxt frontend đang render từ Botble/Laravel Blade.
Mục tiêu là làm UI đẹp hơn, có tính editorial và phá cách hơn, nhưng không vỡ flow CMS, không đổi shape dữ liệu, không phá Blade spec.

## Nguyên tắc bắt buộc
- Giữ nguyên contract dữ liệu hiện tại của shortcode: parser, registry, type, raw HTML mapping phải tiếp tục tương thích với Blade output.
- Không sửa tên class Blade gốc chỉ để phục vụ visual nếu việc đó làm parser mong manh hơn.
- Không đưa logic layout vào content editor. UI phải được giải quyết ở component Nuxt.
- Khi parser không lấy đủ data, component phải fallback về sanitized raw HTML thay vì render nửa vời.
- Mỗi shortcode phải responsive trên desktop, tablet, mobile; không có layout chỉ đẹp ở 1 breakpoint.
- Ưu tiên native component Nuxt, nhưng vẫn tôn trọng thứ tự và ý nghĩa nội dung từ Blade spec.

## Visual direction
Cảm hứng chính phải đi theo 2 shortcode đang thành công nhất hiện tại:
- `HeroStory.vue`
- `AboutUs.vue`

Ngôn ngữ thiết kế cần kế thừa:
- Chữ nằm trên ảnh hoặc lớp nền media, không tách rời thành một khối text đứng cạnh quá an toàn.
- Giữa text và ảnh có một lớp kính mờ nhẹ: translucent panel, blur vừa phải, border sáng nhẹ, bóng đổ mềm.
- Có chiều sâu thị giác: overlay, gradient, shadow, layer, ảnh phụ, panel nổi, lệch lớp có chủ đích.
- Phá cách nhưng vẫn sang: tránh bố cục grid thương mại quá phổ thông.

## Surface và overlay rules
- Nếu text đặt trên ảnh, phải có một trong 2 lớp sau:
  - một panel kính mờ bán trong suốt
  - hoặc một gradient overlay tối ở vùng text
- Kính mờ chỉ dùng để tăng tách lớp, không được làm mất contrast.
- Không dùng blur quá tay đến mức text mờ hoặc như biến mất.
- Border của panel nên rất nhẹ, độ dày 1px, màu sáng trong suốt.
- Bo góc mềm, ưu tiên radius lớn hơn card thông thường.

## Typography rules
- Heading phải có tỷ lệ lớn, giống editorial hero: rõ cấp bậc, không bé và dày chật.
- Eyebrow/subtitle được phép uppercase, tracking rộng, có tính định hướng section.
- Body text không quá dài một dòng; cần có max-width hợp lý.
- Text trên media phải ưu tiên contrast trước hiệu ứng.
- Không dùng underline trang trí cho nav, card, CTA nếu không có lý do rõ ràng.

## Layout rules theo loại shortcode

### 1. Hero / story / intro / showcase lớn
Áp dụng cho shortcode có 1 câu chuyện chính, 1 media chính:
- Một ảnh nền lớn full-width hoặc gần full-width.
- Text nằm trên ảnh, không đặt quá xa media.
- Có panel kính mờ hoặc overlay tối để text đọc được.
- Được phép có ảnh phụ, card nổi, quote mark, thumb nhỏ lệch góc để tạo chiều sâu.
- Desktop có thể bất đối xứng, lệch trái/phải có chủ đích.
- Mobile phải thu gọn về 1 cột, panel vẫn dễ đọc.

### 2. Multi-item shortcode
Áp dụng cho services, rooms, featured lists, blog cards, gallery cards, amenity cards, testimonial cards, v.v.

#### Desktop
- Ưu tiên 4 item một hàng nếu là danh sách card.
- Nếu concept cần nhấn mạnh từng item, được phép dùng 1 item một hàng theo dạng showcase ngang.
- Không dùng 2 item một hàng nếu nó làm bố cục trống quá, trừ khi shortcode đó bản chất là split layout.

#### Mobile
- Ưu tiên hiển thị 3 item trong một viewport theo dạng horizontal rail/carousel.
- User phải có cách để xem item tiếp theo:
  - swipe ngang
  - snap scroll
  - loop/rotate nhẹ nếu hợp lý
- Không ép card thành quá nhỏ đến mức text không đọc được.
- Nếu 3 item một viewport làm card vỡ quá nhiều, cho phép 1.2-1.5 item visible nhưng vẫn giữ tinh thần rail có thể lướt.

#### Interaction
- Item cần có ít nhất 1 trong các cách để xem thêm:
  - hover reveal trên desktop
  - click/tap mở detail, modal, page detail, hoặc internal link
  - drag/swipe để xem item tiếp theo
- Hover không được là cách duy nhất; mobile phải tap được.
- Nếu item có link detail, toàn bộ card nên có hit area rõ ràng.

## Motion rules
- Motion phải nhẹ, có ý nghĩa.
- Được dùng:
  - fade in
  - rise up nhẹ
  - scale ảnh rất nhẹ
  - parallax nhẹ
  - carousel snap mềm
- Không dùng motion liên tục gây mệt mắt.
- Hover motion nhẹ, ưu tiên transform nhỏ và shadow, không rung lắc.

## CTA rules
- CTA được phép nổi trên panel kính mờ hoặc nằm ở dưới content.
- Dạng ưu tiên: pill, radius lớn, background contrast rõ, shadow nhẹ.
- CTA không được tranh spotlight với heading.
- Nếu Blade không có button thì không tự thêm CTA giả.

## Blade spec compatibility rules
Đây là phần bắt buộc khi giao tasks:
- Không đổi tên shortcode name trong registry nếu không có lý do parser.
- Không đổi structure `parser -> type -> component` nếu không cần thiết.
- Mọi redesign phải giữ nguyên:
  - tên parser
  - output type fields
  - logic fallback raw HTML
  - mapping asset/link đang dùng
- Không sửa raw HTML trong CMS để chữa UI frontend.
- Nếu cần detect shortcode, ưu tiên root block class thay vì class item con.
- Không hardcode content mẫu trong component.
- Nếu Blade output có nested structure phức tạp, parser phải bám tag/class thật sự tồn tại trong raw.

## Accessibility rules
- Text trên ảnh phải đạt contrast thực tế, không chỉ đẹp trong screenshot.
- Nút, card, dot, rail phải bấm được trên mobile.
- Arrow nếu không cần thiết thì bỏ; dot và swipe là đủ.
- Alt text, aria-label, focus state không được mất trong quá trình redesign.

## Responsive rules chi tiết
- Desktop >= 1280px: layout có thể thoáng, lệch lớp, showcase rộng.
- Tablet 768px - 1279px: giảm lớp chồng, giảm ảnh phụ, tăng độ đọc.
- Mobile < 768px:
  - ưu tiên 1 cột cho hero/showcase lớn
  - multi-item chuyển sang rail/carousel
  - panel text vẫn phải dễ đọc, không nhỏ hơn mức cần thiết
  - không để text nằm sát mép màn hình

## Responsive acceptance criteria
Mỗi shortcode khi bàn giao phải được nghiệm thu tối thiểu ở 3 viewport:
- `1440px`
- `1024px`
- `390px`

Bắt buộc đạt:
- Không có overflow ngang.
- Không có text bị che, bị cắt hoặc chạm mép khó đọc.
- Không có hit area quá nhỏ trên mobile.
- Không có layout chỉ đẹp ở desktop nhưng vỡ ở tablet/mobile.

## Mục cấm
- Cấm UI phẳng, chỉ có card trắng + bóng đổ mặc định.
- Cấm grid thương mại lặp lại không có nhạc điệu.
- Cấm blur mạnh làm mất text.
- Cấm sửa parser theo kiểu bắt đầu bằng regex mong manh nếu có thể bám root block rõ ràng hơn.
- Cấm chỉ optimize desktop mà bỏ qua mobile.
- Cấm phá vỡ Blade spec để đổi layout nhanh.

## Definition of done cho mỗi shortcode redesign
Một shortcode chỉ được coi là xong khi đạt đủ các điều kiện sau:
- Dùng dữ liệu từ Blade/CMS hiện tại.
- Render đúng ở desktop, tablet, mobile.
- Nếu là hero/showcase: text có lớp kính mờ hoặc overlay rõ ràng, đọc được.
- Nếu là multi-item: desktop theo 4 item hoặc 1 item showcase; mobile có khả năng lướt/xem thêm.
- Hover/click/tap đều hợp lý, không có dead zone.
- Không phát sinh hydration mismatch, layout jump, overflow ngang, hoặc z-index che nội dung.
- Nếu parser không đủ data, fallback raw HTML vẫn hoạt động.

## Checklist giao việc
Khi giao redesign cho người khác, yêu cầu họ nộp lại:
- Danh sách file đã sửa
- Ảnh desktop + mobile của từng shortcode
- Giải thích ngắn về cách giữ Blade spec
- Giải thích parser có thay đổi hay không
- Xác nhận fallback raw HTML còn hoạt động
- Xác nhận multi-item mobile có swipe/scroll/click được

## Phạm vi sửa file khi làm UI
Trong đa số trường hợp, nếu chỉ đổi UI của một shortcode đã có data đúng và parser đúng, chỉ cần sửa đúng 1 file component shortcode tương ứng, ví dụ:
- `app/components/shortcode/HeroStory.vue`
- `app/components/shortcode/AboutUs.vue`
- `app/components/shortcode/SimpleSlider.vue`

Tuy nhiên, **không phải lúc nào cũng chỉ 1 file**. Người làm phải kiểm tra 3 trường hợp sau:
- Nếu dữ liệu parse ra sai hoặc thiếu: phải sửa thêm parser trong `app/utils/shortcode/parsers.ts`.
- Nếu shortcode chưa được detect đúng: phải sửa thêm `app/utils/shortcode/registry.ts` hoặc `types.ts`.
- Nếu visual phụ thuộc token/global layout dùng chung: có thể phải sửa thêm CSS shared, nhưng chỉ khi thật sự cần thiết.

Rule giao việc:
- Nếu chỉ đổi bố cục, màu, khoảng cách, overlay, card, interaction của 1 shortcode -> ưu tiên sửa đúng 1 file shortcode đó.
- Nếu đụng parser hoặc registry -> phải ghi rõ lý do trong phần bàn giao.
- Không sửa lan ra nhiều file nếu chưa chứng minh được shortcode đó không thể xử lý gọn trong component riêng.

## Danh sách shortcode để giao task và đánh dấu tiến độ

### Chuẩn tham chiếu đã làm tốt
- [x] `HeroStory.vue`
- [x] `AboutUs.vue`

### Ưu tiên cao - đang có lỗi hoặc cần làm lại sớm
- [ ] `SimpleSlider.vue`
- [x] `CuisineShowcase.vue`
- [x] `ForestFacilityShowcase.vue`
- [x] `BlogPosts.vue`
- [ ] `ServiceList.vue`
- [ ] `Pricing.vue`
- [ ] `Services.vue`

### Danh sách shortcode còn lại để QA / redesign nếu cần
- [ ] `AllRooms.vue`
- [ ] `BookingForm.vue`
- [ ] `Brands.vue`
- [ ] `CheckAvailabilityForm.vue`
- [ ] `Faqs.vue`
- [ ] `FeatureArea.vue`
- [ ] `FeaturedAmenities.vue`
- [ ] `FeaturedRooms.vue`
- [ ] `Galleries.vue`
- [ ] `HeroBannerWithBookingForm.vue`
- [ ] `HotelPlaces.vue`
- [ ] `HotelServices.vue`
- [ ] `IntroVideo.vue`
- [ ] `LocationTourismShowcase.vue`
- [ ] `LogoShowcaseBanner.vue`
- [ ] `News.vue`
- [ ] `Newsletter.vue`
- [ ] `OnsenSpaGallery.vue`
- [ ] `PickupGalleryShowcase.vue`
- [ ] `RoomList.vue`
- [ ] `RoomMosaicShowcase.vue`
- [ ] `SpaCollageShowcase.vue`
- [ ] `SpecialStoryShowcase.vue`
- [ ] `Teams.vue`
- [ ] `Testimonials.vue`
- [ ] `UserProfile.vue`
- [ ] `WhyChooseUs.vue`

## Cách đánh dấu khi giao việc
- Đánh dấu `[ ]` -> chưa làm.
- Đánh dấu `[x]` -> đã làm xong và đã qua QA ở `1440px`, `1024px`, `390px`.
- Nếu shortcode phải sửa thêm parser/registry, ghi thêm note ngay dưới mục đó:
  - `Parser changed`
  - `Registry changed`
  - `Fallback verified`
