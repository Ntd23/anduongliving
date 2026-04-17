<script setup lang="ts">
import { parseFeatureBlock, type ShortcodeBlock } from "~/utils/shortcode";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseFeatureBlock(props.block.raw));
const sectionStyle = computed(() =>
  section.value.backgroundColor
    ? { background: section.value.backgroundColor }
    : undefined,
);
</script>

<template>
  <section
    v-if="section.title || section.subtitle || section.image?.src"
    class="shortcode-feature"
    :style="sectionStyle"
  >
    <div v-if="section.backgroundImage?.src" class="feature-decor">
      <img
        :src="section.backgroundImage.src"
        :alt="section.backgroundImage.alt || section.title || 'Feature decoration'"
        loading="lazy"
      >
    </div>

    <div class="container">
      <div class="feature-layout">
        <div v-if="section.image?.src" class="feature-media">
          <img
            :src="section.image.src"
            :alt="section.image.alt || section.title || 'Feature image'"
            loading="lazy"
          >
        </div>

        <div class="feature-copy">
          <p v-if="section.subtitle" class="feature-eyebrow shortcode-narrative-eyebrow">
            {{ section.subtitle }}
          </p>
          <h2 v-if="section.title" class="feature-title shortcode-narrative-title">
            {{ section.title }}
          </h2>
          <p v-if="section.description" class="feature-description">
            {{ section.description }}
          </p>

          <NuxtLink
            v-if="section.action"
            :to="section.action.href"
            class="feature-cta"
          >
            {{ section.action.label }}
          </NuxtLink>
        </div>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-feature">
    <div v-html="block.raw" />
  </section>
</template>

<style scoped>
.shortcode-feature {
  position: relative;
  overflow: hidden;
  padding: 5.5rem 0;
  background:
    linear-gradient(180deg, rgba(248, 243, 234, 0.94), rgba(237, 228, 214, 0.9));
}

.feature-decor {
  position: absolute;
  top: 1rem;
  right: clamp(-1rem, 1vw, 1rem);
  opacity: 0.25;
  pointer-events: none;
}

.feature-layout {
  position: relative;
  display: grid;
  gap: 2.5rem;
  align-items: center;
}

.feature-media {
  position: relative;
  overflow: hidden;
  border-radius: 1.75rem;
  box-shadow: 0 28px 72px rgba(47, 36, 29, 0.12);
}

.feature-media::after {
  content: "";
  position: absolute;
  inset: auto 0 0;
  height: 38%;
  background: linear-gradient(180deg, transparent, rgba(47, 36, 29, 0.1));
  pointer-events: none;
}

.feature-media img {
  display: block;
  width: 100%;
  aspect-ratio: 4 / 5;
  object-fit: cover;
}

.feature-copy {
  max-width: 38rem;
}

.feature-eyebrow {
  margin: 0 0 0.75rem;
  color: var(--retreat-olive);
}

.feature-title {
  margin: 0;
  color: var(--retreat-ink);
  font-size: clamp(2rem, 4.8vw, 3.85rem);
  line-height: 1.04;
}

.feature-description {
  margin: 1.25rem 0 0;
  color: rgba(47, 36, 29, 0.78);
  font-size: 1.02rem;
}

.feature-cta {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3.45rem;
  margin-top: 1.75rem;
  padding: 0 1.45rem;
  border: 1px solid rgba(111, 117, 83, 0.22);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.78);
  color: var(--retreat-ink);
  font-weight: 700;
  text-decoration: none;
  box-shadow: 0 16px 32px rgba(47, 36, 29, 0.08);
  transition:
    transform 0.2s ease,
    border-color 0.2s ease,
    background-color 0.2s ease;
}

.feature-cta:hover {
  transform: translateY(-1px);
  border-color: rgba(111, 117, 83, 0.4);
  background: #fff;
}

@media (min-width: 1024px) {
  .feature-layout {
    grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
  }
}
</style>
