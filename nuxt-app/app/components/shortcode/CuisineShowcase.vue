<script setup lang="ts">
import { parseCuisineShowcaseBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useResolvedCmsAsset } from "~/composables/useResolvedCmsAsset";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { parseShortcodeAttributes } from "~/utils/shortcode/core";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseCuisineShowcaseBlock(props.block.raw));
const rawAttributes = computed(() => parseShortcodeAttributes(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const resolveAsset = useResolvedCmsAsset();
const rawButtonLabel = computed(() => rawAttributes.value.button_label?.trim() || null);
const rawButtonUrl = computed(() => rawAttributes.value.button_url?.trim() || null);
const actionSourceHref = computed(() => section.value.action?.href || section.value.buttonUrl || rawButtonUrl.value || null);
const action = computed(() => resolveLink(actionSourceHref.value));
const actionLabel = computed(() => section.value.action?.label || section.value.buttonLabel || rawButtonLabel.value);
const actionHref = computed(() => action.value?.href || actionSourceHref.value);
const actionIsInternal = computed(() => Boolean(action.value?.isInternal && action.value?.href));
</script>

<template>
  <section
    v-if="section.images.length && (section.title || section.description || section.sectionLabel || actionLabel)"
    class="shortcode-cuisine-native"
  >
    <div class="cuisine-shell">
      <header v-if="section.title" class="cuisine-header">
        <h2 class="cuisine-header__title">{{ section.title }}</h2>
      </header>

      <div class="cuisine-stage">
        <div v-if="section.images.length" class="cuisine-gallery">
          <figure
            v-for="(image, index) in section.images.slice(0, 2)"
            :key="`${image.src}-${index}`"
            class="cuisine-gallery__item"
            :class="[`is-layer-${index + 1}`]"
          >
            <img :src="resolveAsset(image.src) || image.src" :alt="image.alt || `Cuisine image ${index + 1}`">
          </figure>
        </div>

        <div v-if="section.description || actionLabel" class="cuisine-copy">
          <p v-if="section.description" class="cuisine-copy__description">{{ section.description }}</p>

          <NuxtLink
            v-if="actionIsInternal && actionHref && actionLabel"
            :to="actionHref"
            class="cuisine-copy__button"
          >
            <span class="cuisine-copy__button-text">{{ actionLabel }}</span>
          </NuxtLink>

          <a
            v-else-if="actionHref && actionLabel"
            :href="actionHref"
            class="cuisine-copy__button"
          >
            <span class="cuisine-copy__button-text">{{ actionLabel }}</span>
          </a>

          <button
            v-else-if="actionLabel"
            type="button"
            class="cuisine-copy__button cuisine-copy__button--static"
            aria-disabled="true"
          >
            <span class="cuisine-copy__button-text">{{ actionLabel }}</span>
          </button>
        </div>
      </div>

      <footer v-if="section.sectionLabel" class="cuisine-footer">
        <p class="cuisine-footer__label">
          {{ section.sectionLabel }}
        </p>
      </footer>
    </div>
  </section>

  <section v-else class="shortcode-cuisine-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-cuisine-native {
  background: linear-gradient(180deg, #fcf8f1, #f4ecdf);
  padding: clamp(4rem, 8vw, 7rem) 0;
}

.cuisine-shell {
  width: min(calc(100% - 2rem), 76rem);
  margin: 0 auto;
}

.cuisine-shell {
  --cuisine-display-font: "Cormorant Garamond", "Noto Serif", "Noto Serif JP", "Hiragino Mincho ProN", "Yu Mincho", Georgia, serif;
  --cuisine-copy-font: "Noto Serif", "Noto Serif JP", "Iowan Old Style", "Palatino Linotype", "Book Antiqua", "Hiragino Kaku Gothic ProN", "Yu Gothic", Georgia, serif;
}

.cuisine-header {
  max-width: 100%;
  margin: 0 auto;
  text-align: center;
}

.cuisine-header__title {
  margin: 0;
  white-space: nowrap;
  color: #2f241d;
  font-family: var(--cuisine-display-font);
  font-size: clamp(2.6rem, 4.2vw, 4.9rem);
  font-weight: 600;
  line-height: 1;
  letter-spacing: -0.018em;
  text-wrap: balance;
}

.cuisine-stage {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  gap: clamp(1.5rem, 3.8vw, 3.5rem);
  align-items: stretch;
  margin-top: 2.4rem;
}

.cuisine-gallery {
  position: relative;
  min-height: clamp(23.5rem, 45vw, 35rem);
  padding: 1rem 2.2rem 1.4rem 0;
  width: 100%;
}

.cuisine-gallery__item {
  position: absolute;
  margin: 0;
  overflow: hidden;
  border-radius: 2rem;
  background: #fff;
  border: 1px solid rgba(255, 250, 241, 0.75);
  box-shadow:
    0 28px 60px rgba(45, 32, 24, 0.1),
    0 10px 24px rgba(45, 32, 24, 0.08);
}

.cuisine-gallery__item.is-layer-1 {
  inset: 0 16% 7% 0;
  z-index: 1;
}

.cuisine-gallery__item.is-layer-2 {
  inset: 14% 0 0 32%;
  z-index: 2;
}

.cuisine-gallery__item img {
  width: 100%;
  height: 100%;
  display: block;
  aspect-ratio: 4 / 5;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.cuisine-gallery__item:hover img {
  transform: scale(1.04);
}

.cuisine-copy {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  gap: 1.35rem;
  min-height: clamp(23.5rem, 45vw, 35rem);
  width: 100%;
  padding: clamp(1.6rem, 3vw, 2.5rem) clamp(2rem, 3.6vw, 3rem);
  border-radius: 2rem;
  background: rgba(255, 251, 245, 0.56);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255, 255, 255, 0.68);
  box-shadow: 0 24px 55px rgba(73, 53, 35, 0.1);
}

.cuisine-copy__description {
  max-width: none;
  margin: 0;
  color: rgba(47, 36, 29, 0.82);
  font-family: var(--cuisine-copy-font);
  font-size: 1.035rem;
  font-weight: 400;
  letter-spacing: 0.002em;
  line-height: 1.92;
  text-align: left;
  text-wrap: pretty;
}

.cuisine-footer {
  margin-top: 3rem;
}

.cuisine-footer__label {
  margin: 0;
  color: #8a6e48;
  font-family: var(--cuisine-copy-font);
  letter-spacing: 0.24em;
  text-transform: uppercase;
  font-size: 0.78rem;
  font-weight: 600;
}

.cuisine-copy__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 14rem;
  min-height: 3.5rem;
  padding: 0 1.8rem;
  border-radius: 999px;
  background: #5f6945;
  color: #fffaf1;
  font-family: var(--cuisine-copy-font);
  font-size: 0.95rem;
  font-weight: 600;
  letter-spacing: 0.03em;
  text-decoration: none;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 14px 30px rgba(95, 105, 69, 0.22);
  transition: all 0.25s ease;
}

.cuisine-copy__button:hover {
  transform: translateY(-2px);
  background: #4f5839;
}

.cuisine-copy__button--static {
  cursor: default;
}

.cuisine-copy__button--static:hover {
  transform: none;
  background: #5f6945;
}

.cuisine-copy__button-text {
  display: inline-block;
  color: inherit;
  line-height: 1.2;
  white-space: nowrap;
}

@media (max-width: 1024px) {
  .cuisine-shell {
    width: min(calc(100% - 2rem), 64rem);
  }

  .cuisine-stage {
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 1.5rem;
  }

  .cuisine-copy__description {
    font-size: 1rem;
    line-height: 1.84;
  }
}

@media (max-width: 768px) {
  .shortcode-cuisine-native {
    padding: 4rem 0;
  }

  .cuisine-header__title {
    white-space: normal;
    font-size: clamp(2.45rem, 10vw, 3.85rem);
  }

  .cuisine-stage {
    grid-template-columns: 1fr;
    margin-top: 2.25rem;
  }

  .cuisine-gallery {
    min-height: 22rem;
    padding: 0 1rem 1.15rem 0;
  }

  .cuisine-gallery__item.is-layer-1 {
    inset: 0 12% 10% 0;
  }

  .cuisine-gallery__item.is-layer-2 {
    inset: 18% 0 0 30%;
  }

  .cuisine-copy__description {
    max-width: none;
    text-align: left;
    line-height: 1.8;
  }

  .cuisine-copy {
    min-height: auto;
    padding: 1.5rem 1.25rem;
  }

  .cuisine-footer {
    margin-top: 2.4rem;
  }

  .cuisine-copy__button {
    width: 100%;
    max-width: 20rem;
  }
}

@media (max-width: 390px) {
  .cuisine-shell {
    width: calc(100% - 1.25rem);
  }

  .cuisine-copy__description {
    font-size: 0.96rem;
  }

  .cuisine-gallery {
    min-height: 19.5rem;
    padding-right: 0.65rem;
  }
}
</style>
