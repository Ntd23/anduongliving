# Thêm Shortcode Mới Trong Nuxt

Tài liệu này hướng dẫn cách thêm một shortcode CMS mới vào Nuxt theo kiến trúc hiện tại của project, sau khi đã refactor sang mô hình:

- `registry` trung tâm
- `parser` tách lớp
- `renderer` resolve component theo registry

Mục tiêu:

- Không làm mất nội dung khi Botble trả HTML
- Ưu tiên render bằng Vue component thật thay vì `v-html`
- Khi thêm shortcode mới chỉ cần chạm đúng 3 nơi chính:
  - parser
  - registry
  - component

## 0. Hiểu flow hiện tại

1. URL động `/:slug` đi vào `app/pages/[slug].vue`
2. Page gọi `usePage(slug, locale)` trong `app/composables/usePage.ts`
3. `usePage()` fetch page content rồi gọi `parseShortcodeBlocks()`
4. `parseShortcodeBlocks()` nằm trong `app/utils/shortcode/blocks.ts`
5. `detectShortcodeName()` nằm trong `app/utils/shortcode/registry.ts`
6. `BlockRenderer.vue` resolve component qua `app/components/shortcode/component-registry.ts`
7. Component shortcode gọi parser riêng và render UI
8. Nếu shortcode chưa có trong registry hoặc parse không đủ dữ liệu, block vẫn rơi về fallback HTML

## 1. Cấu trúc file quan trọng

Các file chính của hệ shortcode hiện tại:

- `app/utils/shortcode/types.ts`
- `app/utils/shortcode/core.ts`
- `app/utils/shortcode/parsers.ts`
- `app/utils/shortcode/registry.ts`
- `app/utils/shortcode/blocks.ts`
- `app/utils/shortcode/index.ts`
- `app/utils/shortcode-content.ts`  
  File này chỉ còn là lớp tương thích ngược, export lại từ `~/utils/shortcode`

Phần render:

- `app/components/shortcode/BlockRenderer.vue`
- `app/components/shortcode/component-registry.ts`
- `app/components/shortcode/*.vue`

## 2. Quy tắc thêm shortcode mới

Khi thêm một shortcode mới, đi đúng thứ tự này:

1. Lấy HTML thật từ API hoặc từ Botble shortcode output
2. Chọn class root ổn định để detect block
3. Viết parser trong `parsers.ts`
4. Thêm entry vào `registry.ts`
5. Tạo component Vue trong `app/components/shortcode/`
6. Chạy `npm run build`
7. Mở page thật để kiểm tra UI

## 3. Bước 1: Lấy HTML thật của block

Nguồn lấy HTML:

- API page của Botble, ví dụ:
  - `http://anduongliving.test/api/v1/pages/about-us`
  - `http://anduongliving.test/api/v1/pages/team`
- Hoặc Blade output bên theme:
  - `platform/themes/riorelax/partials/shortcodes/`

Khi xem HTML, cần xác định:

- class root của section
- các field quan trọng cần parse
- ảnh chính, ảnh phụ
- nút CTA
- các đoạn title / subtitle / description

Ví dụ root class tốt:

- `about-area`
- `feature-area2`
- `skill-area`
- `newslater-area`
- `team-area`
- `hero-story`

Nguyên tắc:

- Ưu tiên class root của cả block
- Không detect bằng class nhỏ ở sâu bên trong nếu có class root ổn định
- Nếu cùng một shortcode có nhiều biến thể HTML, thêm nhiều alias vào registry

## 4. Bước 2: Viết parser

Viết trong:

- `app/utils/shortcode/parsers.ts`

Các helper HTML chung đã có sẵn trong:

- `app/utils/shortcode/core.ts`

Ví dụ parser mới:

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

- Chỉ parse các field thật sự cần cho UI
- Field thiếu thì trả `null`, `[]`, hoặc giá trị mặc định an toàn
- Không đưa logic UI vào parser
- Không viết parser phụ thuộc vào component

## 5. Bước 3: Thêm vào registry

Sửa file:

- `app/utils/shortcode/registry.ts`

Mỗi shortcode phải có:

- `name`
- `aliases`
- `componentName`
- `parser`

Ví dụ:

```ts
{
  name: "testimonial",
  aliases: ["testimonial-area", "testimonial-slider"],
  componentName: "Testimonial",
  parser: parseTestimonialBlock,
}
```

Ý nghĩa:

- `detectShortcodeName()` sẽ nhận diện block dựa vào `aliases`
- `BlockRenderer.vue` sẽ resolve sang component theo `componentName`
- parser tương ứng sẽ là nguồn dữ liệu chuẩn cho UI

Lưu ý:

- Từ sau refactor, không thêm mapping trong `usePage.ts`
- Từ sau refactor, không sửa map tay trong `BlockRenderer.vue`
- Tất cả đi qua `registry.ts`

## 6. Bước 4: Tạo component Vue

Tạo file trong:

- `app/components/shortcode/`

Ví dụ:

- `Testimonial.vue`

Khung chuẩn:

```vue
<script setup lang="ts">
import { parseTestimonialBlock, type ShortcodeBlock } from "~/utils/shortcode";

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

Nguyên tắc component:

- Chỉ nhận `block`
- Tự parse `block.raw`
- Render Vue layout khi parse đủ dữ liệu
- Có fallback `v-html` để tránh mất nội dung CMS
- Không tự detect shortcode name trong component

## 7. Bước 5: Nếu shortcode có style narrative

Các block kiểu storytelling như:

- `About`
- `Feature`
- `Newsletter`
- `HeroStory`

đã dùng shared style tại:

- `app/assets/css/shortcode-narrative.css`

Nếu shortcode mới cùng nhóm này, ưu tiên dùng lại:

- `.shortcode-narrative-eyebrow`
- `.shortcode-narrative-title`

Không copy-paste lại cùng một rule font/letter-spacing ở nhiều component nếu không cần.

## 8. Cách verify sau khi thêm

Chạy:

```bash
npm run build
```

Sau đó mở page thật để kiểm tra:

1. block render đúng thứ tự admin đã sắp
2. block đi đúng component mới
3. fallback không làm mất nội dung nếu HTML thay đổi
4. responsive không vỡ layout
5. không có hydration mismatch hoặc lỗi console

## 9. 3 lỗi thường gặp

### Lỗi 1: Block vẫn rơi vào fallback

Nguyên nhân thường gặp:

- chưa thêm alias vào `registry.ts`
- alias không đúng class root thật
- component name trong registry không khớp tên file Vue

### Lỗi 2: Có detect được nhưng UI rỗng

Nguyên nhân thường gặp:

- parser đang bám sai tag
- HTML thật khác HTML mẫu
- đang parse quá cứng theo 1 structure cũ

### Lỗi 3: Build pass nhưng UI sai dữ liệu

Nguyên nhân thường gặp:

- parser lấy nhầm ảnh đầu tiên
- parse title/description từ block con sai
- alias đang match nhầm một block khác

## 10. Checklist ngắn cho người mới

Mỗi lần thêm shortcode mới:

- lấy HTML thật
- tìm class root
- viết parser trong `app/utils/shortcode/parsers.ts`
- thêm entry vào `app/utils/shortcode/registry.ts`
- tạo component trong `app/components/shortcode/`
- chạy `npm run build`
- test page thật

## 11. Handoff 1 câu

Nếu cần hướng dẫn nhanh cho người khác, chỉ cần nói:

“Lấy HTML block thật từ API hoặc Blade, viết parser trong `parsers.ts`, thêm entry vào `registry.ts`, tạo component trong `app/components/shortcode/`, rồi build và test page thật.”
