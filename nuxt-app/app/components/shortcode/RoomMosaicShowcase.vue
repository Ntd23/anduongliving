<script setup lang="ts">
import { parseRoomMosaicShowcaseBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseRoomMosaicShowcaseBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();

const action = computed(() => resolveLink(section.value.action?.href));
const actionLabel = computed(() => section.value.action?.label || null);

const heroStyle = computed(() =>
  section.value.backgroundImage?.src
    ? { backgroundImage: `url('${section.value.backgroundImage.src}')` }
    : undefined,
);
</script>

<template>
  <section
    v-if="section.title || section.cards.length || section.mainImage?.src"
    class="shortcode-room-mosaic-native"
  >
    <header class="room-mosaic-hero" :style="heroStyle">
      <div class="room-mosaic-hero__overlay" />

      <div class="room-mosaic-hero__inner">
        <p v-if="section.subtitle" class="room-mosaic-hero__eyebrow">
          {{ section.subtitle }}
        </p>
        <h2 v-if="section.title" class="room-mosaic-hero__title">
          {{ section.title }}
        </h2>
        <p v-if="section.description" class="room-mosaic-hero__description">
          {{ section.description }}
        </p>
      </div>
    </header>

    <div v-if="section.mainImage?.src" class="room-mosaic-main-image">
      <img
        :src="section.mainImage.src"
        :alt="section.mainImage.alt || section.title || 'Main showcase image'"
        loading="lazy"
      >
    </div>

    <div class="room-mosaic-content">
      <div v-if="section.cards.length" class="room-mosaic-grid">
        <article
          v-for="(card, index) in section.cards"
          :key="`${card.title || 'card'}-${index}`"
          class="room-mosaic-card"
        >
          <img
            v-if="card.image?.src"
            :src="card.image.src"
            :alt="card.image.alt || card.title || 'Room image'"
            loading="lazy"
          >
          <div v-if="card.title" class="room-mosaic-card__title">
            {{ card.title }}
          </div>
        </article>
      </div>

      <aside class="room-mosaic-sidebar">
        <div v-if="section.sideTextLines.length" class="room-mosaic-sidebar__copy">
          <p v-for="line in section.sideTextLines" :key="line">
            {{ line }}
          </p>
        </div>

        <ul v-if="section.roomItems.length" class="room-mosaic-sidebar__list">
          <li v-for="item in section.roomItems" :key="item">
            {{ item }}
          </li>
        </ul>
      </aside>
    </div>

    <div v-if="actionLabel" class="room-mosaic-cta">
      <div v-if="section.ctaTitle" class="room-mosaic-cta__label">
        {{ section.ctaTitle }}
      </div>

      <NuxtLink
        v-if="action?.isInternal"
        :to="action.href"
        class="room-mosaic-cta__button"
      >
        {{ actionLabel }}
      </NuxtLink>
      <a
        v-else-if="action"
        :href="action.href"
        class="room-mosaic-cta__button"
      >
        {{ actionLabel }}
      </a>
      <div v-else class="room-mosaic-cta__button">
        {{ actionLabel }}
      </div>
    </div>
  </section>

  <section v-else class="shortcode-room-mosaic-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-room-mosaic-native {
  background: linear-gradient(180deg, #faf5ee, #f2eadf);
  padding-bottom: 4.5rem;
}

.room-mosaic-hero {
  position: relative;
  min-height: 33rem;
  padding: 5rem 1.5rem 11rem;
  background-color: #22150f;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}

.room-mosaic-hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(22, 13, 10, 0.58), rgba(22, 13, 10, 0.72));
}

.room-mosaic-hero__inner {
  position: relative;
  z-index: 1;
  width: min(100%, 48rem);
  margin: 0 auto;
  text-align: center;
}

.room-mosaic-hero__eyebrow {
  margin: 0 0 0.8rem;
  color: rgba(250, 242, 228, 0.84);
  font-size: 0.78rem;
  letter-spacing: 0.24em;
  text-transform: uppercase;
}

.room-mosaic-hero__title {
  margin: 0;
  color: #fff6ea;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.6rem, 5vw, 4.6rem);
  line-height: 0.92;
}

.room-mosaic-hero__description {
  margin: 1.2rem auto 0;
  max-width: 42rem;
  color: rgba(250, 242, 228, 0.88);
  line-height: 1.9;
}

.room-mosaic-main-image {
  width: min(calc(100% - 2rem), 70rem);
  margin: -7.5rem auto 2.5rem;
  overflow: hidden;
  border-radius: 1.7rem;
  box-shadow: 0 28px 56px rgba(48, 34, 25, 0.14);
}

.room-mosaic-main-image img {
  width: 100%;
  display: block;
}

.room-mosaic-content {
  width: min(calc(100% - 2rem), 74rem);
  margin: 0 auto;
  display: grid;
  gap: 2rem;
  grid-template-columns: minmax(0, 1.8fr) minmax(18rem, 0.92fr);
}

.room-mosaic-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.room-mosaic-card {
  overflow: hidden;
  border-radius: 1.35rem;
  background: rgba(255, 251, 244, 0.9);
  border: 1px solid rgba(111, 117, 83, 0.1);
}

.room-mosaic-card img {
  width: 100%;
  aspect-ratio: 1.52;
  display: block;
  object-fit: cover;
}

.room-mosaic-card__title {
  padding: 0.95rem 1rem 1.05rem;
  color: #30231b;
  text-align: center;
  line-height: 1.55;
}

.room-mosaic-sidebar {
  align-self: start;
  display: grid;
  gap: 1.4rem;
}

.room-mosaic-sidebar__copy {
  padding: 1.5rem;
  border-radius: 1.35rem;
  background: rgba(255, 251, 244, 0.74);
  border: 1px solid rgba(111, 117, 83, 0.08);
}

.room-mosaic-sidebar__copy p {
  margin: 0;
  color: rgba(48, 35, 27, 0.78);
  line-height: 1.8;
}

.room-mosaic-sidebar__copy p + p {
  margin-top: 0.75rem;
}

.room-mosaic-sidebar__list {
  margin: 0;
  padding: 0;
  list-style: none;
  display: grid;
  gap: 0.75rem;
}

.room-mosaic-sidebar__list li {
  padding: 0.9rem 1rem;
  border-radius: 999px;
  background: rgba(237, 229, 215, 0.84);
  color: #4b3a2c;
}

.room-mosaic-cta {
  width: min(calc(100% - 2rem), 24rem);
  margin: 2.2rem auto 0;
  text-align: center;
}

.room-mosaic-cta__label {
  margin-bottom: 0.8rem;
  color: #8b6a3f;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  font-size: 0.74rem;
}

.room-mosaic-cta__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 100%;
  padding: 0.95rem 1.35rem;
  border-radius: 999px;
  background: #6d7351;
  color: #fffaf2;
  text-decoration: none;
  transition: transform 0.2s ease, background-color 0.2s ease;
}

.room-mosaic-cta__button:hover {
  transform: translateY(-1px);
  background: #5b6244;
}

@media (max-width: 991px) {
  .room-mosaic-hero {
    min-height: 28rem;
    padding-bottom: 8rem;
  }

  .room-mosaic-content {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .room-mosaic-grid {
    grid-template-columns: 1fr;
  }
}
</style>
