<script setup lang="ts">
import { computed } from "vue";
import { parseFeaturedRoomsBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseFeaturedRoomsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);

const isInternalLink = (value?: string | null) => {
  const href = String(value || "").trim();
  return !!href && href.startsWith("/") && !href.startsWith("//");
};
</script>

<template>
  <section
    v-if="section.title || section.subtitle || section.items.length"
    class="shortcode-services"
  >
    <div class="container shortcode-services__inner">
      <header
        v-if="section.subtitle || section.title || section.description"
        class="shortcode-services__header"
      >
        <p v-if="section.subtitle" class="shortcode-services__eyebrow">
          {{ section.subtitle }}
        </p>
        <h2 v-if="section.title" class="shortcode-services__title">
          {{ section.title }}
        </h2>
        <p v-if="section.description" class="shortcode-services__description">
          {{ section.description }}
        </p>
      </header>

      <div class="shortcode-services__grid">
        <article
          v-for="item in section.items"
          :key="item.id"
          class="shortcode-services__card"
        >
          <div v-if="item.image" class="shortcode-services__media">
            <img
              :src="item.image"
              :alt="item.name"
              class="shortcode-services__image"
            >
          </div>

          <div class="shortcode-services__content">
            <h3 class="shortcode-services__card-title">
              {{ item.name }}
            </h3>

            <p v-if="item.price" class="shortcode-services__price">
              {{ item.price }}
            </p>

            <NuxtLink
              v-if="isInternalLink(item.url)"
              :to="item.url"
              class="shortcode-services__action"
            >
              {{ item.bookLabel }}
            </NuxtLink>

            <a
              v-else
              :href="item.url || '#'"
              class="shortcode-services__action"
            >
              {{ item.bookLabel }}
            </a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-services__fallback">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-services {
  padding: clamp(3.5rem, 7vw, 5.5rem) 0;
  background:
    radial-gradient(circle at top left, rgba(214, 191, 149, 0.18), transparent 32%),
    linear-gradient(180deg, #fffaf3 0%, #f5ede1 100%);
}

.shortcode-services__inner {
  display: grid;
  gap: 2.5rem;
}

.shortcode-services__header {
  max-width: 40rem;
  display: grid;
  gap: 0.9rem;
}

.shortcode-services__eyebrow {
  margin: 0;
  color: #8b6a3f;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.24em;
  text-transform: uppercase;
}

.shortcode-services__title {
  margin: 0;
  color: #2d2018;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.4rem, 5vw, 4rem);
  font-weight: 600;
  line-height: 0.95;
}

.shortcode-services__description {
  margin: 0;
  color: rgba(58, 42, 31, 0.78);
  font-size: 1rem;
  line-height: 1.8;
}

.shortcode-services__grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1.5rem;
}

.shortcode-services__card {
  display: flex;
  flex-direction: column;
  min-height: 100%;
  overflow: hidden;
  border: 1px solid rgba(126, 96, 62, 0.12);
  border-radius: 1.5rem;
  background: rgba(255, 255, 255, 0.8);
  box-shadow: 0 18px 48px rgba(50, 32, 20, 0.08);
}

.shortcode-services__media {
  aspect-ratio: 4 / 5;
  overflow: hidden;
  background: rgba(93, 66, 42, 0.08);
}

.shortcode-services__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.shortcode-services__content {
  display: grid;
  gap: 1rem;
  padding: 1.5rem;
}

.shortcode-services__card-title {
  margin: 0;
  color: #231711;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.8rem;
  font-weight: 600;
  line-height: 1.05;
}

.shortcode-services__price {
  margin: 0;
  color: #8b6a3f;
  font-size: 0.95rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.shortcode-services__action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: fit-content;
  min-height: 2.8rem;
  padding: 0 1.25rem;
  border-radius: 999px;
  background: #5b4330;
  color: #fffaf1;
  font-size: 0.88rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-decoration: none;
  text-transform: uppercase;
  transition:
    transform 0.2s ease,
    background-color 0.2s ease;
}

.shortcode-services__action:hover {
  transform: translateY(-1px);
  background: #6c523d;
}

.shortcode-services__fallback {
  display: contents;
}

@media (max-width: 1024px) {
  .shortcode-services__grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .shortcode-services__grid {
    grid-template-columns: minmax(0, 1fr);
  }
}
</style>
