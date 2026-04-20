<script setup lang="ts">
import { parseIntroVideoBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseIntroVideoBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const sectionStyle = computed(() =>
  section.value.backgroundImage?.src
    ? { backgroundImage: `url('${section.value.backgroundImage.src}')` }
    : undefined,
);
</script>

<template>
  <section v-if="section.title || section.videoUrl" class="shortcode-intro-video-native" :style="sectionStyle">
    <div class="intro-video-overlay" />
    <div class="container intro-video-shell">
      <a v-if="section.videoUrl" :href="section.videoUrl" class="intro-video-button" target="_blank" rel="noreferrer">
        <img
          v-if="section.buttonIcon?.src"
          :src="section.buttonIcon.src"
          :alt="section.buttonIcon.alt || 'Play video'"
          class="intro-video-button__icon"
        >
        <span v-else class="intro-video-button__fallback">Play</span>
      </a>

      <h2 v-if="section.title" class="intro-video-title">{{ section.title }}</h2>
    </div>
  </section>

  <section v-else class="shortcode-intro-video-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-intro-video-native {
  position: relative;
  padding: clamp(6rem, 12vw, 9rem) 0;
  background-color: #17110e;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  color: #fff7ec;
}
.intro-video-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(18, 12, 9, 0.55), rgba(18, 12, 9, 0.76));
}
.intro-video-shell {
  position: relative;
  z-index: 1;
  text-align: center;
}
.intro-video-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 6.5rem;
  height: 6.5rem;
  margin-bottom: 1.5rem;
  border-radius: 999px;
  background: rgba(255, 251, 245, 0.14);
  backdrop-filter: blur(10px);
}
.intro-video-button__icon {
  width: 3rem;
  height: 3rem;
  object-fit: contain;
}
.intro-video-button__fallback {
  color: #fff7ec;
  font-weight: 700;
}
.intro-video-title {
  margin: 0;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.5rem);
  line-height: 0.98;
}
</style>
