<script setup lang="ts">
import { parseFeaturedAmenitiesBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseFeaturedAmenitiesBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const sectionStyle = computed(() =>
  section.value.backgroundColor ? { backgroundColor: section.value.backgroundColor } : undefined,
);
</script>

<template>
  <section
    v-if="section.title || section.subtitle || section.items.length"
    class="shortcode-featured-amenities-native"
    :style="sectionStyle"
  >
    <div class="container amenities-shell">
      <header v-if="section.subtitle || section.title || section.description" class="amenities-header">
        <p v-if="section.subtitle" class="amenities-header__eyebrow">{{ section.subtitle }}</p>
        <h2 v-if="section.title" class="amenities-header__title">{{ section.title }}</h2>
        <p v-if="section.description" class="amenities-header__description">{{ section.description }}</p>
      </header>

      <div class="amenities-grid">
        <article v-for="item in section.items" :key="item.title" class="amenities-card">
          <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title" class="amenities-card__icon">
          <h3 class="amenities-card__title">{{ item.title }}</h3>
          <p v-if="item.description" class="amenities-card__description">{{ item.description }}</p>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-featured-amenities-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-featured-amenities-native {
  position: relative;
  padding: clamp(4rem, 8vw, 7rem) 0;
  background: linear-gradient(180deg, #fbf7ef, #f2e9dc);
}
.amenities-shell {
  display: grid;
  gap: 2.5rem;
}
.amenities-header {
  max-width: 42rem;
  margin: 0 auto;
  text-align: center;
}
.amenities-header__eyebrow {
  margin: 0 0 0.75rem;
  color: #8a6e48;
  font-size: 0.78rem;
  letter-spacing: 0.22em;
  text-transform: uppercase;
}
.amenities-header__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.2rem);
  line-height: 0.96;
}
.amenities-header__description {
  margin: 1rem 0 0;
  color: rgba(47, 36, 29, 0.78);
  line-height: 1.9;
}
.amenities-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
.amenities-card {
  padding: 1.7rem;
  border-radius: 1.6rem;
  background: rgba(255, 251, 245, 0.84);
  box-shadow: 0 20px 50px rgba(48, 35, 27, 0.08);
  text-align: center;
}
.amenities-card__icon {
  width: 4rem;
  height: 4rem;
  margin: 0 auto 1rem;
  object-fit: contain;
}
.amenities-card__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.85rem;
}
.amenities-card__description {
  margin: 0.8rem 0 0;
  color: rgba(47, 36, 29, 0.72);
  line-height: 1.8;
}
@media (max-width: 991px) {
  .amenities-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 640px) {
  .amenities-grid {
    grid-template-columns: 1fr;
  }
}
</style>
