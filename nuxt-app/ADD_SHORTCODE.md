# Thêm shortcode mới trong Nuxt

Tài liệu này mô tả flow shortcode sau khi đã refactor sang kiến trúc `feature-first modular monolith`.

## Flow hiện tại

1. `usePage()` trong `app/features/cms/data/usePage.ts` fetch page content
2. `parseShortcodeBlocks()` trong `app/features/shortcodes/core/blocks.ts` tách content thành `ShortcodeBlock[]`
3. `detectShortcodeName()` trong `app/features/shortcodes/core/registry.ts` nhận diện block
4. `resolveShortcodeComponent()` trong `app/features/shortcodes/render/component-registry.ts` map block -> Vue component
5. component native trong `app/features/shortcodes/components/*.vue` render UI
6. nếu parser không đủ data hoặc block chưa native hóa thì rơi vào `Fallback.vue`

## Cấu trúc cần biết

### Core

- `app/features/shortcodes/core/types.ts`
- `app/features/shortcodes/core/core.ts`
- `app/features/shortcodes/core/blocks.ts`
- `app/features/shortcodes/core/parsers.ts`
- `app/features/shortcodes/core/registry.ts`

### Render

- `app/features/shortcodes/render/component-registry.ts`
- `app/features/shortcodes/components/*.vue`

### Compatibility

- `app/utils/shortcode/*`
- `app/components/shortcode/*`

Hai nhóm này hiện chỉ là compatibility wrapper. Code mới ưu tiên import từ `app/features/shortcodes/*`.

## Quy tắc thêm shortcode

Thứ tự chuẩn:

1. lấy HTML output thật từ backend
2. xác định root block ổn định để detect
3. thêm type nếu cần
4. viết parser
5. thêm entry vào registry
6. tạo component native
7. build và test trên page thật

## Rule detect

- ưu tiên detect bằng root class cấp block
- không detect bằng class item con nếu class đó có thể tái sử dụng ở block khác
- nếu bắt buộc detect bằng item class thì phải thêm điều kiện phụ trong registry để tránh match nhầm

## Rule parser

- parser chỉ lo data
- không render UI trong parser
- field thiếu phải trả default an toàn
- parser nên bó vào root section trước khi đọc item con

## Rule component

- component chỉ nhận `block`
- parser nằm ở `computed`
- UI native là đường chính
- fallback `v-html` chỉ là đường phụ khi parser chưa đủ mạnh

## Khi nào chỉ cần sửa 1 file component

Chỉ sửa 1 file component khi:

- shortcode đã map đúng sang component native
- chỉ đổi layout, spacing, màu, typography, card, CTA

Phải sửa thêm parser hoặc registry khi:

- block đang rơi vào fallback
- detect sai block
- parser không lấy đủ data
- HTML backend đổi structure
