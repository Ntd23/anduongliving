<script setup lang="ts">
import { parseLocationTourismShowcaseBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseLocationTourismShowcaseBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();

const sectionStyle = computed(() =>
  section.value.backgroundImage?.src
    ? { backgroundImage: `url('${section.value.backgroundImage.src}')` }
    : undefined,
);

const accessAction = computed(() => resolveLink(section.value.access.action?.href));
const tourismAction = computed(() => resolveLink(section.value.tourism.action?.href));
</script>

<template>
  <section
    v-if="section.title || section.mapImage?.src || section.gridImages.length"
    class="shortcode-location-tourism-native"
    :style="sectionStyle"
  >
    <div class="location-shell">
      <header class="location-header">
        <span v-if="section.decorativeText" class="location-header__deco">{{ section.decorativeText }}</span>
        <h2 v-if="section.title" class="location-header__title">{{ section.title }}</h2>
      </header>

      <div class="location-main">
        <figure v-if="section.mapImage?.src" class="location-map">
          <img :src="section.mapImage.src" :alt="section.mapImage.alt || 'Map image'">
        </figure>

        <div v-if="section.gridImages.length" class="location-grid">
          <figure
            v-for="(image, index) in section.gridImages"
            :key="`${image.src}-${index}`"
            class="location-grid__item"
          >
            <img :src="image.src" :alt="image.alt || `Tourism image ${index + 1}`">
          </figure>
        </div>
      </div>

      <div class="location-columns">
        <article class="location-column">
          <p v-if="section.access.description" class="location-column__description">{{ section.access.description }}</p>
          <p v-if="section.access.label" class="location-column__label">{{ section.access.label }}</p>
          <NuxtLink v-if="accessAction?.isInternal" :to="accessAction.href" class="location-column__button">
            {{ section.access.action?.label }}
          </NuxtLink>
          <a v-else-if="accessAction" :href="accessAction.href" class="location-column__button">
            {{ section.access.action?.label }}
          </a>
        </article>

        <article class="location-column">
          <p v-if="section.tourism.description" class="location-column__description">{{ section.tourism.description }}</p>
          <p v-if="section.tourism.label" class="location-column__label">{{ section.tourism.label }}</p>
          <NuxtLink v-if="tourismAction?.isInternal" :to="tourismAction.href" class="location-column__button">
            {{ section.tourism.action?.label }}
          </NuxtLink>
          <a v-else-if="tourismAction" :href="tourismAction.href" class="location-column__button">
            {{ section.tourism.action?.label }}
          </a>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-location-tourism-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-location-tourism-native {
  position: relative;
  padding: clamp(4rem, 8vw, 7rem) 0;
  background-color: #15100d;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  color: #f6efe4;
}

.shortcode-location-tourism-native::before {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(14, 10, 8, 0.86), rgba(14, 10, 8, 0.56));
}

.location-shell {
  position: relative;
  z-index: 1;
  width: min(calc(100% - 2rem), 78rem);
  margin: 0 auto;
}

.location-header {
  position: relative;
  margin-bottom: 2.5rem;
  text-align: center;
}

.location-header__deco {
  position: absolute;
  inset: auto 0 40% 0;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(4rem, 12vw, 8rem);
  color: rgba(255, 248, 236, 0.08);
}

.location-header__title {
  position: relative;
  margin: 0;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.3rem, 4.6vw, 4.5rem);
  line-height: 0.98;
}

.location-main {
  display: grid;
  gap: 1.25rem;
  grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
}

.location-map,
.location-grid__item {
  margin: 0;
  overflow: hidden;
  border-radius: 1.4rem;
}

.location-map img,
.location-grid__item img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
}

.location-grid {
  display: grid;
  gap: 0.75rem;
  grid-template-columns: repeat(4, minmax(0, 1fr));
}

.location-grid__item {
  aspect-ratio: 1;
}

.location-columns {
  display: grid;
  gap: 1.25rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  margin-top: 2rem;
}

.location-column {
  padding: 1.6rem;
  border-radius: 1.5rem;
  background: rgba(255, 252, 246, 0.08);
  backdrop-filter: blur(8px);
}

.location-column__description {
  margin: 0;
  color: rgba(246, 239, 228, 0.86);
  line-height: 1.95;
}

.location-column__label {
  margin: 1.1rem 0 0;
  color: #cbb27b;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  font-size: 0.76rem;
}

.location-column__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 12rem;
  margin-top: 1rem;
  padding: 0.9rem 1.3rem;
  border-radius: 999px;
  background: #b19143;
  color: #fff9ef;
  text-decoration: none;
}

@media (max-width: 991px) {
  .location-main,
  .location-columns {
    grid-template-columns: 1fr;
  }

  .location-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
