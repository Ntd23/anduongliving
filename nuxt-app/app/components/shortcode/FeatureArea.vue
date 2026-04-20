<script setup lang="ts">
import { computed } from "vue";
import {
  parseFeatureAreaBlock,
  type FeatureAreaSectionData,
  type ShortcodeBlock,
} from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed<FeatureAreaSectionData>(() => parseFeatureAreaBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);

const sectionStyle = computed(() =>
  section.value.backgroundColor
    ? { background: section.value.backgroundColor }
    : undefined,
);
</script>

<template>
  <section class="shortcode-feature-v2" :style="sectionStyle">
    <div class="container">
      <div
        v-if="section.quote || section.quoteAuthor"
        class="feature-quote"
      >
        <span class="quote-mark left">“</span>

        <div class="quote-content">
          <h3 v-if="section.quote" class="quote-text">
            {{ section.quote }}
          </h3>

          <p v-if="section.quoteAuthor" class="quote-author">
            <span class="quote-line"></span>
            {{ section.quoteAuthor }}
          </p>
        </div>

        <span class="quote-mark right">”</span>
      </div>

      <div
        v-if="
          section.image?.src ||
          section.title ||
          section.subtitle ||
          section.description ||
          section.secondaryImage?.src
        "
        class="feature-grid"
      >
        <div v-if="section.image?.src" class="feature-main-image">
          <img
            :src="section.image.src"
            :alt="section.image.alt || section.title || 'Feature image'"
            loading="lazy"
          >
        </div>

        <div class="feature-right">
          <div class="feature-copy">
            <p v-if="section.subtitle" class="feature-subtitle">
              {{ section.subtitle }}
            </p>

            <h2 v-if="section.title" class="feature-title">
              {{ section.title }}
            </h2>

            <p v-if="section.description" class="feature-description">
              {{ section.description }}
            </p>

            <NuxtLink
              v-if="section.action?.href && section.action?.label"
              :to="section.action.href"
              class="feature-button"
            >
              {{ section.action.label }}
            </NuxtLink>
          </div>

          <div v-if="section.secondaryImage?.src" class="feature-side-image">
            <img
              :src="section.secondaryImage.src"
              :alt="section.secondaryImage.alt || section.title || 'Secondary image'"
              loading="lazy"
            >
          </div>
        </div>
      </div>

      <div
        v-else
        class="feature-fallback"
        v-html="sanitizedHtml"
      ></div>
    </div>
  </section>
</template>

<style scoped>
.shortcode-feature-v2 {
  position: relative;
  overflow: hidden;
  padding: 72px 0 64px;
  background: #f3efeb;
}

.container {
  width: min(1120px, calc(100% - 48px));
  margin: 0 auto;
}

.feature-quote {
  display: grid;
  grid-template-columns: 50px minmax(0, 760px) 50px;
  justify-content: center;
  align-items: start;
  gap: 16px;
  margin-bottom: 56px;
}

.quote-mark {
  color: #9c633d;
  font-size: 72px;
  line-height: 1;
  font-weight: 700;
  text-align: center;
}

.quote-content {
  text-align: center;
}

.quote-text {
  margin: 0;
  color: #1f241d;
  font-size: clamp(1.9rem, 2.8vw, 3rem);
  line-height: 1.28;
  font-weight: 600;
}

.quote-author {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  margin: 22px 0 0;
  color: #9c633d;
  font-size: 15px;
  font-weight: 700;
  text-transform: uppercase;
}

.quote-line {
  width: 28px;
  height: 1px;
  background: rgba(31, 36, 29, 0.45);
}

.feature-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.02fr) minmax(0, 1fr);
  gap: 38px;
  align-items: start;
}

.feature-main-image,
.feature-side-image {
  overflow: hidden;
}

.feature-main-image img,
.feature-side-image img {
  display: block;
  width: 100%;
  height: auto;
  object-fit: cover;
}

.feature-main-image img {
  aspect-ratio: 4 / 5;
}

.feature-right {
  display: grid;
  grid-template-rows: auto auto;
  gap: 28px;
  align-content: start;
  padding-top: 42px;
}

.feature-copy {
  max-width: 480px;
}

.feature-subtitle {
  margin: 0 0 10px;
  color: #8a5a3c;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

.feature-title {
  margin: 0;
  color: #7b4d35;
  font-size: clamp(2.4rem, 3.5vw, 4rem);
  line-height: 1.04;
  font-weight: 500;
  font-family: "Times New Roman", Georgia, serif;
}

.feature-description {
  margin: 18px 0 0;
  color: rgba(69, 53, 43, 0.88);
  font-size: 18px;
  line-height: 1.78;
}

.feature-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 44px;
  margin-top: 22px;
  padding: 0 18px;
  border: 1px solid rgba(123, 77, 53, 0.45);
  border-radius: 999px;
  background: transparent;
  color: #7b4d35;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  text-transform: uppercase;
  transition:
    background-color 0.2s ease,
    color 0.2s ease,
    border-color 0.2s ease,
    transform 0.2s ease;
}

.feature-button:hover {
  transform: translateY(-1px);
  background: #7b4d35;
  color: #fff;
  border-color: #7b4d35;
}

.feature-side-image img {
  aspect-ratio: 16 / 9;
}

.feature-fallback {
  min-height: 120px;
}

@media (max-width: 1023px) {
  .feature-quote {
    grid-template-columns: 32px minmax(0, 1fr) 32px;
    margin-bottom: 38px;
  }

  .quote-mark {
    font-size: 42px;
  }

  .feature-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .feature-right {
    padding-top: 0;
  }
}

@media (max-width: 767px) {
  .container {
    width: min(100%, calc(100% - 24px));
  }

  .shortcode-feature-v2 {
    padding: 48px 0;
  }

  .quote-text {
    font-size: 1.5rem;
  }

  .feature-description {
    font-size: 16px;
    line-height: 1.7;
  }
}
</style>
