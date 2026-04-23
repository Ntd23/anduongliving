<script setup lang="ts">
import { parseBrandsBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";
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
  padding: 3.5rem 0;
  background:
    radial-gradient(circle at center, rgba(185, 130, 90, 0.06), transparent 40%),
    linear-gradient(180deg, #faf5ee, #f2e9de);
}

.brands-shell {
  display: grid;
  gap: 0.75rem;
  grid-template-columns: repeat(6, minmax(0, 1fr));
  align-items: center;
}

.brands-item {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 7rem;
  border-radius: 1.35rem;
  border: 1px solid rgba(111, 117, 83, 0.08);
  background: rgba(255, 252, 246, 0.72);
  backdrop-filter: blur(6px);
  box-shadow: 0 8px 24px rgba(47, 36, 29, 0.04);
  transition: box-shadow 0.3s ease;
}

.brands-item:hover {
  box-shadow: 0 12px 32px rgba(47, 36, 29, 0.08);
}

.brands-item__image {
  max-width: 80%;
  max-height: 3rem;
  object-fit: contain;
  filter: saturate(0.6);
  transition: filter 0.3s ease;
}

.brands-item:hover .brands-item__image {
  filter: saturate(1);
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




