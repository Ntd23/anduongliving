<script setup lang="ts">
import { parseHotelPlacesBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";
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
        <div class="places-header__glass">
          <p v-if="section.subtitle" class="places-header__eyebrow">{{ section.subtitle }}</p>
          <h2 v-if="section.title" class="places-header__title">{{ section.title }}</h2>
          <p v-if="section.description" class="places-header__description">{{ section.description }}</p>
        </div>
      </header>

      <div class="places-grid">
        <article v-for="item in section.items" :key="item.title" class="places-card">
          <div class="places-card__media">
            <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title" class="places-card__image">
            <div class="places-card__overlay" />
          </div>
          <div class="places-card__body">
            <div class="places-card__glass">
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
  background:
    radial-gradient(circle at top right, rgba(185, 130, 90, 0.1), transparent 28%),
    linear-gradient(180deg, #fcfaf6, #f3ecdf);
}

.places-shell {
  display: grid;
  gap: 3.25rem;
}

.places-header {
  max-width: 76rem;
  margin: 0 auto;
}

.places-header__glass {
  text-align: center;
  padding: clamp(1.5rem, 3vw, 2.25rem) clamp(1rem, 2vw, 1.75rem);
  border: 1px solid rgba(111, 117, 83, 0.1);
  border-radius: 1.75rem;
  background: rgba(255, 252, 246, 0.72);
  backdrop-filter: blur(8px);
  box-shadow: 0 16px 40px rgba(47, 36, 29, 0.05);
}

.places-header__eyebrow {
  margin: 0 0 0.75rem;
  color: #8a6e48;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  font-size: 0.78rem;
  font-weight: 600;
}

.places-header__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  max-width: 66rem;
  margin-inline: auto;
  font-size: clamp(3rem, 5.35vw, 5.1rem);
  line-height: 0.98;
  letter-spacing: -0.015em;
}

.places-header__description {
  margin: 1.35rem auto 0;
  max-width: 68rem;
  color: rgba(47, 36, 29, 0.76);
  font-size: 1.05rem;
  line-height: 1.8;
  text-align: center;
}

.places-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(4, minmax(0, 1fr));
}

.places-card {
  position: relative;
  overflow: hidden;
  border-radius: 1.6rem;
  background: #1a120c;
  box-shadow: 0 18px 44px rgba(48, 35, 27, 0.12);
  transition: box-shadow 0.35s ease;
}

.places-card:hover {
  box-shadow: 0 24px 56px rgba(48, 35, 27, 0.18);
}

.places-card__media {
  position: relative;
}

.places-card__image {
  width: 100%;
  aspect-ratio: 3 / 4;
  display: block;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.places-card:hover .places-card__image {
  transform: scale(1.03);
}

.places-card__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(0deg, rgba(14, 8, 4, 0.82) 0%, rgba(14, 8, 4, 0.2) 50%, transparent 100%);
}

.places-card__body {
  position: absolute;
  left: 0.6rem;
  right: 0.6rem;
  bottom: 0.6rem;
  z-index: 1;
}

.places-card__glass {
  padding: 0.85rem 1rem;
  border: 1px solid rgba(248, 243, 234, 0.1);
  border-radius: 1.15rem;
  background: rgba(32, 22, 16, 0.36);
  backdrop-filter: blur(12px);
  box-shadow: inset 0 1px 0 rgba(255, 248, 237, 0.06);
}

.places-card__meta {
  margin: 0 0 0.3rem;
  color: #e3c8a8;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.places-card__title {
  margin: 0;
  color: #fff7ef;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.3rem, 1.8vw, 1.7rem);
  line-height: 1.08;
}

.places-card__link {
  display: inline-flex;
  margin-top: 0.55rem;
  padding: 0.3rem 0.75rem;
  border-radius: 999px;
  background: rgba(108, 116, 79, 0.7);
  color: #faf4ea;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.76rem;
  letter-spacing: 0.06em;
}

@media (max-width: 1024px) {
  .places-shell {
    gap: 2.75rem;
  }

  .places-header {
    max-width: 68rem;
  }

  .places-header__glass {
    padding-inline: clamp(1rem, 2.5vw, 1.75rem);
  }

  .places-header__title {
    max-width: 56rem;
    font-size: clamp(2.7rem, 5vw, 4.2rem);
    line-height: 1.02;
  }

  .places-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .places-shell {
    gap: 2.25rem;
  }

  .places-header__glass {
    padding: 1.25rem 1.1rem;
  }

  .places-header__title {
    max-width: none;
    font-size: clamp(2.2rem, 10vw, 3rem);
    line-height: 1.08;
  }

  .places-header__description {
    max-width: none;
    font-size: 1rem;
    line-height: 1.75;
    text-align: center;
  }

  .places-grid {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(78%, 78%);
    grid-template-columns: none;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
  }

  .places-grid::-webkit-scrollbar {
    display: none;
  }

  .places-card {
    scroll-snap-align: start;
  }
}
</style>
