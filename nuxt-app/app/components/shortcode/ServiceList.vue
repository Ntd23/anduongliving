<script setup lang="ts">
import { parseServiceListBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseServiceListBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
</script>

<template>
  <section v-if="section.items.length" class="shortcode-service-list-native">
    <div class="container service-list-grid">
      <article v-for="item in section.items" :key="item.title" class="service-list-card">
        <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title" class="service-list-card__image">
        <h3 class="service-list-card__title">{{ item.title }}</h3>
        <p v-if="item.description" class="service-list-card__description">{{ item.description }}</p>

        <NuxtLink
          v-if="item.href && resolveLink(item.href)?.isInternal"
          :to="resolveLink(item.href)!.href"
          class="service-list-card__link"
        >
          {{ item.actionLabel || "Read more" }}
        </NuxtLink>
        <a v-else-if="item.href" :href="item.href" class="service-list-card__link">
          {{ item.actionLabel || "Read more" }}
        </a>
      </article>
    </div>
  </section>

  <section v-else class="shortcode-service-list-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-service-list-native {
  padding: clamp(4rem, 8vw, 7rem) 0;
  background: linear-gradient(180deg, #faf5ee, #f1e7d9);
}
.service-list-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
.service-list-card {
  overflow: hidden;
  border-radius: 1.6rem;
  background: rgba(255, 251, 245, 0.85);
  box-shadow: 0 20px 50px rgba(48, 35, 27, 0.08);
}
.service-list-card__image {
  width: 100%;
  aspect-ratio: 4 / 5;
  display: block;
  object-fit: cover;
}
.service-list-card__title {
  margin: 1.2rem 1.2rem 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 2rem;
}
.service-list-card__description {
  margin: 0.75rem 1.2rem 0;
  color: rgba(47, 36, 29, 0.74);
  line-height: 1.8;
}
.service-list-card__link {
  display: inline-flex;
  margin: 1rem 1.2rem 1.3rem;
  color: #6c744f;
  text-decoration: none;
  font-weight: 600;
}
@media (max-width: 991px) {
  .service-list-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 640px) {
  .service-list-grid {
    grid-template-columns: 1fr;
  }
}
</style>
