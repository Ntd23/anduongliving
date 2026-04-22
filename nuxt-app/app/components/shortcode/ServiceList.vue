<script setup lang="ts">
import { parseServiceListBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useResolvedCmsAsset } from "~/composables/useResolvedCmsAsset";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { parseShortcodeAttributes } from "~/utils/shortcode/core";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseServiceListBlock(props.block.raw));
const rawAttributes = computed(() => parseShortcodeAttributes(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const resolveAsset = useResolvedCmsAsset();

const sectionBackgroundSrc = computed(() =>
  section.value.backgroundImage?.src || rawAttributes.value.background_image?.trim() || null,
);

const sectionStyle = computed(() =>
  sectionBackgroundSrc.value
    ? {
        backgroundImage: `linear-gradient(180deg, rgba(250, 245, 238, 0.84), rgba(241, 231, 217, 0.9)), url(${resolveAsset(sectionBackgroundSrc.value) || sectionBackgroundSrc.value})`,
      }
    : {},
);

const resolveServiceLink = (href?: string | null) => {
  return resolveLink(href);
};
</script>

<template>
  <section v-if="section.items.length" class="shortcode-service-list-native" :style="sectionStyle">
    <div class="container service-list-grid">
      <article v-for="item in section.items" :key="item.title" class="service-list-card">
        <NuxtLink
          v-if="item.image?.src && resolveServiceLink(item.href)?.isInternal"
          :to="resolveServiceLink(item.href)!.href"
          class="service-list-card__media-link"
          :aria-label="item.title"
        >
          <div
            class="service-list-card__media"
            :style="{ backgroundImage: `url(${resolveAsset(item.image.src) || item.image.src})` }"
          >
            <img
              :src="resolveAsset(item.image.src) || item.image.src"
              :alt="item.image.alt || item.title"
              class="service-list-card__image"
            >
          </div>
        </NuxtLink>
        <a
          v-else-if="item.image?.src && resolveServiceLink(item.href)"
          :href="resolveServiceLink(item.href)!.href"
          class="service-list-card__media-link"
          :aria-label="item.title"
        >
          <div
            class="service-list-card__media"
            :style="{ backgroundImage: `url(${resolveAsset(item.image.src) || item.image.src})` }"
          >
            <img
              :src="resolveAsset(item.image.src) || item.image.src"
              :alt="item.image.alt || item.title"
              class="service-list-card__image"
            >
          </div>
        </a>
        <div
          v-else
          class="service-list-card__media"
          :style="item.image?.src ? { backgroundImage: `url(${resolveAsset(item.image.src) || item.image.src})` } : undefined"
        >
          <img
            v-if="item.image?.src"
            :src="resolveAsset(item.image.src) || item.image.src"
            :alt="item.image.alt || item.title"
            class="service-list-card__image"
          >
        </div>

        <div class="service-list-card__body">
          <h3 class="service-list-card__title">{{ item.title }}</h3>
          <p v-if="item.description" class="service-list-card__description">{{ item.description }}</p>

          <NuxtLink
            v-if="resolveServiceLink(item.href)?.isInternal"
            :to="resolveServiceLink(item.href)!.href"
            class="service-list-card__link"
          >
            {{ item.actionLabel || "Read more" }}
          </NuxtLink>
          <a v-else-if="resolveServiceLink(item.href)" :href="resolveServiceLink(item.href)!.href" class="service-list-card__link">
            {{ item.actionLabel || "Read more" }}
          </a>
        </div>
      </article>
    </div>
  </section>

  <section v-else class="shortcode-service-list-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-service-list-native {
  position: relative;
  padding: clamp(4rem, 8vw, 7rem) 0;
  background: linear-gradient(180deg, #faf5ee, #f1e7d9);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  overflow: hidden;
}
.service-list-grid {
  position: relative;
  z-index: 1;
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
.service-list-card {
  position: relative;
  display: grid;
  min-height: 31rem;
  overflow: hidden;
  border-radius: 1.6rem;
  background: #251911;
  box-shadow: 0 20px 50px rgba(48, 35, 27, 0.12);
}
.service-list-card__media {
  position: absolute;
  inset: 0;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  transition: transform 0.45s ease;
}
.service-list-card__media-link {
  position: absolute;
  inset: 0;
  z-index: 0;
}
.service-list-card__media::after {
  content: "";
  position: absolute;
  inset: 0;
  background:
    linear-gradient(180deg, rgba(19, 11, 7, 0.08), rgba(19, 11, 7, 0.74)),
    linear-gradient(0deg, rgba(19, 11, 7, 0.85), rgba(19, 11, 7, 0.12));
}
.service-list-card__image {
  width: 0;
  height: 0;
  opacity: 0;
}
.service-list-card:hover .service-list-card__media {
  transform: scale(1.03);
  transition: transform 0.45s ease;
}
.service-list-card__body {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  gap: 0.9rem;
  min-height: 100%;
  padding: 1.5rem;
}
.service-list-card__title {
  margin: 0;
  color: #fff7ef;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2rem, 3vw, 2.6rem);
  line-height: 1;
}
.service-list-card__description {
  margin: 0;
  color: rgba(255, 245, 232, 0.88);
  line-height: 1.9;
  max-width: 24rem;
}
.service-list-card__link {
  display: inline-flex;
  width: fit-content;
  margin-top: 0.2rem;
  padding-bottom: 0.15rem;
  color: #f3d39a;
  text-decoration: none;
  font-weight: 600;
  border-bottom: 1px solid currentColor;
}
@media (max-width: 991px) {
  .service-list-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 640px) {
  .service-list-grid {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(86%, 86%);
    grid-template-columns: none;
    overflow-x: auto;
    padding-bottom: 0.5rem;
    scroll-snap-type: x mandatory;
    scrollbar-width: thin;
  }

  .service-list-card {
    min-height: 27rem;
    scroll-snap-align: start;
  }
}
</style>
