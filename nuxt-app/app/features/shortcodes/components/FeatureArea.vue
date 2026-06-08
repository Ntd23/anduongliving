<script setup lang="ts">
import { computed } from "vue";
import {
  parseFeatureAreaBlock,
  type FeatureAreaSectionData,
  type ShortcodeBlock,
} from "~/features/shortcodes/core";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { useResolvedCmsAsset } from "~/composables/useResolvedCmsAsset";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed<FeatureAreaSectionData>(() => parseFeatureAreaBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const resolveAsset = useResolvedCmsAsset();
const action = computed(() => resolveLink(section.value.action?.href));

const sectionStyle = computed(() => {
  const style: Record<string, string> = {};

  if (section.value.backgroundColor) {
    style.background = section.value.backgroundColor;
  }

  if (section.value.backgroundImage?.src) {
    const backgroundSrc = resolveAsset(section.value.backgroundImage.src) || section.value.backgroundImage.src;

    style.backgroundImage = [
      "linear-gradient(180deg, rgba(252, 250, 246, 0.76), rgba(243, 236, 223, 0.88))",
      `url('${backgroundSrc}')`,
    ].join(", ");
  }

  return Object.keys(style).length ? style : undefined;
});
</script>

<template>
  <section class="shortcode-feature-v2" :style="sectionStyle">
    <div class="container">
      <div
        v-if="section.quote || section.quoteAuthor"
        class="feature-quote"
      >
        <span class="quote-mark left">"</span>

        <div class="quote-content">
          <h3 v-if="section.quote" class="quote-text">
            {{ section.quote }}
          </h3>

          <p v-if="section.quoteAuthor" class="quote-author">
            <span class="quote-line" />
            {{ section.quoteAuthor }}
          </p>
        </div>

        <span class="quote-mark right">"</span>
      </div>

      <div
        v-if="
          section.image?.src ||
          section.title ||
          section.subtitle ||
          section.description
        "
        class="feature-grid"
      >
        <div v-if="section.image?.src" class="feature-main-image">
          <img
            :src="resolveAsset(section.image.src) || section.image.src"
            :alt="section.image.alt || section.title || 'Feature image'"
            loading="lazy"
          >
        </div>

        <div class="feature-right">
          <div class="feature-copy">
            <div class="feature-copy__glass">
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
                v-if="action?.isInternal && section.action?.label"
                :to="action.href"
                class="feature-button"
              >
                {{ section.action.label }}
              </NuxtLink>
              <a
                v-else-if="action && section.action?.label"
                :href="action.href"
                class="feature-button"
              >
                {{ section.action.label }}
              </a>
            </div>
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
  padding: clamp(4.5rem, 8vw, 6rem) 0;
  background:
    radial-gradient(circle at bottom left, rgba(185, 130, 90, 0.1), transparent 28%),
    linear-gradient(180deg, #fcfaf6, #f3ecdf);
  background-position: center;
  background-size: cover;
}

.container {
  width: min(1120px, calc(100% - 48px));
  margin: 0 auto;
}

/* â”€â”€ Quote â”€â”€ */
.feature-quote {
  display: grid;
  grid-template-columns: 50px minmax(0, 760px) 50px;
  justify-content: center;
  align-items: start;
  gap: 16px;
  margin-bottom: clamp(2.5rem, 5vw, 3.5rem);
  padding: clamp(1.5rem, 3vw, 2.5rem);
  border: 1px solid rgba(111, 117, 83, 0.1);
  border-radius: 2rem;
  background: rgba(255, 252, 246, 0.72);
  backdrop-filter: blur(8px);
  box-shadow: 0 20px 48px rgba(47, 36, 29, 0.06);
}

.quote-mark {
  color: var(--retreat-clay, #9c633d);
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
  color: var(--retreat-ink, #1f241d);
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.9rem, 2.8vw, 3rem);
  line-height: 1.28;
  font-weight: 600;
}

.quote-author {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  margin: 22px 0 0;
  color: var(--retreat-clay, #9c633d);
  font-size: 15px;
  font-weight: 700;
  text-transform: uppercase;
}

.quote-line {
  width: 28px;
  height: 1px;
  background: rgba(31, 36, 29, 0.45);
}

/* â”€â”€ Grid â”€â”€ */
.feature-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: clamp(2rem, 4vw, 3rem);
  align-items: stretch;
  min-height: clamp(34rem, 58vw, 43rem);
}

.feature-main-image {
  overflow: hidden;
  border-radius: 1.75rem;
  box-shadow: 0 28px 72px rgba(47, 36, 29, 0.12);
}

.feature-main-image img {
  display: block;
  width: 100%;
  height: 100%;
  min-height: 100%;
  object-fit: cover;
}

.feature-right {
  display: flex;
  min-height: 100%;
}

/* â”€â”€ Glass panel â”€â”€ */
.feature-copy {
  display: flex;
  width: 100%;
}

.feature-copy__glass {
  display: flex;
  width: 100%;
  min-height: 100%;
  flex-direction: column;
  justify-content: center;
  padding: clamp(1.75rem, 4vw, 3rem);
  border: 1px solid rgba(111, 117, 83, 0.1);
  border-radius: 1.75rem;
  background: rgba(255, 252, 246, 0.72);
  backdrop-filter: blur(8px);
  box-shadow: 0 20px 48px rgba(47, 36, 29, 0.06);
}

.feature-subtitle {
  margin: 0 0 0.65rem;
  color: var(--retreat-clay, #8a5a3c);
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.feature-title {
  margin: 0;
  color: var(--retreat-ink, #2d2018);
  font-size: clamp(2.4rem, 3.5vw, 4rem);
  line-height: 1.04;
  font-weight: 600;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
}

.feature-description {
  margin: 1rem 0 0;
  max-width: 30rem;
  color: rgba(69, 53, 43, 0.82);
  font-size: 1rem;
  line-height: 1.78;
}

.feature-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3rem;
  margin-top: 1.25rem;
  padding: 0 1.25rem;
  border-radius: 999px;
  background: linear-gradient(135deg, var(--retreat-clay, #9c633d), #8a5530);
  color: #fffdf9;
  font-size: 0.84rem;
  font-weight: 700;
  text-decoration: none;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  box-shadow: 0 14px 28px rgba(156, 99, 61, 0.18);
  transition: box-shadow 0.2s ease;
}

.feature-button:hover {
  box-shadow: 0 18px 36px rgba(156, 99, 61, 0.24);
}

.feature-fallback {
  min-height: 120px;
}

@media (max-width: 1023px) {
  .feature-quote {
    grid-template-columns: 32px minmax(0, 1fr) 32px;
  }

  .quote-mark {
    font-size: 42px;
  }

  .feature-grid {
    grid-template-columns: 1fr;
    min-height: auto;
  }

  .feature-right {
    min-height: auto;
    padding-top: 0;
  }

  .feature-main-image img,
  .feature-copy__glass {
    min-height: clamp(28rem, 62vw, 36rem);
  }
}

@media (max-width: 767px) {
  .container {
    width: min(100%, calc(100% - 24px));
  }

  .quote-text {
    font-size: 1.5rem;
  }

  .feature-description {
    font-size: 0.95rem;
  }
}
</style>



