<script setup lang="ts">
import { parseAboutUsBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseAboutUsBlock(props.block.raw));
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
    v-if="
      section.title ||
      section.subtitle ||
      section.mainImage?.src ||
      section.bullets.length
    "
    class="shortcode-about"
  >
    <div v-if="section.backgroundImage?.src" class="about-decor">
      <img
        :src="section.backgroundImage.src"
        :alt="section.backgroundImage.alt || section.title || 'About decoration'"
        loading="lazy"
      >
    </div>

    <div class="container">
      <div class="about-layout">
        <div class="about-media">
          <div v-if="section.mainImage?.src" class="about-media__primary">
            <img
              :src="section.mainImage.src"
              :alt="section.mainImage.alt || section.title || 'About image'"
              loading="lazy"
            >
          </div>
        
          <div v-if="section.accentImage?.src" class="about-media__accent">
            <img
              :src="section.accentImage.src"
              :alt="section.accentImage.alt || section.title || 'About accent image'"
              loading="lazy"
            >
          </div>
        </div>

        <div class="about-copy">
          <div v-if="section.subtitle || section.title" class="about-header">
            <p v-if="section.subtitle" class="about-eyebrow shortcode-narrative-eyebrow">
              {{ section.subtitle }}
            </p>
            <h2 v-if="section.title" class="about-title shortcode-narrative-title">
              {{ section.title }}
            </h2>
          </div>

          <div v-if="descriptionLines.length" class="about-description">
            <p v-for="(line, index) in descriptionLines" :key="`${index}-${line.slice(0, 24)}`">
              {{ line }}
            </p>
          </div>

          <ul v-if="section.bullets.length" class="about-points">
            <li v-for="point in section.bullets" :key="point">
              <span class="about-points__marker" />
              <span>{{ point }}</span>
            </li>
          </ul>

          <div
            v-if="section.action || section.signatureImage?.src"
            class="about-footer"
          >
            <NuxtLink
              v-if="section.action"
              :to="section.action.href"
              class="about-cta"
            >
              {{ section.action.label }}
            </NuxtLink>

            <div v-if="section.signatureImage?.src" class="about-signature">
              <img
                :src="section.signatureImage.src"
                :alt="section.signatureImage.alt || 'Signature'"
                loading="lazy"
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-about">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-about {
  position: relative;
  overflow: hidden;
  padding: 5.5rem 0;
  background:
    radial-gradient(circle at top right, rgba(185, 130, 90, 0.12), transparent 28%),
    linear-gradient(180deg, #fcfaf6, #f3ecdf);
}

.about-decor {
  position: absolute;
  top: 1.25rem;
  right: clamp(-1rem, 1vw, 1rem);
  opacity: 0.3;
  pointer-events: none;
}

.about-layout {
  position: relative;
  display: grid;
  gap: 2.5rem;
  align-items: center;
}

.about-media {
  position: relative;
  min-height: 24rem;
}

.about-media__primary {
  overflow: hidden;
  width: min(100%, 34rem);
  border-radius: 1.75rem;
  box-shadow: 0 32px 84px rgba(47, 36, 29, 0.14);
}

.about-media__primary img,
.about-media__accent img,
.about-signature img {
  display: block;
  width: 100%;
}

.about-media__primary img {
  aspect-ratio: 4 / 5;
  object-fit: cover;
}

.about-media__accent {
  position: absolute;
  right: 0;
  bottom: 1.5rem;
  overflow: hidden;
  width: min(42%, 13rem);
  border: 8px solid rgba(255, 252, 246, 0.95);
  border-radius: 1.25rem;
  box-shadow: 0 18px 40px rgba(47, 36, 29, 0.16);
}

.about-copy {
  max-width: 40rem;
  padding: clamp(1.25rem, 2vw, 2rem);
  border: 1px solid rgba(111, 117, 83, 0.12);
  border-radius: 1.75rem;
  background: rgba(255, 252, 246, 0.72);
  box-shadow: 0 20px 48px rgba(47, 36, 29, 0.06);
  backdrop-filter: blur(6px);
}

.about-header {
  margin-bottom: 1.25rem;
}

.about-eyebrow {
  margin: 0 0 0.8rem;
  color: var(--retreat-olive);
}

.about-title {
  margin: 0;
  color: var(--retreat-ink);
  font-size: clamp(2rem, 5vw, 4rem);
  line-height: 1.04;
}

.about-description {
  color: rgba(47, 36, 29, 0.76);
}

.about-description p {
  margin: 0 0 1rem;
}

.about-points {
  display: grid;
  gap: 0.85rem;
  margin: 1.75rem 0 0;
  padding: 0;
  list-style: none;
}

.about-points li {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 0.85rem;
  align-items: start;
  color: var(--retreat-ink);
  font-weight: 500;
}

.about-points__marker {
  width: 0.8rem;
  height: 0.8rem;
  margin-top: 0.35rem;
  border-radius: 999px;
  background: linear-gradient(135deg, var(--retreat-olive), var(--retreat-clay));
  box-shadow: 0 0 0 6px rgba(111, 117, 83, 0.08);
}

.about-footer {
  display: flex;
  flex-wrap: wrap;
  gap: 1.25rem;
  align-items: center;
  justify-content: space-between;
  margin-top: 2rem;
}

.about-cta {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3.5rem;
  padding: 0 1.4rem;
  border-radius: 999px;
  background: linear-gradient(135deg, var(--retreat-clay), #9f6f49);
  color: #fffdf9;
  font-weight: 700;
  text-decoration: none;
  box-shadow: 0 16px 32px rgba(185, 130, 90, 0.22);
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.about-cta:hover {
  transform: translateY(-1px);
  box-shadow: 0 18px 36px rgba(185, 130, 90, 0.28);
}

.about-signature {
  width: min(100%, 12rem);
  opacity: 0.92;
}

@media (min-width: 1024px) {
  .about-layout {
    grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
  }
}

@media (max-width: 767px) {
  .about-media {
    min-height: 0;
  }

  .about-media__accent {
    right: 0.75rem;
    bottom: -1rem;
    width: 40%;
  }

  .about-footer {
    align-items: flex-start;
  }
}
</style>




