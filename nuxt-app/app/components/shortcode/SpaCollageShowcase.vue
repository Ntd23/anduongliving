<script setup lang="ts">
import { parseSpaCollageShowcaseBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseSpaCollageShowcaseBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
</script>

<template>
  <section
    v-if="section.leftPanelImage?.src || section.rightPanelImage?.src || section.bottomImages.length"
    class="shortcode-spa-collage-native"
  >
    <div class="spa-shell">
      <div class="spa-top">
        <figure v-if="section.leftPanelImage?.src" class="spa-top__left">
          <img :src="section.leftPanelImage.src" :alt="section.leftPanelImage.alt || section.title || 'Spa image'">
        </figure>

        <div class="spa-top__copy">
          <p v-if="section.subtitle" class="spa-top__subtitle">{{ section.subtitle }}</p>
          <h2 v-if="section.title" class="spa-top__title">{{ section.title }}</h2>
          <p v-if="section.description" class="spa-top__description">{{ section.description }}</p>
        </div>

        <figure v-if="section.rightPanelImage?.src" class="spa-top__right">
          <img :src="section.rightPanelImage.src" :alt="section.rightPanelImage.alt || 'Spa collage image'">
        </figure>
      </div>

      <div v-if="section.bottomImages.length" class="spa-bottom">
        <figure
          v-for="(image, index) in section.bottomImages"
          :key="`${image.src}-${index}`"
          class="spa-bottom__item"
        >
          <img :src="image.src" :alt="image.alt || `Spa detail ${index + 1}`">
        </figure>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-spa-collage-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-spa-collage-native {
  background: #130f0d;
  padding: clamp(3rem, 7vw, 6rem) 0;
  color: #f8f0e6;
}

.spa-shell {
  width: min(100%, 90rem);
  margin: 0 auto;
}

.spa-top {
  display: grid;
  gap: 1rem;
  grid-template-columns: minmax(0, 1fr) minmax(20rem, 0.9fr) minmax(0, 1fr);
  align-items: stretch;
}

.spa-top__left,
.spa-top__right,
.spa-bottom__item {
  margin: 0;
  overflow: hidden;
}

.spa-top__left img,
.spa-top__right img,
.spa-bottom__item img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
}

.spa-top__copy {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: clamp(2rem, 4vw, 3rem);
}

.spa-top__subtitle {
  margin: 0 0 0.8rem;
  color: rgba(245, 236, 224, 0.76);
  letter-spacing: 0.18em;
  text-transform: uppercase;
  font-size: 0.74rem;
}

.spa-top__title {
  margin: 0;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.4rem, 5vw, 4.4rem);
  line-height: 0.98;
}

.spa-top__description {
  margin: 1.2rem 0 0;
  color: rgba(245, 236, 224, 0.84);
  line-height: 1.95;
}

.spa-bottom {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  margin-top: 1rem;
}

.spa-bottom__item {
  min-height: 12rem;
}

@media (max-width: 991px) {
  .spa-top {
    grid-template-columns: 1fr;
  }

  .spa-bottom {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .spa-bottom {
    grid-template-columns: 1fr;
  }
}
</style>
