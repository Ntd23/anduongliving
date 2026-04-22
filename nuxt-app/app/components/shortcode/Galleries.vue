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
            <div class="galleries-item__overlay" />
          </NuxtLink>
          <a v-else-if="item.href" :href="item.href" class="galleries-item__link">
            <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title || 'Gallery image'" class="galleries-item__image">
            <div class="galleries-item__overlay" />
          </a>
          <template v-else>
            <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title || 'Gallery image'" class="galleries-item__image">
            <div class="galleries-item__overlay" />
          </template>
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
  background:
    radial-gradient(circle at top right, rgba(185, 130, 90, 0.08), transparent 28%),
    linear-gradient(180deg, #fcfaf6, #f3ecdf);
}

.galleries-shell {
  display: grid;
  gap: 2rem;
}

.galleries-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 0.55rem;
  justify-content: center;
}

.galleries-filters__button {
  border: 1px solid rgba(111, 117, 83, 0.12);
  border-radius: 999px;
  background: rgba(255, 252, 246, 0.82);
  backdrop-filter: blur(6px);
  padding: 0.6rem 1.15rem;
  color: #4c3a2c;
  font-weight: 600;
  font-size: 0.86rem;
  cursor: pointer;
  transition:
    background-color 0.2s ease,
    color 0.2s ease,
    border-color 0.2s ease;
}

.galleries-filters__button.is-active {
  background: linear-gradient(135deg, #6c744f, #5e6746);
  color: #fffaf1;
  border-color: transparent;
  box-shadow: 0 8px 20px rgba(108, 116, 79, 0.18);
}

.galleries-grid {
  display: grid;
  gap: 0.75rem;
  grid-template-columns: repeat(4, minmax(0, 1fr));
}

.galleries-item {
  position: relative;
  overflow: hidden;
  border-radius: 1.35rem;
  box-shadow: 0 12px 32px rgba(48, 35, 27, 0.08);
}

.galleries-item__link {
  display: block;
  position: relative;
}

.galleries-item__image {
  width: 100%;
  aspect-ratio: 1;
  display: block;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.galleries-item__overlay {
  position: absolute;
  inset: 0;
  background: rgba(14, 8, 4, 0);
  transition: background 0.35s ease;
}

.galleries-item:hover .galleries-item__image {
  transform: scale(1.04);
}

.galleries-item:hover .galleries-item__overlay {
  background: rgba(14, 8, 4, 0.15);
}

@media (max-width: 991px) {
  .galleries-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .galleries-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.5rem;
  }
}
</style>
