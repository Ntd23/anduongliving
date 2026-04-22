<script setup lang="ts">
import { parseNewsBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseNewsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
</script>

<template>
  <section v-if="section.title || section.items.length" class="shortcode-news-native">
    <div class="container news-shell">
      <header v-if="section.subtitle || section.title || section.description" class="news-header">
        <div class="news-header__glass">
          <p v-if="section.subtitle" class="news-header__eyebrow">{{ section.subtitle }}</p>
          <h2 v-if="section.title" class="news-header__title">{{ section.title }}</h2>
          <p v-if="section.description" class="news-header__description">{{ section.description }}</p>
        </div>
      </header>

      <div class="news-grid">
        <article v-for="item in section.items" :key="item.title" class="news-card">
          <div class="news-card__media">
            <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title" class="news-card__image">
            <div class="news-card__overlay" />
          </div>
          <div class="news-card__body">
            <div class="news-card__glass">
              <p v-if="item.meta" class="news-card__meta">{{ item.meta }}</p>
              <h3 class="news-card__title">{{ item.title }}</h3>
              <p v-if="item.description" class="news-card__description">{{ item.description }}</p>
              <NuxtLink
                v-if="item.href && resolveLink(item.href)?.isInternal"
                :to="resolveLink(item.href)!.href"
                class="news-card__link"
              >
                {{ item.actionLabel || "Read more" }}
              </NuxtLink>
              <a v-else-if="item.href" :href="item.href" class="news-card__link">
                {{ item.actionLabel || "Read more" }}
              </a>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-news-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-news-native {
  padding: clamp(4rem, 8vw, 7rem) 0;
  background:
    radial-gradient(circle at bottom right, rgba(185, 130, 90, 0.1), transparent 28%),
    linear-gradient(180deg, #fcfaf6, #f2e9de);
}

.news-shell {
  display: grid;
  gap: 2.5rem;
}

.news-header {
  max-width: 48rem;
  margin: 0 auto;
}

.news-header__glass {
  text-align: center;
  padding: clamp(1.25rem, 3vw, 2rem);
  border: 1px solid rgba(111, 117, 83, 0.1);
  border-radius: 1.75rem;
  background: rgba(255, 252, 246, 0.72);
  backdrop-filter: blur(8px);
  box-shadow: 0 16px 40px rgba(47, 36, 29, 0.05);
}

.news-header__eyebrow {
  margin: 0 0 0.75rem;
  color: #8a6e48;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  font-size: 0.78rem;
  font-weight: 600;
}

.news-header__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.2rem);
  line-height: 1;
}

.news-header__description {
  margin: 1rem 0 0;
  color: rgba(47, 36, 29, 0.76);
  line-height: 1.8;
}

.news-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.news-card {
  position: relative;
  overflow: hidden;
  border-radius: 1.6rem;
  background: #1a120c;
  min-height: 24rem;
  box-shadow: 0 18px 44px rgba(48, 35, 27, 0.12);
  transition: box-shadow 0.35s ease;
}

.news-card:hover {
  box-shadow: 0 24px 56px rgba(48, 35, 27, 0.18);
}

.news-card__media {
  position: absolute;
  inset: 0;
}

.news-card__image {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.news-card:hover .news-card__image {
  transform: scale(1.03);
}

.news-card__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(0deg, rgba(14, 8, 4, 0.88) 0%, rgba(14, 8, 4, 0.4) 40%, rgba(14, 8, 4, 0.06) 100%);
}

.news-card__body {
  position: absolute;
  left: 0.6rem;
  right: 0.6rem;
  bottom: 0.6rem;
  z-index: 1;
}

.news-card__glass {
  padding: 0.9rem 1rem;
  border: 1px solid rgba(248, 243, 234, 0.1);
  border-radius: 1.15rem;
  background: rgba(32, 22, 16, 0.36);
  backdrop-filter: blur(12px);
  box-shadow: inset 0 1px 0 rgba(255, 248, 237, 0.06);
}

.news-card__meta {
  margin: 0 0 0.3rem;
  color: #e3c8a8;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.news-card__title {
  margin: 0;
  color: #fff7ef;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.4rem, 2vw, 1.85rem);
  line-height: 1.06;
}

.news-card__description {
  margin: 0.4rem 0 0;
  color: rgba(255, 245, 232, 0.76);
  line-height: 1.55;
  font-size: 0.84rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.news-card__link {
  display: inline-flex;
  margin-top: 0.5rem;
  padding: 0.3rem 0.75rem;
  border-radius: 999px;
  background: rgba(243, 211, 154, 0.14);
  border: 1px solid rgba(243, 211, 154, 0.2);
  color: #f3d39a;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.78rem;
}

@media (max-width: 991px) {
  .news-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .news-grid {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(80%, 80%);
    grid-template-columns: none;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
  }

  .news-grid::-webkit-scrollbar {
    display: none;
  }

  .news-card {
    scroll-snap-align: start;
  }
}
</style>
