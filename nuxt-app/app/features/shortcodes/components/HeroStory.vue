<script setup lang="ts">
import { parseHeroStoryBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseHeroStoryBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const descriptionLines = computed(() =>
  (section.value.description || "")
    .split(/\n{2,}/)
    .map((line) => line.trim())
    .filter(Boolean),
);
</script>

<template>
  <section
    v-if="section.title || section.backgroundImage?.src"
    class="shortcode-hero-story"
  >
    <div
      class="hero-story__backdrop"
      :style="section.backgroundImage?.src ? { backgroundImage: `url(${section.backgroundImage.src})` } : undefined"
    />
    <div class="hero-story__veil" />

    <div class="container hero-story__shell">
      <div class="hero-story__panel">
        <p v-if="section.subtitle" class="hero-story__eyebrow shortcode-narrative-eyebrow">
          {{ section.subtitle }}
        </p>
        <h2 v-if="section.title" class="hero-story__title shortcode-narrative-title">
          {{ section.title }}
        </h2>

        <div v-if="descriptionLines.length" class="hero-story__description">
          <p v-for="(line, index) in descriptionLines" :key="`${index}-${line.slice(0, 24)}`">
            {{ line }}
          </p>
        </div>

        <NuxtLink
          v-if="section.action"
          :to="section.action.href"
          class="hero-story__cta"
        >
          {{ section.action.label }}
        </NuxtLink>
      </div>

      <div v-if="section.foregroundImage?.src" class="hero-story__aside">
        <img
          :src="section.foregroundImage.src"
          :alt="section.foregroundImage.alt || section.title || 'Story image'"
          loading="lazy"
        >
      </div>
    </div>
  </section>

  <section v-else class="shortcode-hero-story">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-hero-story {
  position: relative;
  overflow: hidden;
  min-height: clamp(34rem, 78vh, 52rem);
  color: #fff9f0;
}

.hero-story__backdrop,
.hero-story__veil {
  position: absolute;
  inset: 0;
}

.hero-story__backdrop {
  background:
    linear-gradient(180deg, rgba(21, 16, 11, 0.18), rgba(21, 16, 11, 0.32)),
    linear-gradient(135deg, #6f7553, #3b4531);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  transform: scale(1.03);
}

.hero-story__veil {
  background:
    radial-gradient(circle at 20% 20%, rgba(185, 130, 90, 0.18), transparent 30%),
    linear-gradient(90deg, rgba(21, 16, 11, 0.7) 0%, rgba(21, 16, 11, 0.36) 45%, rgba(21, 16, 11, 0.08) 100%);
}

.hero-story__shell {
  position: relative;
  display: grid;
  align-items: end;
  min-height: inherit;
  padding-top: 5rem;
  padding-bottom: 5rem;
}

.hero-story__panel {
  position: relative;
  z-index: 1;
  max-width: min(42rem, 92vw);
  padding: clamp(1.75rem, 4vw, 3rem);
  border: 1px solid rgba(248, 243, 234, 0.18);
  border-radius: 1.9rem;
  background: rgba(47, 36, 29, 0.38);
  box-shadow: 0 30px 80px rgba(0, 0, 0, 0.24);
  backdrop-filter: blur(10px);
}

.hero-story__eyebrow {
  margin: 0 0 0.75rem;
  color: #e3c8a8;
}

.hero-story__title {
  margin: 0;
  font-size: clamp(3rem, 6vw, 5.75rem);
  line-height: 0.95;
}

.hero-story__description {
  max-width: 34rem;
  margin-top: 1.25rem;
  color: rgba(255, 249, 240, 0.84);
  font-size: 1.04rem;
}

.hero-story__description p {
  margin: 0 0 0.95rem;
}

.hero-story__cta {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3.35rem;
  margin-top: 1.2rem;
  padding: 0 1.45rem;
  border-radius: 999px;
  background: linear-gradient(135deg, var(--retreat-clay), #9f6f49);
  color: #fff9f0;
  font-weight: 700;
  text-decoration: none;
  box-shadow: 0 18px 40px rgba(185, 130, 90, 0.22);
}

.hero-story__aside {
  position: absolute;
  right: 1rem;
  bottom: 4rem;
  width: min(28vw, 16rem);
  overflow: hidden;
  border: 8px solid rgba(255, 252, 246, 0.88);
  border-radius: 1.5rem;
  box-shadow: 0 28px 60px rgba(18, 9, 5, 0.28);
}

.hero-story__aside img {
  display: block;
  width: 100%;
  aspect-ratio: 4 / 5;
  object-fit: cover;
}

@media (max-width: 1023px) {
  .hero-story__aside {
    position: relative;
    right: auto;
    bottom: auto;
    width: min(72vw, 18rem);
    margin: 1.5rem 0 0 auto;
  }
}

@media (max-width: 767px) {
  .shortcode-hero-story {
    min-height: 32rem;
  }

  .hero-story__veil {
    background:
      radial-gradient(circle at 20% 20%, rgba(185, 130, 90, 0.16), transparent 30%),
      linear-gradient(180deg, rgba(21, 16, 11, 0.58) 0%, rgba(21, 16, 11, 0.3) 100%);
  }

  .hero-story__panel {
    max-width: 100%;
  }
}
</style>




