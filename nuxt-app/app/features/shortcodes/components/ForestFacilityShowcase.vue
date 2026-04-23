<script setup lang="ts">
import { parseForestFacilityShowcaseBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";
import { useResolvedCmsAsset } from "~/features/cms/data/useResolvedCmsAsset";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseForestFacilityShowcaseBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const resolveAsset = useResolvedCmsAsset();

const action = computed(() => resolveLink(section.value.action?.href));
</script>

<template>
  <section
    v-if="(section.leftImage?.src || section.rightImages.length) && (section.title || section.description || section.sectionLabel || section.action?.label)"
    class="shortcode-forest-facility-native"
  >
    <header v-if="section.title" class="forest-header">
      <h2 class="forest-header__title">
        {{ section.title }}
      </h2>
    </header>

    <div class="forest-shell">
      <div class="forest-media">
        <figure v-if="section.leftImage?.src" class="forest-media__primary">
          <img :src="resolveAsset(section.leftImage.src) || section.leftImage.src" :alt="section.leftImage.alt || section.title || 'Forest facility image'">
        </figure>

        <div v-if="section.rightImages.length" class="forest-media__secondary">
          <figure
            v-for="(image, index) in section.rightImages"
            :key="`${image.src}-${index}`"
            class="forest-media__secondary-item"
          >
            <img :src="resolveAsset(image.src) || image.src" :alt="image.alt || `Forest detail ${index + 1}`">
          </figure>
        </div>
      </div>

      <div class="forest-copy">
        <p v-if="section.description" class="forest-copy__description">
          {{ section.description }}
        </p>

        <NuxtLink v-if="action?.isInternal" :to="action.href" class="forest-copy__button">
          {{ section.action?.label }}
        </NuxtLink>
        <a v-else-if="action" :href="action.href" class="forest-copy__button">
          {{ section.action?.label }}
        </a>
      </div>
    </div>

    <footer v-if="section.sectionLabel" class="forest-footer">
      <p class="forest-footer__label">
        {{ section.sectionLabel }}
      </p>
    </footer>
  </section>

  <section v-else class="shortcode-forest-facility-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-forest-facility-native {
  background: linear-gradient(180deg, #f7f0e3, #efe5d5);
  padding: clamp(4rem, 8vw, 7rem) 0;
}

.forest-header {
  width: min(calc(100% - 2rem), 76rem);
  margin: 0 auto 2.5rem;
  text-align: center;
}

.forest-header__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(3.3rem, 7vw, 6.2rem);
  line-height: 0.92;
  letter-spacing: -0.025em;
}

.forest-shell {
  width: min(calc(100% - 2rem), 76rem);
  margin: 0 auto;
  display: grid;
  gap: clamp(2rem, 4vw, 4rem);
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  align-items: center;
}

.forest-media {
  display: grid;
  gap: 1rem;
  grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.9fr);
}

.forest-media__primary,
.forest-media__secondary-item {
  margin: 0;
  overflow: hidden;
  border-radius: 1.6rem;
  box-shadow: 0 24px 48px rgba(48, 35, 27, 0.08);
}

.forest-media__primary img,
.forest-media__secondary-item img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
}

.forest-media__primary {
  min-height: 34rem;
}

.forest-media__secondary {
  display: grid;
  gap: 1rem;
}

.forest-media__secondary-item {
  min-height: 16rem;
}

.forest-copy__description {
  margin: 0;
  max-width: 36rem;
  color: rgba(47, 36, 29, 0.78);
  font-size: 1.08rem;
  line-height: 2;
}

.forest-copy__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 12rem;
  margin-top: 1.75rem;
  padding: 0.95rem 1.4rem;
  border-radius: 999px;
  background: #6c744f;
  color: #fffaf1;
  text-decoration: none;
}

.forest-footer {
  width: min(calc(100% - 2rem), 76rem);
  margin: 2.25rem auto 0;
}

.forest-footer__label {
  margin: 0;
  color: #8a6e48;
  font-size: 0.78rem;
  letter-spacing: 0.24em;
  text-transform: uppercase;
}

@media (max-width: 991px) {
  .forest-shell {
    grid-template-columns: 1fr;
  }

  .forest-header {
    margin-bottom: 2rem;
  }
}

@media (max-width: 640px) {
  .forest-header__title {
    font-size: clamp(2.6rem, 11vw, 4rem);
  }

  .forest-media {
    grid-template-columns: 1fr;
  }

  .forest-media__primary,
  .forest-media__secondary-item {
    min-height: 15rem;
  }
}
</style>




