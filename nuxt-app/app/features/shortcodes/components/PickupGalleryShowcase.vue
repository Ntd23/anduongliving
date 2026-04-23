<script setup lang="ts">
import { parsePickupGalleryShowcaseBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parsePickupGalleryShowcaseBlock(props.block.raw));
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
    v-if="section.galleryImages.length || section.title || actionLabel"
    class="shortcode-pickup-gallery-native"
    :style="sectionStyle"
  >
    <div class="pickup-gallery-overlay" />

    <div class="pickup-gallery-shell">
      <header class="pickup-gallery-header">
        <p v-if="section.pretitle" class="pickup-gallery-header__pretitle">
          {{ section.pretitle }}
        </p>
        <h2 v-if="section.title" class="pickup-gallery-header__title">
          {{ section.title }}
        </h2>
      </header>

      <div class="pickup-gallery-body">
        <div v-if="section.galleryImages.length" class="pickup-gallery-strip">
          <div
            v-for="(image, index) in section.galleryImages"
            :key="`${image.src}-${index}`"
            class="pickup-gallery-strip__item"
          >
            <img
              :src="image.src"
              :alt="image.alt || section.title || 'Gallery image'"
              loading="lazy"
            >
          </div>
        </div>

        <div class="pickup-gallery-copy">
          <p v-if="section.description" class="pickup-gallery-copy__description">
            {{ section.description }}
          </p>

          <div v-if="section.sectionLabel" class="pickup-gallery-copy__label">
            {{ section.sectionLabel }}
          </div>

          <NuxtLink
            v-if="action?.isInternal && actionLabel"
            :to="action.href"
            class="pickup-gallery-copy__button"
          >
            {{ actionLabel }}
          </NuxtLink>
          <a
            v-else-if="action && actionLabel"
            :href="action.href"
            class="pickup-gallery-copy__button"
          >
            {{ actionLabel }}
          </a>
        </div>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-pickup-gallery-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-pickup-gallery-native {
  position: relative;
  min-height: 80vh;
  padding: 3.6rem 1.25rem 4rem;
  background-color: #2a1f14;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  overflow: hidden;
}

.pickup-gallery-overlay {
  position: absolute;
  inset: 0;
  background: rgba(30, 20, 10, 0.58);
}

.pickup-gallery-shell {
  position: relative;
  z-index: 1;
  width: min(100%, 86rem);
  margin: 0 auto;
}

.pickup-gallery-header {
  margin-bottom: 2rem;
  text-align: center;
}

.pickup-gallery-header__pretitle {
  margin: 0 0 0.3rem;
  color: rgba(255, 244, 229, 0.68);
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2rem, 4vw, 3.5rem);
  font-style: italic;
}

.pickup-gallery-header__title {
  margin: 0;
  color: #fff7eb;
  font-size: clamp(0.95rem, 1.8vw, 1.45rem);
  letter-spacing: 0.18em;
  line-height: 1.7;
  text-transform: uppercase;
}

.pickup-gallery-body {
  display: grid;
  gap: 2rem;
  grid-template-columns: minmax(0, 1.55fr) minmax(18rem, 0.7fr);
  align-items: stretch;
}

.pickup-gallery-strip {
  display: grid;
  gap: 0.35rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.pickup-gallery-strip__item {
  overflow: hidden;
  border-radius: 1.35rem;
  min-height: 24rem;
}

.pickup-gallery-strip__item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.pickup-gallery-strip__item:hover img {
  transform: scale(1.03);
}

.pickup-gallery-copy {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 1.5rem;
  border: 1px solid rgba(248, 243, 234, 0.1);
  border-radius: 1.5rem;
  background: rgba(255, 252, 246, 0.06);
  backdrop-filter: blur(10px);
  box-shadow: inset 0 1px 0 rgba(255, 248, 237, 0.05);
}

.pickup-gallery-copy__description {
  margin: 0 0 1.6rem;
  max-width: 22rem;
  color: rgba(255, 245, 231, 0.84);
  line-height: 2;
}

.pickup-gallery-copy__label {
  margin-bottom: 1.15rem;
  color: rgba(255, 244, 229, 0.64);
  letter-spacing: 0.18em;
  font-size: 0.74rem;
  text-transform: uppercase;
}

.pickup-gallery-copy__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 12.5rem;
  padding: 0.9rem 1.35rem;
  border-radius: 999px;
  background: linear-gradient(135deg, #b19143, #9a7c38);
  color: #fff8ed;
  text-decoration: none;
  font-weight: 600;
  box-shadow: 0 12px 28px rgba(177, 145, 67, 0.22);
  transition: box-shadow 0.2s ease;
}

.pickup-gallery-copy__button:hover {
  box-shadow: 0 16px 36px rgba(177, 145, 67, 0.3);
}

@media (max-width: 991px) {
  .pickup-gallery-body {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .pickup-gallery-strip {
    grid-template-columns: 1fr;
  }

  .pickup-gallery-strip__item {
    min-height: 14rem;
  }
}
</style>




