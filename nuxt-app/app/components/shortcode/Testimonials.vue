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
  background:
    radial-gradient(circle at 30% 70%, rgba(185, 130, 90, 0.1), transparent 30%),
    linear-gradient(180deg, rgba(17, 12, 9, 0.68), rgba(17, 12, 9, 0.82));
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
  font-weight: 600;
}

.testimonials-header__title {
  margin: 0;
  color: #fff8ee;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.2rem);
  line-height: 1;
}

.testimonials-header__description {
  margin: 1rem 0 0;
  color: rgba(247, 239, 226, 0.78);
  line-height: 1.8;
}

.testimonials-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.testimonial-card {
  padding: 1.5rem;
  border-radius: 1.6rem;
  border: 1px solid rgba(248, 243, 234, 0.1);
  background: rgba(255, 252, 246, 0.07);
  backdrop-filter: blur(12px);
  box-shadow:
    0 16px 40px rgba(0, 0, 0, 0.16),
    inset 0 1px 0 rgba(255, 248, 237, 0.05);
  transition: box-shadow 0.3s ease;
}

.testimonial-card:hover {
  box-shadow:
    0 20px 48px rgba(0, 0, 0, 0.22),
    inset 0 1px 0 rgba(255, 248, 237, 0.08);
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
  border: 2px solid rgba(203, 178, 123, 0.3);
}

.testimonial-card__name {
  margin: 0;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.6rem;
  line-height: 1.1;
  color: #fff8ee;
}

.testimonial-card__content {
  margin: 1rem 0 0;
  color: rgba(247, 239, 226, 0.82);
  line-height: 1.85;
  font-size: 0.95rem;
}

@media (max-width: 991px) {
  .testimonials-grid {
    grid-template-columns: 1fr;
  }
}
</style>
