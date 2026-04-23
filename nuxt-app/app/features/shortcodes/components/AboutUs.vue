<script setup lang="ts">
import { parseAboutUsBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsAsset } from "~/composables/useResolvedCmsAsset";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseAboutUsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveAsset = useResolvedCmsAsset();
const sectionStyle = computed(() => {
  const image = section.value.signatureImage?.src;
  const resolvedImage = image ? resolveAsset(image) || image : "";

  return resolvedImage
    ? { "--about-signature-bg": `url("${resolvedImage}")` } as Record<string, string>
    : undefined;
});
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
    :style="sectionStyle"
  >
    <div class="container">
      <div class="about-layout">
        <div v-if="section.mainImage?.src" class="about-side-image">
          <img
            :src="resolveAsset(section.mainImage.src) || section.mainImage.src"
            :alt="section.mainImage.alt || section.title || 'About side image'"
            loading="lazy"
          >
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

          <div v-if="section.action" class="about-footer">
            <NuxtLink
              :to="section.action.href"
              class="about-cta"
            >
              {{ section.action.label }}
            </NuxtLink>
          </div>
        </div>

        <div class="about-media">
          <div v-if="section.backgroundImage?.src" class="about-media__item">
            <img
              :src="resolveAsset(section.backgroundImage.src) || section.backgroundImage.src"
              :alt="section.backgroundImage.alt || section.title || 'About image'"
              loading="lazy"
            >
          </div>

          <div v-if="section.accentImage?.src" class="about-media__item">
            <img
              :src="resolveAsset(section.accentImage.src) || section.accentImage.src"
              :alt="section.accentImage.alt || section.title || 'About accent image'"
              loading="lazy"
            >
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
  --about-signature-bg: none;
  position: relative;
  overflow: hidden;
  padding: clamp(3rem, 5vw, 4.25rem) 0;
  background-image:
    linear-gradient(180deg, rgba(252, 250, 246, 0.74), rgba(243, 236, 223, 0.82)),
    var(--about-signature-bg),
    radial-gradient(circle at top right, rgba(185, 130, 90, 0.12), transparent 28%),
    linear-gradient(180deg, #fcfaf6, #f3ecdf);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover, cover, auto, auto;
}

.shortcode-about > .container {
  position: relative;
  z-index: 1;
}

.about-layout {
  position: relative;
  display: grid;
  gap: clamp(1.35rem, 2.6vw, 2.35rem);
  align-items: stretch;
  grid-template-columns: minmax(20rem, 0.95fr) minmax(0, 1.18fr) minmax(15rem, 0.68fr);
}

.about-media {
  display: grid;
  grid-template-rows: minmax(0, 1fr) minmax(0, 1fr);
  gap: 1.1rem;
  height: 100%;
}

.about-media__item {
  display: flex;
  overflow: hidden;
  min-height: 0;
  border-radius: 1.55rem;
  box-shadow: 0 24px 58px rgba(47, 36, 29, 0.13);
}

.about-media__item img,
.about-side-image img {
  display: block;
  width: 100%;
}

.about-media__item img {
  height: 100%;
  min-height: 0;
  object-fit: cover;
  object-position: center;
}

.about-copy {
  min-height: clamp(28rem, 40vw, 36rem);
  padding: clamp(1.35rem, 2vw, 2rem);
  border: 1px solid rgba(111, 117, 83, 0.12);
  border-radius: 1.75rem;
  background: rgba(255, 252, 246, 0.76);
  box-shadow: 0 20px 48px rgba(47, 36, 29, 0.06);
  backdrop-filter: blur(8px);
}

.about-side-image {
  overflow: hidden;
  height: 100%;
  border-radius: 1.75rem;
  box-shadow: 0 28px 68px rgba(47, 36, 29, 0.12);
}

.about-side-image img {
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.about-header {
  margin-bottom: 1rem;
}

.about-eyebrow {
  margin: 0 0 0.8rem;
  color: var(--retreat-olive);
}

.about-title {
  margin: 0;
  color: var(--retreat-ink);
  font-size: clamp(2rem, 4.2vw, 3.35rem);
  line-height: 1.04;
}

.about-description {
  color: rgba(47, 36, 29, 0.76);
  font-size: 0.96rem;
  line-height: 1.62;
}

.about-description p {
  margin: 0 0 0.72rem;
}

.about-points {
  display: grid;
  gap: 0.7rem;
  margin: 1.15rem 0 0;
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
  justify-content: flex-start;
  margin-top: 1.35rem;
}

.about-cta {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3.05rem;
  padding: 0 1.25rem;
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

@media (min-width: 1024px) {
  .about-layout {
    grid-template-columns: minmax(20rem, 0.95fr) minmax(0, 1.18fr) minmax(15rem, 0.68fr);
  }
}

@media (max-width: 1100px) {
  .about-layout {
    grid-template-columns: minmax(0, 1fr);
  }

  .about-copy {
    min-height: auto;
  }

  .about-media,
  .about-side-image {
    height: auto;
  }

  .about-media__item img,
  .about-side-image img {
    height: auto;
    aspect-ratio: 16 / 10;
    object-fit: cover;
  }
}

@media (max-width: 767px) {
  .about-footer {
    align-items: flex-start;
  }
}
</style>