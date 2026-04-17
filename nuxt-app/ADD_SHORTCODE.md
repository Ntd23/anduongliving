# Thêm Shortcode Mới Trong 10 Phút

Tài liệu này dùng để hướng dẫn nhanh cách thêm 1 shortcode CMS mới vào Nuxt theo flow hiện tại của project.

Mục tiêu:
- Không làm rơi nội dung khi Botble trả HTML
- Ưu tiên render bằng Vue component thật, không dùng `v-html` nếu block này sẽ được dùng lâu dài
- Hoàn thành theo thứ tự cố định để dễ bàn giao cho người khác

## 0. Hiểu flow hiện tại

1. URL động `/:slug` vào [app/pages/[slug].vue](./app/pages/[slug].vue)
2. Page gọi `usePage(slug, locale)` trong [app/composables/usePage.ts](./app/composables/usePage.ts)
3. `usePage.ts` fetch page content và tách thành từng block
4. Mỗi block được gán `name` bằng `detectShortcodeName()`
5. [app/components/shortcode/BlockRenderer.vue](./app/components/shortcode/BlockRenderer.vue) map `name` sang component Vue
6. Component shortcode parse HTML thành data và render lại bằng template Vue

Nếu chưa có component riêng, block sẽ rơi vào `Fallback.vue`.

## 1. Lấy mẫu HTML của shortcode

Làm đầu tiên:

1. Gọi API page từ Botble, ví dụ:
   - `http://anduongliving.test/api/v1/pages/about-us`
   - `http://anduongliving.test/api/v1/pages/team`
2. Tìm đoạn HTML của shortcode cần xử lý
3. Chọn 1 class ổn định để nhận diện block, ví dụ:
   - `about-area`
   - `feature-area2`
   - `team-area`
   - `skill-area`
   - `newslater-area`

Quy tắc:
- Chọn class mang tính "section root", không chọn class nhỏ bên trong
- Nếu có 2 style khác nhau, ưu tiên class root chung. Nếu cần, thêm cả 2 vào detect map

## 2. Khai báo nhận diện block trong parser

Sửa file: [app/composables/usePage.ts](./app/composables/usePage.ts)

Việc cần làm:

1. Mở `detectShortcodeName()`
2. Thêm mapping mới vào `mappings`

Ví dụ:

```ts
["testimonial-area", "testimonial"],
```

Ý nghĩa:
- HTML có `testimonial-area` sẽ được gán `name = "testimonial"`
- Sau đó `BlockRenderer.vue` mới biết cần gọi component nào

Lưu ý:
- Project này hỗ trợ cả 2 kiểu content:
  - có wrapper `<shortcode>...</shortcode>`
  - không có wrapper, chỉ là nhiều `<section>...</section>` nối nhau
- Không cần sửa parser nếu block mới chỉ là 1 `section` bình thường, chỉ cần detect đúng class

## 3. Thêm parser cho block mới

Sửa file: [app/utils/shortcode-content.ts](./app/utils/shortcode-content.ts)

Mục tiêu:
- Biết block HTML cần bóc ra field gì
- Trả về object rõ ràng để component Vue render

Thứ tự làm:

1. Định nghĩa type mới
2. Tạo hàm `parseXxxBlock(html: string)`
3. Tái sử dụng các helper có sẵn:
   - `extractBlocksByTag()`
   - `extractFirstBlockByClass()`
   - `extractFirstImage()`
   - `extractTextFromTag()`
   - `extractAttribute()`
   - `extractStyleValue()`

Ví dụ khung parser:

```ts
export type TestimonialItem = {
  name: string;
  content: string;
};

export type TestimonialSectionData = {
  title: string | null;
  items: TestimonialItem[];
};

export const parseTestimonialBlock = (html: string): TestimonialSectionData => {
  const section = extractFirstBlockByClass(html, "section", "testimonial-area") || html;
  const itemBlocks = extractBlocksByTag(section, "div", "single-testimonial");

  return {
    title: extractTextFromTag(section, "h2"),
    items: itemBlocks
      .map((itemBlock) => {
        const name = extractTextFromTag(itemBlock, "h4");
        const content = extractTextFromTag(itemBlock, "p");

        if (!name || !content) return null;

        return { name, content };
      })
      .filter(Boolean) as TestimonialItem[],
  };
};
```

Quy tắc parser:
- Parse ít field thật cần của block, đừng parse quá sâu nếu chưa cần
- Nếu field nào không có, trả `null` hoặc mảng rỗng
- Ưu tiên output đơn giản, dễ để template Vue đọc

## 4. Tạo component shortcode mới

Tạo file mới trong: `app/components/shortcode/`

Đặt tên theo mẫu:
- `About.vue`
- `Feature.vue`
- `Team.vue`
- `Skill.vue`
- `Newsletter.vue`

Ví dụ block mới:
- `Testimonial.vue`

Khung component:

```vue
<script setup lang="ts">
import { parseTestimonialBlock } from "~/utils/shortcode-content";
import type { ShortcodeBlock } from "~/composables/usePage";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseTestimonialBlock(props.block.raw));
</script>

<template>
  <section v-if="section.items.length" class="shortcode-testimonial">
    <div class="container">
      <h2 v-if="section.title">{{ section.title }}</h2>

      <article v-for="item in section.items" :key="item.name">
        <h3>{{ item.name }}</h3>
        <p>{{ item.content }}</p>
      </article>
    </div>
  </section>

  <section v-else class="shortcode-testimonial">
    <div v-html="block.raw" />
  </section>
</template>
```

Nguyên tắc quan trọng:
- `v-if` để render layout Vue khi parse đủ data
- Có fallback `v-html` khi parse thất bại, tránh làm mất nội dung CMS
- Nếu block cần style riêng, viết `style scoped` ngay trong component trước

## 5. Đăng ký component vào renderer

Sửa file: [app/components/shortcode/BlockRenderer.vue](./app/components/shortcode/BlockRenderer.vue)

Việc cần làm:

1. Import component mới
2. Thêm vào `componentMap`

Ví dụ:

```ts
import ShortcodeTestimonial from "./Testimonial.vue";

const componentMap = {
  about: ShortcodeAbout,
  testimonial: ShortcodeTestimonial,
} as const;
```

Sau bước này:
- Khi `detectShortcodeName()` trả về `"testimonial"`
- `BlockRenderer` sẽ gọi đúng `Testimonial.vue`

## 6. Khi nào chỉ cần Fallback, khi nào phải viết component thật

Chỉ cần `Fallback.vue` nếu:
- Block ít dùng
- Chưa cần style đẹp
- Chưa cần control interaction

Nên viết component thật nếu:
- Block hay dùng ở nhiều page
- Layout hiện tại bị vỡ khi render HTML raw
- Cần animation, submit form, state, icon, responsive, SSR ổn định

## 7. Thứ tự thao tác nhanh nhất

Đây là thứ tự nên làm, đừng bỏ qua:

1. Lấy HTML thật của block từ API page
2. Chọn class root ổn định
3. Thêm mapping vào `detectShortcodeName()` trong `usePage.ts`
4. Tạo parser mới trong `shortcode-content.ts`
5. Tạo component mới trong `app/components/shortcode/`
6. Đăng ký vào `BlockRenderer.vue`
7. Chạy build
8. Mở browser test page thật

## 8. Cách verify sau khi thêm

Chạy:

```bash
pnpm dev
```

Verify trên browser:

1. Page có render đúng thứ tự block không
2. Block mới có vào đúng component không
3. Responsive có vỡ layout không
4. Console có hydration mismatch không
5. Nếu block có form, submit có hoạt động không

## 9. Checklist copy-paste cho người mới

Mỗi lần thêm shortcode mới, đi theo checklist này:

- Lấy HTML từ Botble API
- Tìm class root của block
- Thêm detect map trong `app/composables/usePage.ts`
- Thêm parser trong `app/utils/shortcode-content.ts`
- Tạo component trong `app/components/shortcode/`
- Đăng ký vào `app/components/shortcode/BlockRenderer.vue`
- Build lại bằng `npm run build`
- Test trên page thật

## 10. 3 lỗi thường gặp

### Lỗi 1: Page chỉ hiện tiêu đề, block không ra

Nguyên nhân thường gặp:
- `BlockRenderer.vue` chưa map component mới
- `detectShortcodeName()` không nhận ra class root

### Cách check

1. In `blocks` trong `[slug].vue`
2. Xem `block.name` có đúng không
3. Xem `componentMap` có key tương ứng không

### Lỗi 2: Block rơi vào fallback dù đã có component

Nguyên nhân thường gặp:
- HTML không chứa class mà bạn đang detect
- Class root của page thật khác với page mẫu

### Cách check

1. Copy raw HTML từ API
2. Search class root thật sự
3. Thêm alias trong `detectShortcodeName()` nếu cần

### Lỗi 3: Layout xấu hoặc vỡ responsive

Nguyên nhân thường gặp:
- Vẫn đang render `v-html`
- Chưa viết structure Vue đúng theo theme gốc

### Cách check

1. Mở blade theme gốc trong `platform/themes/riorelax/partials/shortcodes/`
2. Mở CSS theme gốc trong `platform/themes/riorelax/assets/sass/components/`
3. Render lại bằng Vue, không copy HTML nguyên khối nếu không cần

## 11. File thường dùng nhất

- [app/composables/usePage.ts](./app/composables/usePage.ts)
- [app/utils/shortcode-content.ts](./app/utils/shortcode-content.ts)
- [app/components/shortcode/BlockRenderer.vue](./app/components/shortcode/BlockRenderer.vue)
- `app/components/shortcode/*.vue`
- `platform/themes/riorelax/partials/shortcodes/`
- `platform/themes/riorelax/assets/sass/components/`

## 12. Câu nói ngắn để handoff

Nếu cần hướng dẫn người khác rất nhanh, chỉ cần nói:

"Lấy HTML block từ API, detect class root trong `usePage.ts`, viết parser trong `shortcode-content.ts`, tạo component trong `app/components/shortcode/`, đăng ký vào `BlockRenderer.vue`, xong build và test page thật."
