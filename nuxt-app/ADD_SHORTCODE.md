# Them Shortcode Moi Trong Nuxt

Tai lieu nay mo ta flow hien tai cua he shortcode va cach them hoac sua shortcode moi ma khong pha vo flow Botble.

## Muc tieu

- Uu tien render bang Vue component that thay vi de HTML roi vao `Fallback.vue`
- Khong mat noi dung khi CMS tra block HTML la
- Detect shortcode theo root block on dinh, khong detect bang item class con neu co the tranh duoc
- Khi sua UI, biet ro luc nao chi sua 1 component va luc nao phai sua parser/registry

## Flow hien tai

1. `usePage()` fetch noi dung page tu CMS
2. `parseShortcodeBlocks()` trong `app/utils/shortcode/blocks.ts` tach content thanh `ShortcodeBlock[]`
3. Moi block duoc `detectShortcodeName()` trong `app/utils/shortcode/registry.ts` nhan dien
4. `BlockRenderer.vue` resolve component qua `app/components/shortcode/component-registry.ts`
5. Component shortcode tu parse `block.raw` roi render UI native
6. Neu block khong co component native hoac parser khong du chat luong, no roi vao `Fallback.vue`
7. `Fallback.vue` hien tai bo qua cac block rong chi co `&nbsp;`, `<br>` hoac whitespace

## Cac file can biet

### Data va detect
- `app/utils/shortcode/types.ts`
- `app/utils/shortcode/core.ts`
- `app/utils/shortcode/parsers.ts`
- `app/utils/shortcode/registry.ts`
- `app/utils/shortcode/blocks.ts`

### Render
- `app/components/shortcode/BlockRenderer.vue`
- `app/components/shortcode/component-registry.ts`
- `app/components/shortcode/*.vue`
- `app/components/shortcode/Fallback.vue`

## Quy tac them shortcode moi

Thu tu uu tien:

1. Lay HTML output that tu API hoac Blade partial
2. Xac dinh root block on dinh de detect
3. Them type neu can trong `types.ts`
4. Viet parser trong `parsers.ts`
5. Them entry vao `registry.ts`
6. Tao component trong `app/components/shortcode/`
7. Build va test tren page that

## Rule detect shortcode

- Uu tien detect bang root class cua ca block, vi du `shortcode-cuisine-showcase`
- Khong detect bang class item con nhu `bsingle__post` neu block khac cung dung lai class do
- Neu buoc detect bang item class khong tranh duoc, phai them dieu kien bo sung trong `detectShortcodeName()` de tranh match nham
- Alias trong `registry.ts` chi nen la cac dau hieu nhan dien cap block, khong phai cap card con

Vi du sai:
- detect `blog-posts` bang `bsingle__post`

Vi du dung hon:
- detect `blog-posts` bang dau hieu dac trung hon nhu `blog__btn`
- hoac them special-case trong `detectShortcodeName()` khi can nhieu dieu kien cung luc

## Rule viet parser

- Parser chi lo data, khong lo UI
- Luon bo vao root section truoc khi parse neu co the
- Field thieu thi tra `null`, `[]` hoac default an toan
- Uu tien parse cac field can dung that su
- Neu asset co the la relative URL, component nen resolve qua composable asset resolver truoc khi render

## Rule viet component shortcode

Component shortcode nen:

- chi nhan `block`
- goi parser tu `block.raw`
- render native layout khi co du du lieu
- co fallback `v-html` khi parser chua du manh
- khong tu detect shortcode name trong component

Mau co ban:

```vue
<script setup lang="ts">
import { parseSomethingBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseSomethingBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
</script>

<template>
  <section v-if="section.items.length" class="shortcode-something-native">
    <!-- native UI -->
  </section>

  <section v-else class="shortcode-something-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>
```

## Khi nao chi can sua 1 file shortcode

Thuong chi sua 1 file component neu:

- block da duoc map dung sang component native
- chi doi layout, spacing, color, typography, card, CTA
- khong can parse them field moi

Can sua them parser/registry neu:

- block dang roi vao fallback
- detect sai block
- parser khong lay du data
- HTML thay doi structure

## Checklist verify

- block di dung component native
- khong roi vao `Fallback.vue` neu da co native component
- khong co khoang trong do block rong
- asset hien thi dung o local va production
- responsive khong vo layout
- console khong co hydration mismatch hoac 404 asset
- `npm run build` pass

## Loi thuong gap

### 1. Block van roi vao fallback
- alias khong dung root class that
- component name trong registry khong khop ten file
- `detectShortcodeName()` match nham block khac

### 2. Co native component nhung UI rong
- parser bam sai root block
- parser bam tag qua cung
- data nam o layer khac cua block nhung parser chi doc root section

### 3. Render sai asset
- src la duong dan relative cua CMS
- component render truc tiep ma khong resolve asset base URL

## Handoff ngan

Neu can noi nhanh cho nguoi khac:

"Lay HTML output that, detect bang root class on dinh, viet parser trong `parsers.ts`, them entry vao `registry.ts`, tao component native trong `app/components/shortcode/`, giu fallback de tranh mat noi dung, va test bang page that + build." 
