<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref } from "vue";
import { parseFeaturedAmenitiesBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { useScrollAnimation } from "~/composables/useScrollAnimation";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseFeaturedAmenitiesBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const sectionStyle = computed(() =>
  section.value.backgroundColor ? { backgroundColor: section.value.backgroundColor } : undefined,
);

const { setupScrollAnimations } = useScrollAnimation()

onMounted(async () => {
  await nextTick()
  setupScrollAnimations()
})

onUnmounted(() => {
  // Cleanup handled by composable
})
</script>

<template>
  <section
    v-if="section.title || section.subtitle || section.items.length"
    class="shortcode-featured-amenities-native"
    :style="sectionStyle"
  >
    <div class="container amenities-shell animate-on-scroll">
      <header
        v-if="section.subtitle || section.title || section.description"
        class="amenities-header animate-on-scroll"
      >
        <p v-if="section.subtitle" class="amenities-header__eyebrow animate-on-scroll" style="--delay: 100ms">
          {{ section.subtitle }}
        </p>
        <h2 v-if="section.title" class="amenities-header__title animate-on-scroll" style="--delay: 200ms">
          {{ section.title }}
        </h2>
        <p v-if="section.description" class="amenities-header__description animate-on-scroll" style="--delay: 300ms">
          {{ section.description }}
        </p>
      </header>

      <div class="amenities-grid animate-on-scroll">
        <article
          v-for="(item, index) in section.items"
          :key="item.title"
          class="amenities-card animate-on-scroll"
          :style="{ '--delay': `${index * 100}ms` }"
          :aria-label="item.image?.alt || item.title"
        >
          <div class="amenities-card__media">
            <img
              v-if="item.image?.src"
              :src="item.image.src"
              :alt="item.image?.alt || item.title"
              class="amenities-card__image"
            >
            
            <!-- Glass panel overlay with text -->
            <div class="amenities-card__overlay">
              <div class="amenities-card__glass-panel">
                <h3 class="amenities-card__title">{{ item.title }}</h3>
                <p v-if="item.description" class="amenities-card__description">
                  {{ item.description }}
                </p>
              </div>
            </div>
          </div>
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
  opacity: 0;
  transform: translateY(20px);
}

.amenities-header__eyebrow.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.amenities-header__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.2rem);
  line-height: 0.96;
  opacity: 0;
  transform: translateY(30px);
}

.amenities-header__title.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.amenities-header__description {
  margin: 1rem 0 0;
  color: rgba(47, 36, 29, 0.78);
  line-height: 1.9;
  opacity: 0;
  transform: translateY(20px);
}

.amenities-header__description.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

/* Grid - 4 columns desktop */
.amenities-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 2rem;
}

/* Card */
.amenities-card {
  position: relative;
  border-radius: 2rem;
  overflow: hidden;
  background: #f5f5f0;
  transition: all 0.4s ease;
  opacity: 0;
  transform: translateY(30px);
}

.amenities-card.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.amenities-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 25px 60px rgba(48, 35, 27, 0.15);
}

.amenities-card__media {
  position: relative;
  aspect-ratio: 3 / 2;
  overflow: hidden;
}

.amenities-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
  opacity: 0;
}

.amenities-card.animate-in .amenities-card__image {
  opacity: 1;
  transition: opacity 1s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 200ms);
}

.amenities-card:hover .amenities-card__image {
  transform: scale(1.1);
}

/* Gradient overlay for text readability */
.amenities-card__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 50%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

/* Glass panel following Surface Rules */
.amenities-card__glass-panel {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 1.5rem;
  padding: 1.2rem 1.5rem;
  width: auto;
  max-width: 90%;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  opacity: 0;
  transform: translateY(20px);
}

.amenities-card.animate-in .amenities-card__glass-panel {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 400ms);
}

.amenities-card__title {
  margin: 0 0 0.5rem 0;
  color: #ffffff;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.8rem;
  font-weight: 600;
  line-height: 1.2;
  text-align: left;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
  opacity: 0;
  transform: translateY(15px);
}

.amenities-card.animate-in .amenities-card__title {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 600ms);
}

.amenities-card__description {
  margin: 0;
  color: #f8f8f8;
  line-height: 1.8;
  text-align: left;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
  opacity: 0;
  transform: translateY(15px);
}

.amenities-card.animate-in .amenities-card__description {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 800ms);
}

/* Scroll animations */
.animate-on-scroll {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.8s ease-out;
}

.animate-on-scroll.animate-in {
  opacity: 1;
  transform: translateY(0);
}

.animate-on-scroll.animate-out {
  opacity: 0;
  transform: translateY(30px);
}

/* Tablet: 2 items with better spacing */
@media (max-width: 1024px) {
  .amenities-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.5rem;
  }
  
  .amenities-card__title {
    font-size: 1.6rem;
  }
  
  .amenities-card__overlay {
    padding: 1.5rem;
  }
  
  .amenities-card__glass-panel {
    padding: 1.2rem;
  }
}

/* Mobile: Horizontal rail with 3 items viewport */
@media (max-width: 768px) {
  .amenities-grid {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(75%, 85%);
    grid-template-columns: none;
    overflow-x: auto;
    overflow-y: hidden;
    padding-bottom: 1rem;
    scroll-snap-type: x mandatory;
    scrollbar-width: thin;
    scrollbar-color: rgba(138, 110, 72, 0.3) transparent;
    gap: 1rem;
  }

  .amenities-grid::-webkit-scrollbar {
    height: 6px;
  }

  .amenities-grid::-webkit-scrollbar-track {
    background: rgba(138, 110, 72, 0.1);
    border-radius: 3px;
  }

  .amenities-grid::-webkit-scrollbar-thumb {
    background: rgba(138, 110, 72, 0.3);
    border-radius: 3px;
  }

  .amenities-card {
    scroll-snap-align: start;
    min-width: 0;
  }

  .amenities-card__media {
    aspect-ratio: 4 / 4.5;
  }

  .amenities-card__overlay {
    padding: 1.2rem;
    align-items: center;
    justify-content: center;
  }

  .amenities-card__glass-panel {
    padding: 0.8rem 1rem;
    width: auto;
    max-width: 85%;
    backdrop-filter: blur(8px);
  }

  .amenities-card__title {
    font-size: 1.4rem;
    margin-bottom: 0.3rem;
  }

  .amenities-card__description {
    font-size: 0.9rem;
  }
}

/* Small mobile adjustments */
@media (max-width: 480px) {
  .amenities-grid {
    grid-auto-columns: minmax(80%, 90%);
    gap: 0.8rem;
  }
  
  .amenities-card__title {
    font-size: 1.3rem;
  }
  
  .amenities-card__glass-panel {
    padding: 0.8rem;
  }
}
</style>