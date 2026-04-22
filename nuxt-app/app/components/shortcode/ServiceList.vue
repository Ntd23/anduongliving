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
    <div class="container service-list-bento">
      <article
        v-for="(item, index) in section.items"
        :key="item.title"
        class="service-list-card"
        :class="{ 'service-list-card--hero': index === 0 }"
      >
        <!-- Background media -->
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

        <!-- Glass panel -->
        <div class="service-list-card__body">
          <div class="service-list-card__glass">
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
  background:
    radial-gradient(circle at 15% 20%, rgba(185, 130, 90, 0.14), transparent 28%),
    linear-gradient(180deg, #faf5ee, #f1e7d9);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  overflow: hidden;
}

/* ── Bento grid ── */
.service-list-bento {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  grid-auto-rows: 16rem;
  gap: 1rem;
}

/* Hero card: first item spans 2 cols + 2 rows */
.service-list-card--hero {
  grid-column: span 2;
  grid-row: span 2;
}

/* ── Card ── */
.service-list-card {
  position: relative;
  overflow: hidden;
  border-radius: 1.6rem;
  background: #1a120c;
  box-shadow: 0 20px 50px rgba(48, 35, 27, 0.14);
  transition: box-shadow 0.35s ease;
}

.service-list-card:hover {
  box-shadow: 0 28px 64px rgba(48, 35, 27, 0.22);
}

/* ── Media ── */
.service-list-card__media {
  position: absolute;
  inset: 0;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
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
    linear-gradient(0deg, rgba(14, 8, 4, 0.88) 0%, rgba(14, 8, 4, 0.42) 40%, rgba(14, 8, 4, 0.06) 100%);
}

.service-list-card__image {
  width: 0;
  height: 0;
  opacity: 0;
}

.service-list-card:hover .service-list-card__media {
  transform: scale(1.03);
}

/* ── Glass panel ── */
.service-list-card__body {
  position: absolute;
  left: 0.75rem;
  right: 0.75rem;
  bottom: 0.75rem;
  z-index: 1;
}

.service-list-card__glass {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 0.9rem 1rem;
  border: 1px solid rgba(248, 243, 234, 0.12);
  border-radius: 1.15rem;
  background: rgba(32, 22, 16, 0.36);
  backdrop-filter: blur(12px);
  box-shadow:
    0 12px 32px rgba(0, 0, 0, 0.18),
    inset 0 1px 0 rgba(255, 248, 237, 0.06);
}

/* Hero card glass: more padding */
.service-list-card--hero .service-list-card__glass {
  padding: 1.2rem 1.35rem;
}

.service-list-card__title {
  margin: 0;
  color: #fff7ef;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.3rem, 1.8vw, 1.65rem);
  line-height: 1.1;
  font-weight: 600;
}

.service-list-card--hero .service-list-card__title {
  font-size: clamp(1.8rem, 2.8vw, 2.5rem);
}

.service-list-card__description {
  margin: 0.4rem 0 0;
  color: rgba(255, 245, 232, 0.78);
  line-height: 1.55;
  font-size: 0.84rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.service-list-card--hero .service-list-card__description {
  font-size: 0.95rem;
  -webkit-line-clamp: 3;
  max-width: 28rem;
}

.service-list-card__link {
  display: inline-flex;
  align-items: center;
  width: fit-content;
  margin-top: 0.55rem;
  padding: 0.32rem 0.8rem;
  border-radius: 999px;
  background: rgba(243, 211, 154, 0.14);
  border: 1px solid rgba(243, 211, 154, 0.22);
  color: #f3d39a;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.78rem;
  letter-spacing: 0.02em;
  transition: background-color 0.2s ease;
}

.service-list-card--hero .service-list-card__link {
  padding: 0.4rem 1rem;
  font-size: 0.85rem;
}

.service-list-card__link:hover {
  background: rgba(243, 211, 154, 0.22);
}

/* ── Tablet ── */
@media (max-width: 1023px) {
  .service-list-bento {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    grid-auto-rows: 15rem;
  }
}

/* ── Mobile: horizontal rail ── */
@media (max-width: 640px) {
  .service-list-bento {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(82%, 82%);
    grid-template-columns: none;
    grid-auto-rows: auto;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
  }

  .service-list-bento::-webkit-scrollbar {
    display: none;
  }

  .service-list-card,
  .service-list-card--hero {
    grid-column: auto;
    grid-row: auto;
    min-height: 26rem;
    scroll-snap-align: start;
  }

  .service-list-card--hero .service-list-card__title {
    font-size: 1.65rem;
  }

  .service-list-card--hero .service-list-card__description {
    -webkit-line-clamp: 2;
  }
}
</style>
