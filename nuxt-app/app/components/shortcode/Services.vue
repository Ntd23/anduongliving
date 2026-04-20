<script setup lang="ts">
import { computed } from "vue";
import {
  parseServicesBlock,
  type FeatureAreaSectionData,
  type ShortcodeBlock,
} from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed<FeatureAreaSectionData>(() => parseServicesBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const sectionStyle = computed(() =>
  section.value.backgroundColor
    ? { background: section.value.backgroundColor }
    : undefined,
);
</script>

<template>
  <section class="shortcode-services-split" :style="sectionStyle">
    <div class="container services-split-shell">
      <div v-if="section.image?.src" class="services-split-shell__media">
        <img :src="section.image.src" :alt="section.image.alt || section.title || 'Services image'">
      </div>

      <div
        v-if="section.subtitle || section.title || section.description || section.action"
        class="services-split-shell__content"
      >
        <p v-if="section.subtitle" class="services-split-shell__eyebrow">
          {{ section.subtitle }}
        </p>
        <h2 v-if="section.title" class="services-split-shell__title">
          {{ section.title }}
        </h2>
        <p v-if="section.description" class="services-split-shell__description">
          {{ section.description }}
        </p>
        <NuxtLink
          v-if="section.action?.href && section.action?.label"
          :to="section.action.href"
          class="services-split-shell__action"
        >
          {{ section.action.label }}
        </NuxtLink>
      </div>

      <div v-else class="services-split-shell__fallback" v-html="sanitizedHtml" />
    </div>
  </section>
</template>

<style scoped>
.shortcode-services-split {
  padding: clamp(4rem, 7vw, 6rem) 0;
  background: #f7f5f1;
}

.services-split-shell {
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
  gap: clamp(2rem, 5vw, 4rem);
  align-items: center;
}

.services-split-shell__media {
  overflow: hidden;
  border-radius: 1.8rem;
}

.services-split-shell__media img {
  display: block;
  width: 100%;
  aspect-ratio: 4 / 5;
  object-fit: cover;
}

.services-split-shell__content {
  max-width: 34rem;
}

.services-split-shell__eyebrow {
  margin: 0 0 0.75rem;
  color: #8b6a3f;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.services-split-shell__title {
  margin: 0;
  color: #2d2018;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.35rem);
  line-height: 0.95;
  font-weight: 600;
}

.services-split-shell__description {
  margin: 1.25rem 0 0;
  color: rgba(58, 42, 31, 0.78);
  line-height: 1.8;
}

.services-split-shell__action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2.95rem;
  margin-top: 1.5rem;
  padding: 0 1.25rem;
  border-radius: 999px;
  background: #4c5d43;
  color: #faf4ea;
  text-decoration: none;
  font-size: 0.84rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

@media (max-width: 900px) {
  .services-split-shell {
    grid-template-columns: minmax(0, 1fr);
  }
}
</style>
