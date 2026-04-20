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
        <p v-if="section.subtitle" class="news-header__eyebrow">{{ section.subtitle }}</p>
        <h2 v-if="section.title" class="news-header__title">{{ section.title }}</h2>
        <p v-if="section.description" class="news-header__description">{{ section.description }}</p>
      </header>

      <div class="news-grid">
        <article v-for="item in section.items" :key="item.title" class="news-card">
          <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title" class="news-card__image">
          <div class="news-card__body">
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
  background: linear-gradient(180deg, #faf5ee, #f2e9de);
}
.news-shell {
  display: grid;
  gap: 2.5rem;
}
.news-header {
  max-width: 42rem;
  margin: 0 auto;
  text-align: center;
}
.news-header__eyebrow {
  margin: 0 0 0.75rem;
  color: #8a6e48;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  font-size: 0.78rem;
}
.news-header__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.2rem);
}
.news-header__description {
  margin: 1rem 0 0;
  color: rgba(47, 36, 29, 0.78);
  line-height: 1.9;
}
.news-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
.news-card {
  overflow: hidden;
  border-radius: 1.5rem;
  background: rgba(255, 252, 246, 0.88);
  box-shadow: 0 18px 44px rgba(48, 35, 27, 0.08);
}
.news-card__image {
  width: 100%;
  aspect-ratio: 4 / 5;
  display: block;
  object-fit: cover;
}
.news-card__body {
  padding: 1.2rem;
}
.news-card__meta {
  margin: 0;
  color: #8a6e48;
  font-size: 0.78rem;
}
.news-card__title {
  margin: 0.55rem 0 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 2rem;
  line-height: 1.02;
}
.news-card__description {
  margin: 0.75rem 0 0;
  color: rgba(47, 36, 29, 0.74);
  line-height: 1.8;
}
.news-card__link {
  display: inline-flex;
  margin-top: 0.85rem;
  color: #6c744f;
  text-decoration: none;
  font-weight: 600;
}
@media (max-width: 991px) {
  .news-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 640px) {
  .news-grid {
    grid-template-columns: 1fr;
  }
}
</style>
