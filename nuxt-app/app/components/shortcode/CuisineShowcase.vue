<script setup lang="ts">
import { parseCuisineShowcaseBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseCuisineShowcaseBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const action = computed(() => resolveLink(section.value.action?.href));
</script>

<template>
  <section
    v-if="section.title || section.description || section.images.length"
    class="shortcode-cuisine-native"
  >
    <div class="cuisine-shell">
      <header class="cuisine-copy">
        <h2 v-if="section.title" class="cuisine-copy__title">{{ section.title }}</h2>
        <p v-if="section.description" class="cuisine-copy__description">{{ section.description }}</p>
      </header>

      <div v-if="section.images.length" class="cuisine-gallery">
        <figure
          v-for="(image, index) in section.images"
          :key="`${image.src}-${index}`"
          class="cuisine-gallery__item"
          :class="{ 'is-offset': index % 2 === 1 }"
        >
          <img :src="image.src" :alt="image.alt || `Cuisine image ${index + 1}`">
        </figure>
      </div>

      <footer v-if="section.sectionLabel || section.action?.label" class="cuisine-footer">
        <p v-if="section.sectionLabel" class="cuisine-footer__label">{{ section.sectionLabel }}</p>
        <NuxtLink v-if="action?.isInternal" :to="action.href" class="cuisine-footer__button">
          {{ section.action?.label }}
        </NuxtLink>
        <a v-else-if="action" :href="action.href" class="cuisine-footer__button">
          {{ section.action?.label }}
        </a>
      </footer>
    </div>
  </section>

  <section v-else class="shortcode-cuisine-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-cuisine-native {
  background: linear-gradient(180deg, #fcf8f1, #f4ecdf);
  padding: clamp(4rem, 8vw, 7rem) 0;
}

.cuisine-shell {
  width: min(calc(100% - 2rem), 68rem);
  margin: 0 auto;
  text-align: center;
}

.cuisine-copy__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.6rem, 5vw, 4.4rem);
  line-height: 0.98;
}

.cuisine-copy__description {
  max-width: 42rem;
  margin: 1.25rem auto 0;
  color: rgba(47, 36, 29, 0.78);
  line-height: 1.95;
}

.cuisine-gallery {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  margin-top: 2.2rem;
}

.cuisine-gallery__item {
  margin: 0;
  overflow: hidden;
  border-radius: 1.6rem;
  box-shadow: 0 28px 56px rgba(48, 35, 27, 0.08);
}

.cuisine-gallery__item.is-offset {
  transform: translateY(2rem);
}

.cuisine-gallery__item img {
  width: 100%;
  display: block;
  aspect-ratio: 4 / 5;
  object-fit: cover;
}

.cuisine-footer {
  margin-top: 4rem;
}

.cuisine-footer__label {
  margin: 0 0 0.8rem;
  color: #8a6e48;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  font-size: 0.76rem;
}

.cuisine-footer__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 12rem;
  padding: 0.95rem 1.4rem;
  border-radius: 999px;
  background: #6c744f;
  color: #fffaf1;
  text-decoration: none;
}

@media (max-width: 640px) {
  .cuisine-gallery {
    grid-template-columns: 1fr;
  }

  .cuisine-gallery__item.is-offset {
    transform: none;
  }
}
</style>
