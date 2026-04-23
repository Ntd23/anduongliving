<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref } from "vue";
import { parseFeaturedRoomsBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { useScrollAnimation } from "~/composables/useScrollAnimation";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseFeaturedRoomsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);

const isInternalLink = (value?: string | null) => {
  const href = String(value || "").trim();
  return !!href && href.startsWith("/") && !href.startsWith("//");
};

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
    class="shortcode-services"
  >
    <div class="container shortcode-services__inner animate-on-scroll">
      <header
        v-if="section.subtitle || section.title || section.description"
        class="shortcode-services__header animate-on-scroll"
      >
        <p v-if="section.subtitle" class="shortcode-services__eyebrow animate-on-scroll" style="--delay: 100ms">
          {{ section.subtitle }}
        </p>
        <h2 v-if="section.title" class="shortcode-services__title animate-on-scroll" style="--delay: 200ms">
          {{ section.title }}
        </h2>
        <p v-if="section.description" class="shortcode-services__description animate-on-scroll" style="--delay: 300ms">
          {{ section.description }}
        </p>
      </header>

      <div class="shortcode-featured-rooms__grid animate-on-scroll">
        <article
          v-for="(item, index) in section.items"
          :key="item.id"
          class="shortcode-featured-rooms__card animate-on-scroll"
          :style="{ '--delay': `${index * 100}ms` }"
        >
          <div class="shortcode-featured-rooms__media">
            <img
              v-if="item.image"
              :src="item.image"
              :alt="item.name"
              class="shortcode-featured-rooms__image"
            >
            
            <!-- Glass panel overlay with text -->
            <div class="shortcode-featured-rooms__overlay">
              <div class="shortcode-featured-rooms__glass-panel">
                <h3 class="shortcode-featured-rooms__title">{{ item.name }}</h3>

                <div class="shortcode-featured-rooms__actions">
                  <NuxtLink
                    v-if="isInternalLink(item.url)"
                    :to="item.url"
                    class="shortcode-featured-rooms__action"
                  >
                    {{ item.bookLabel }}
                  </NuxtLink>

                  <a
                    v-else
                    :href="item.url || '#'"
                    class="shortcode-featured-rooms__action"
                  >
                    {{ item.bookLabel }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-services__fallback">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-services {
  padding: clamp(3.5rem, 7vw, 5.5rem) 0;
  background:
    radial-gradient(circle at top left, rgba(214, 191, 149, 0.18), transparent 32%),
    linear-gradient(180deg, #fffaf3 0%, #f5ede1 100%);
}

.shortcode-services__inner {
  display: grid;
  gap: 2.5rem;
}

.shortcode-services__header {
  max-width: 40rem;
  display: grid;
  gap: 0.9rem;
}

.shortcode-services__eyebrow {
  margin: 0 0 0.5rem 0;
  color: #8a6e48;
  font-size: 0.875rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  line-height: 0.95;
  opacity: 0;
  transform: translateY(20px);
}

.shortcode-services__eyebrow.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.shortcode-services__title {
  margin: 0;
  color: #2d2018;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.4rem, 5vw, 4rem);
  font-weight: 600;
  line-height: 0.95;
  opacity: 0;
  transform: translateY(30px);
}

.shortcode-services__title.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.shortcode-services__description {
  margin: 0;
  color: rgba(58, 42, 31, 0.78);
  font-size: 1rem;
  line-height: 1.8;
  opacity: 0;
  transform: translateY(20px);
}

.shortcode-services__description.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.shortcode-featured-rooms__grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 2rem;
}

.shortcode-featured-rooms__card {
  position: relative;
  border-radius: 2rem;
  overflow: hidden;
  background: #f5f5f0;
  transition: all 0.4s ease;
  opacity: 0;
  transform: translateY(30px);
}

.shortcode-featured-rooms__card.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.shortcode-featured-rooms__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
  opacity: 0;
}

.shortcode-featured-rooms__card.animate-in .shortcode-featured-rooms__image {
  opacity: 1;
  transition: opacity 1s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 200ms);
}

.shortcode-featured-rooms__card:hover .shortcode-featured-rooms__image {
  transform: scale(1.1);
}

.shortcode-featured-rooms__card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 25px 60px rgba(48, 35, 27, 0.15);
}

.shortcode-featured-rooms__media {
  position: relative;
  aspect-ratio: 4 / 5;
  overflow: hidden;
}

.shortcode-featured-rooms__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.shortcode-featured-rooms__card:hover .shortcode-featured-rooms__image {
  transform: scale(1.1);
}

.shortcode-featured-rooms__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 50%);
  display: flex;
  align-items: flex-end;
}

.shortcode-featured-rooms__glass-panel {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 1.5rem;
  padding: 1.5rem;
  width: 100%;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  opacity: 0;
  transform: translateY(20px);
}

.shortcode-featured-rooms__card.animate-in .shortcode-featured-rooms__glass-panel {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 400ms);
}

.shortcode-featured-rooms__title {
  margin: 0 0 0.5rem 0;
  color: #ffffff;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.4rem;
  font-weight: 600;
  line-height: 1.2;
  text-align: left;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
  opacity: 0;
  transform: translateY(15px);
}

.shortcode-featured-rooms__card.animate-in .shortcode-featured-rooms__title {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 600ms);
}

.shortcode-featured-rooms__price {
  margin: 0 0 1rem 0;
  color: #f8f8f8;
  font-size: 1.1rem;
  font-weight: 500;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
}

.shortcode-featured-rooms__actions {
  display: flex;
  justify-content: flex-start;
}

.shortcode-featured-rooms__action {
  display: inline-flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0.8rem 2rem;
  border-radius: 3rem;
  color: #f8f8f8;
  font-size: 0.9rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  border: 1px solid rgba(255, 255, 255, 0.3);
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  opacity: 0;
  transform: translateY(10px);
}

.shortcode-featured-rooms__card.animate-in .shortcode-featured-rooms__action {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.5s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 300ms);
}

.shortcode-featured-rooms__action:hover {
  background: rgba(255, 255, 255, 1);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
  color: #8a6e48;
}


.shortcode-services__fallback {
  display: contents;
}

/* Tablet: 2 items with better spacing */
@media (max-width: 1024px) {
  .shortcode-featured-rooms__grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.5rem;
  }
  
  .shortcode-featured-rooms__title {
    font-size: 1.6rem;
  }
  
  .shortcode-featured-rooms__overlay {
    padding: 1.5rem;
  }
  
  .shortcode-featured-rooms__glass-panel {
    padding: 1.2rem;
  }
}

/* Mobile: Horizontal rail with 3 items viewport */
@media (max-width: 768px) {
  .shortcode-featured-rooms__grid {
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

  .shortcode-featured-rooms__grid::-webkit-scrollbar {
    height: 6px;
  }

  .shortcode-featured-rooms__grid::-webkit-scrollbar-track {
    background: rgba(138, 110, 72, 0.1);
    border-radius: 3px;
  }

  .shortcode-featured-rooms__grid::-webkit-scrollbar-thumb {
    background: rgba(138, 110, 72, 0.3);
    border-radius: 3px;
  }

  .shortcode-featured-rooms__card {
    scroll-snap-align: start;
    min-width: 0;
  }

  .shortcode-featured-rooms__media {
    aspect-ratio: 4 / 4.5;
  }

  .shortcode-featured-rooms__overlay {
    padding: 1.2rem;
  }

  .shortcode-featured-rooms__glass-panel {
    padding: 1rem;
    backdrop-filter: blur(8px);
  }

  .shortcode-featured-rooms__title {
    font-size: 1.4rem;
    margin-bottom: 0.3rem;
  }

  .shortcode-featured-rooms__price {
    font-size: 1rem;
    margin-bottom: 0.8rem;
  }

  .shortcode-featured-rooms__action {
    padding: 0.6rem 1.5rem;
    font-size: 0.8rem;
  }
}

/* Small mobile adjustments */
@media (max-width: 480px) {
  .shortcode-featured-rooms__grid {
    grid-auto-columns: minmax(80%, 90%);
    gap: 0.8rem;
  }
  
  .shortcode-featured-rooms__title {
    font-size: 1.3rem;
  }
  
  .shortcode-featured-rooms__glass-panel {
    padding: 0.8rem;
  }
}
</style>




