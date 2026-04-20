<script setup lang="ts">
import { parseHotelPlacesBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseHotelPlacesBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
</script>

<template>
  <section v-if="section.title || section.items.length" class="shortcode-hotel-places-native">
    <div class="container places-shell">
      <header v-if="section.subtitle || section.title || section.description" class="places-header">
        <p v-if="section.subtitle" class="places-header__eyebrow">{{ section.subtitle }}</p>
        <h2 v-if="section.title" class="places-header__title">{{ section.title }}</h2>
        <p v-if="section.description" class="places-header__description">{{ section.description }}</p>
      </header>

      <div class="places-grid">
        <article v-for="item in section.items" :key="item.title" class="places-card">
          <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title" class="places-card__image">
          <div class="places-card__body">
            <p v-if="item.meta" class="places-card__meta">{{ item.meta }}</p>
            <h3 class="places-card__title">{{ item.title }}</h3>
            <NuxtLink
              v-if="item.href && resolveLink(item.href)?.isInternal"
              :to="resolveLink(item.href)!.href"
              class="places-card__link"
            >
              Explore
            </NuxtLink>
            <a v-else-if="item.href" :href="item.href" class="places-card__link">Explore</a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-hotel-places-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-hotel-places-native {
  padding: clamp(4rem, 8vw, 7rem) 0;
  background: #fbf7ef;
}
.places-shell {
  display: grid;
  gap: 2.25rem;
}
.places-header {
  max-width: 42rem;
  margin: 0 auto;
  text-align: center;
}
.places-header__eyebrow {
  margin: 0 0 0.75rem;
  color: #8a6e48;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  font-size: 0.78rem;
}
.places-header__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.2rem);
}
.places-header__description {
  margin: 1rem 0 0;
  color: rgba(47, 36, 29, 0.78);
  line-height: 1.9;
}
.places-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(4, minmax(0, 1fr));
}
.places-card {
  overflow: hidden;
  border-radius: 1.5rem;
  background: rgba(255, 252, 246, 0.88);
  box-shadow: 0 18px 44px rgba(48, 35, 27, 0.08);
}
.places-card__image {
  width: 100%;
  aspect-ratio: 4 / 5;
  display: block;
  object-fit: cover;
}
.places-card__body {
  padding: 1rem 1rem 1.2rem;
}
.places-card__meta {
  margin: 0;
  color: #8a6e48;
  font-size: 0.78rem;
}
.places-card__title {
  margin: 0.55rem 0 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.8rem;
  line-height: 1.04;
}
.places-card__link {
  display: inline-flex;
  margin-top: 0.85rem;
  color: #6c744f;
  text-decoration: none;
  font-weight: 600;
}
@media (max-width: 1024px) {
  .places-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 640px) {
  .places-grid {
    grid-template-columns: 1fr;
  }
}
</style>
