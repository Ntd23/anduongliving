<script setup lang="ts">
import { parseSpecialStoryShowcaseBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseSpecialStoryShowcaseBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();

const action = computed(() => resolveLink(section.value.action?.href));
const actionLabel = computed(() => section.value.action?.label || null);

const sectionStyle = computed(() =>
  section.value.backgroundImage?.src
    ? { backgroundImage: `url('${section.value.backgroundImage.src}')` }
    : undefined,
);
</script>

<template>
  <section
    v-if="section.cards.length || section.description || actionLabel"
    class="shortcode-special-story-native"
    :style="sectionStyle"
  >
    <div class="special-story-overlay" />

    <div class="special-story-shell">
      <div v-if="section.decorativeText" class="special-story-deco">
        {{ section.decorativeText }}
      </div>

      <p v-if="section.sectionLabel" class="special-story-label">
        {{ section.sectionLabel }}
      </p>

      <p v-if="section.description" class="special-story-description">
        {{ section.description }}
      </p>

      <div v-if="section.cards.length" class="special-story-cards">
        <article
          v-for="(card, index) in section.cards"
          :key="`${card.title || 'story'}-${index}`"
          class="special-story-card"
        >
          <img
            v-if="card.image?.src"
            :src="card.image.src"
            :alt="card.image.alt || card.title || 'Story image'"
            loading="lazy"
          >
          <div v-if="card.title" class="special-story-card__title">
            {{ card.title }}
          </div>
        </article>
      </div>

      <div v-if="section.navEntries.length" class="special-story-nav">
        <article
          v-for="(entry, index) in section.navEntries"
          :key="`${entry.badgeVolume || 'vol'}-${index}`"
          class="special-story-nav__entry"
        >
          <div v-if="index === 0 && entry.text" class="special-story-nav__text">
            {{ entry.text }}
          </div>

          <div class="special-story-nav__badge">
            <span v-if="entry.badgeTop" class="special-story-nav__badge-top">
              {{ entry.badgeTop }}
            </span>
            <span v-if="entry.badgeVolume" class="special-story-nav__badge-volume">
              {{ entry.badgeVolume }}
            </span>
          </div>

          <div v-if="index !== 0 && entry.text" class="special-story-nav__text">
            {{ entry.text }}
          </div>
        </article>
      </div>

      <NuxtLink
        v-if="action?.isInternal && actionLabel"
        :to="action.href"
        class="special-story-button"
      >
        {{ actionLabel }}
      </NuxtLink>
      <a
        v-else-if="action && actionLabel"
        :href="action.href"
        class="special-story-button"
      >
        {{ actionLabel }}
      </a>
    </div>
  </section>

  <section v-else class="shortcode-special-story-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-special-story-native {
  position: relative;
  overflow: hidden;
  padding: 4rem 1.25rem 3.5rem;
  background-color: #1e1d18;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}

.special-story-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(30, 29, 24, 0.68), rgba(30, 29, 24, 0.84));
}

.special-story-shell {
  position: relative;
  z-index: 1;
  width: min(100%, 70rem);
  margin: 0 auto;
  text-align: center;
  color: #fff;
}

.special-story-deco {
  color: rgba(255, 255, 255, 0.06);
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(4rem, 12vw, 10rem);
  font-style: italic;
  line-height: 0.82;
}

.special-story-label {
  margin: 0 0 1.5rem;
  color: rgba(255, 247, 236, 0.68);
  letter-spacing: 0.24em;
  font-size: 0.72rem;
  text-transform: uppercase;
}

.special-story-description {
  width: min(100%, 42rem);
  margin: 0 auto 2.4rem;
  color: rgba(255, 247, 236, 0.8);
  line-height: 2;
}

.special-story-cards {
  display: grid;
  gap: 0.5rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  margin-bottom: 2rem;
}

.special-story-card {
  position: relative;
  overflow: hidden;
  border-radius: 1.35rem;
  min-height: 21rem;
}

.special-story-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.special-story-card::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(20, 18, 12, 0.08), rgba(20, 18, 12, 0.52));
}

.special-story-card__title {
  position: absolute;
  right: 1.1rem;
  bottom: 1.1rem;
  z-index: 1;
  color: #fff8ed;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.2rem, 2.2vw, 1.75rem);
  writing-mode: vertical-rl;
  text-orientation: mixed;
}

.special-story-nav {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  margin-bottom: 2rem;
}

.special-story-nav__entry {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  padding: 1rem 1.2rem;
  border-radius: 1.2rem;
  background: rgba(255, 255, 255, 0.04);
}

.special-story-nav__text {
  color: rgba(255, 247, 236, 0.72);
  line-height: 1.75;
}

.special-story-nav__badge {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-width: 5rem;
  padding: 0.7rem 0.9rem;
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 1rem;
}

.special-story-nav__badge-top {
  font-size: 0.58rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: rgba(255, 247, 236, 0.52);
}

.special-story-nav__badge-volume {
  margin-top: 0.25rem;
  color: rgba(255, 247, 236, 0.82);
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.special-story-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 14rem;
  padding: 0.95rem 1.4rem;
  border-radius: 999px;
  border: 1px solid rgba(255, 247, 236, 0.26);
  color: #fff8ed;
  text-decoration: none;
  transition: background-color 0.2s ease, border-color 0.2s ease;
}

.special-story-button:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 247, 236, 0.42);
}

@media (max-width: 767px) {
  .special-story-cards,
  .special-story-nav {
    grid-template-columns: 1fr;
  }

  .special-story-card {
    min-height: 15rem;
  }
}
</style>
