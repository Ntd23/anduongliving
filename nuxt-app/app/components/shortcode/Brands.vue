<script setup lang="ts">
import { parseBrandsBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseBrandsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const sectionStyle = computed(() =>
  section.value.backgroundColor ? { backgroundColor: section.value.backgroundColor } : undefined,
);
</script>

<template>
  <section v-if="section.items.length" class="shortcode-brands-native" :style="sectionStyle">
    <div class="container brands-shell">
      <component
        :is="item.href ? (resolveLink(item.href)?.isInternal ? 'NuxtLink' : 'a') : 'div'"
        v-for="item in section.items"
        :key="item.image?.src"
        v-bind="
          item.href
            ? resolveLink(item.href)?.isInternal
              ? { to: resolveLink(item.href)!.href }
              : { href: item.href }
            : {}
        "
        class="brands-item"
      >
        <img :src="item.image!.src" :alt="item.image!.alt || item.name || 'Brand logo'" class="brands-item__image">
      </component>
    </div>
  </section>

  <section v-else class="shortcode-brands-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-brands-native {
  padding: 3rem 0;
  background: #f7f2e8;
}
.brands-shell {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(6, minmax(0, 1fr));
  align-items: center;
}
.brands-item {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 7rem;
  border-radius: 1.3rem;
  background: rgba(255, 252, 246, 0.72);
}
.brands-item__image {
  max-width: 100%;
  max-height: 3rem;
  object-fit: contain;
  filter: saturate(0.7);
}
@media (max-width: 991px) {
  .brands-shell {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
@media (max-width: 640px) {
  .brands-shell {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
