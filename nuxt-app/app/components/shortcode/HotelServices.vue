<script setup lang="ts">
import { parseHotelServicesBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseHotelServicesBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
</script>

<template>
  <section v-if="section.title || section.items.length" class="shortcode-hotel-services-native">
    <div class="container hotel-services-shell">
      <header v-if="section.subtitle || section.title || section.description" class="hotel-services-header">
        <p v-if="section.subtitle" class="hotel-services-header__eyebrow">{{ section.subtitle }}</p>
        <h2 v-if="section.title" class="hotel-services-header__title">{{ section.title }}</h2>
        <p v-if="section.description" class="hotel-services-header__description">{{ section.description }}</p>
      </header>

      <div class="hotel-services-grid">
        <article v-for="item in section.items" :key="item.title" class="hotel-services-card">
          <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title" class="hotel-services-card__image">
          <div class="hotel-services-card__body">
            <h3 class="hotel-services-card__title">{{ item.title }}</h3>
            <p v-if="item.price" class="hotel-services-card__price">{{ item.price }}</p>
            <p v-if="item.description" class="hotel-services-card__description">{{ item.description }}</p>

            <NuxtLink
              v-if="item.href && resolveLink(item.href)?.isInternal"
              :to="resolveLink(item.href)!.href"
              class="hotel-services-card__link"
            >
              {{ item.actionLabel || "Read more" }}
            </NuxtLink>
            <a v-else-if="item.href" :href="item.href" class="hotel-services-card__link">
              {{ item.actionLabel || "Read more" }}
            </a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-hotel-services-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-hotel-services-native {
  padding: clamp(4rem, 8vw, 7rem) 0;
  background: linear-gradient(180deg, #fffaf3, #f0e7da);
}
.hotel-services-shell {
  display: grid;
  gap: 2.5rem;
}
.hotel-services-header {
  max-width: 42rem;
  margin: 0 auto;
  text-align: center;
}
.hotel-services-header__eyebrow {
  margin: 0 0 0.75rem;
  color: #8a6e48;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  font-size: 0.78rem;
}
.hotel-services-header__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.2rem);
}
.hotel-services-header__description {
  margin: 1rem 0 0;
  color: rgba(47, 36, 29, 0.78);
  line-height: 1.9;
}
.hotel-services-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
.hotel-services-card {
  overflow: hidden;
  border-radius: 1.5rem;
  background: rgba(255, 252, 246, 0.88);
  box-shadow: 0 18px 44px rgba(48, 35, 27, 0.08);
}
.hotel-services-card__image {
  width: 100%;
  aspect-ratio: 4 / 5;
  display: block;
  object-fit: cover;
}
.hotel-services-card__body {
  padding: 1.2rem;
}
.hotel-services-card__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.9rem;
}
.hotel-services-card__price {
  margin: 0.55rem 0 0;
  color: #8a6e48;
  font-weight: 600;
}
.hotel-services-card__description {
  margin: 0.7rem 0 0;
  color: rgba(47, 36, 29, 0.74);
  line-height: 1.8;
}
.hotel-services-card__link {
  display: inline-flex;
  margin-top: 0.9rem;
  color: #6c744f;
  text-decoration: none;
  font-weight: 600;
}
@media (max-width: 991px) {
  .hotel-services-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 640px) {
  .hotel-services-grid {
    grid-template-columns: 1fr;
  }
}
</style>
