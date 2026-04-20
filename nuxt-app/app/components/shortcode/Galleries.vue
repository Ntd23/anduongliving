<script setup lang="ts">
import { parseGalleriesBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseGalleriesBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const activeFilter = ref("*");

const items = computed(() =>
  section.value.items.filter((item) => activeFilter.value === "*" || item.filter === activeFilter.value.replace(/^\./, "")),
);
</script>

<template>
  <section v-if="section.items.length" class="shortcode-galleries-native">
    <div class="container galleries-shell">
      <div v-if="section.filters.length" class="galleries-filters">
        <button
          v-for="filter in section.filters"
          :key="filter.value"
          type="button"
          class="galleries-filters__button"
          :class="{ 'is-active': filter.value === activeFilter }"
          @click="activeFilter = filter.value"
        >
          {{ filter.label }}
        </button>
      </div>

      <div class="galleries-grid">
        <article v-for="item in items" :key="`${item.href || item.title}-${item.image?.src}`" class="galleries-item">
          <NuxtLink
            v-if="item.href && resolveLink(item.href)?.isInternal"
            :to="resolveLink(item.href)!.href"
            class="galleries-item__link"
          >
            <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title || 'Gallery image'" class="galleries-item__image">
          </NuxtLink>
          <a v-else-if="item.href" :href="item.href" class="galleries-item__link">
            <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title || 'Gallery image'" class="galleries-item__image">
          </a>
          <img v-else-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title || 'Gallery image'" class="galleries-item__image">
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-galleries-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-galleries-native {
  padding: clamp(4rem, 8vw, 7rem) 0;
  background: #fbf7ef;
}
.galleries-shell {
  display: grid;
  gap: 2rem;
}
.galleries-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  justify-content: center;
}
.galleries-filters__button {
  border: 1px solid rgba(107, 116, 79, 0.15);
  border-radius: 999px;
  background: rgba(255, 252, 246, 0.88);
  padding: 0.75rem 1.1rem;
  color: #4c3a2c;
}
.galleries-filters__button.is-active {
  background: #6c744f;
  color: #fffaf1;
}
.galleries-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(4, minmax(0, 1fr));
}
.galleries-item {
  overflow: hidden;
  border-radius: 1.3rem;
}
.galleries-item__link {
  display: block;
}
.galleries-item__image {
  width: 100%;
  aspect-ratio: 1;
  display: block;
  object-fit: cover;
}
@media (max-width: 991px) {
  .galleries-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 640px) {
  .galleries-grid {
    grid-template-columns: 1fr;
  }
}
</style>
