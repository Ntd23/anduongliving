<script setup lang="ts">
import { parseHotelServicesBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";
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
        <div class="hotel-services-header__glass">
          <p v-if="section.subtitle" class="hotel-services-header__eyebrow">{{ section.subtitle }}</p>
          <h2 v-if="section.title" class="hotel-services-header__title">{{ section.title }}</h2>
          <p v-if="section.description" class="hotel-services-header__description">{{ section.description }}</p>
        </div>
      </header>

      <div class="hotel-services-grid">
        <article v-for="item in section.items" :key="item.title" class="hotel-services-card">
          <div class="hotel-services-card__media">
            <img v-if="item.image?.src" :src="item.image.src" :alt="item.image.alt || item.title" class="hotel-services-card__image">
            <div class="hotel-services-card__overlay" />
          </div>
          <div class="hotel-services-card__body">
            <div class="hotel-services-card__glass">
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
  background:
    radial-gradient(circle at bottom left, rgba(185, 130, 90, 0.1), transparent 28%),
    linear-gradient(180deg, #fffaf3, #f0e7da);
}

.hotel-services-shell {
  display: grid;
  gap: 4.25rem;
}

.hotel-services-header {
  max-width: 78rem;
  margin: 0 auto;
}

.hotel-services-header__glass {
  text-align: center;
  padding: clamp(1.5rem, 3vw, 2.5rem) clamp(1.75rem, 4vw, 3.5rem);
  border: 1px solid rgba(111, 117, 83, 0.1);
  border-radius: 1.75rem;
  background: rgba(255, 252, 246, 0.72);
  backdrop-filter: blur(8px);
  box-shadow: 0 16px 40px rgba(47, 36, 29, 0.05);
}

.hotel-services-header__eyebrow {
  margin: 0 0 0.75rem;
  color: #8a6e48;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  font-size: 0.78rem;
  font-weight: 600;
}

.hotel-services-header__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.2rem);
  line-height: 1.06;
  text-wrap: balance;
}

.hotel-services-header__description {
  margin: 1.5rem auto 0;
  max-width: 70rem;
  color: rgba(47, 36, 29, 0.76);
  font-size: 1.05rem;
  line-height: 1.8;
  text-align: justify;
  text-align-last: center;
}

.hotel-services-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.hotel-services-card {
  position: relative;
  overflow: hidden;
  border-radius: 1.6rem;
  background: #1a120c;
  box-shadow: 0 18px 44px rgba(48, 35, 27, 0.12);
  transition: box-shadow 0.35s ease;
}

.hotel-services-card:hover {
  box-shadow: 0 24px 56px rgba(48, 35, 27, 0.18);
}

.hotel-services-card__media {
  position: relative;
}

.hotel-services-card__image {
  width: 100%;
  aspect-ratio: 4 / 5;
  display: block;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.hotel-services-card:hover .hotel-services-card__image {
  transform: scale(1.03);
}

.hotel-services-card__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(0deg, rgba(14, 8, 4, 0.85) 0%, rgba(14, 8, 4, 0.3) 45%, transparent 100%);
}

.hotel-services-card__body {
  position: absolute;
  left: 0.6rem;
  right: 0.6rem;
  bottom: 0.6rem;
  z-index: 1;
}

.hotel-services-card__glass {
  padding: 0.9rem 1rem;
  border: 1px solid rgba(248, 243, 234, 0.1);
  border-radius: 1.15rem;
  background: rgba(32, 22, 16, 0.36);
  backdrop-filter: blur(12px);
  box-shadow: inset 0 1px 0 rgba(255, 248, 237, 0.06);
}

.hotel-services-card__title {
  margin: 0;
  color: #fff7ef;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.4rem, 2vw, 1.8rem);
  line-height: 1.06;
}

.hotel-services-card__price {
  margin: 0.35rem 0 0;
  color: #e3c8a8;
  font-weight: 700;
  font-size: 0.88rem;
}

.hotel-services-card__description {
  margin: 0.45rem 0 0;
  color: rgba(255, 245, 232, 0.76);
  line-height: 1.6;
  font-size: 0.85rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.hotel-services-card__link {
  display: inline-flex;
  margin-top: 0.55rem;
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
  .hotel-services-shell {
    gap: 2.75rem;
  }

  .hotel-services-header {
    max-width: 58rem;
  }

  .hotel-services-header__glass {
    padding-inline: clamp(1.5rem, 4vw, 2.5rem);
  }

  .hotel-services-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .hotel-services-shell {
    gap: 2.25rem;
  }

  .hotel-services-header__glass {
    padding: 1.25rem 1.1rem;
  }

  .hotel-services-header__title {
    line-height: 1.08;
  }

  .hotel-services-header__description {
    max-width: none;
    font-size: 1rem;
    line-height: 1.75;
    text-align: left;
    text-align-last: left;
  }

  .hotel-services-grid {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(80%, 80%);
    grid-template-columns: none;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
  }

  .hotel-services-grid::-webkit-scrollbar {
    display: none;
  }

  .hotel-services-card {
    scroll-snap-align: start;
  }
}
</style>



