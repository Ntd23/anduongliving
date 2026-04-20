<script setup lang="ts">
import { parseTestimonialsBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseTestimonialsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const sectionStyle = computed(() =>
  section.value.backgroundImage?.src
    ? { backgroundImage: `url('${section.value.backgroundImage.src}')` }
    : undefined,
);
</script>

<template>
  <section v-if="section.title || section.items.length" class="shortcode-testimonials-native" :style="sectionStyle">
    <div class="testimonials-overlay" />
    <div class="container testimonials-shell">
      <header v-if="section.subtitle || section.title || section.description" class="testimonials-header">
        <p v-if="section.subtitle" class="testimonials-header__eyebrow">{{ section.subtitle }}</p>
        <h2 v-if="section.title" class="testimonials-header__title">{{ section.title }}</h2>
        <p v-if="section.description" class="testimonials-header__description">{{ section.description }}</p>
      </header>

      <div class="testimonials-grid">
        <article v-for="item in section.items" :key="item.name" class="testimonial-card">
          <div class="testimonial-card__author">
            <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.name" class="testimonial-card__avatar">
            <h3 class="testimonial-card__name">{{ item.name }}</h3>
          </div>
          <p v-if="item.content" class="testimonial-card__content">{{ item.content }}</p>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-testimonials-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-testimonials-native {
  position: relative;
  padding: clamp(4rem, 8vw, 7rem) 0;
  background-color: #18120f;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  color: #f7efe2;
}
.testimonials-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(17, 12, 9, 0.72), rgba(17, 12, 9, 0.82));
}
.testimonials-shell {
  position: relative;
  z-index: 1;
  display: grid;
  gap: 2.5rem;
}
.testimonials-header {
  max-width: 42rem;
  margin: 0 auto;
  text-align: center;
}
.testimonials-header__eyebrow {
  margin: 0 0 0.75rem;
  color: #cbb27b;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  font-size: 0.78rem;
}
.testimonials-header__title {
  margin: 0;
  color: #fff8ee;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.2rem);
}
.testimonials-header__description {
  margin: 1rem 0 0;
  color: rgba(247, 239, 226, 0.8);
  line-height: 1.9;
}
.testimonials-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
.testimonial-card {
  padding: 1.6rem;
  border-radius: 1.5rem;
  background: rgba(255, 252, 246, 0.1);
  backdrop-filter: blur(8px);
}
.testimonial-card__author {
  display: flex;
  align-items: center;
  gap: 0.9rem;
}
.testimonial-card__avatar {
  width: 3.75rem;
  height: 3.75rem;
  border-radius: 999px;
  object-fit: cover;
}
.testimonial-card__name {
  margin: 0;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.8rem;
}
.testimonial-card__content {
  margin: 1rem 0 0;
  color: rgba(247, 239, 226, 0.84);
  line-height: 1.9;
}
@media (max-width: 991px) {
  .testimonials-grid {
    grid-template-columns: 1fr;
  }
}
</style>
