<script setup lang="ts">
import { computed } from "vue";
import { parseRoomListBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseRoomListBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
</script>

<template>
  <section v-if="section.items.length" class="shortcode-room-list-native">
    <div class="container room-list-shell">
      <div class="room-list-grid">
        <article
          v-for="item in section.items"
          :key="`${item.id}-${item.name}`"
          class="room-list-card"
        >
          <div class="room-list-card__media-wrap">
            <component
              :is="resolveLink(item.url)?.isInternal ? 'NuxtLink' : 'a'"
              v-if="item.image"
              :to="resolveLink(item.url)?.isInternal ? resolveLink(item.url)?.href : undefined"
              :href="resolveLink(item.url)?.isInternal ? undefined : resolveLink(item.url)?.href"
              class="room-list-card__media"
            >
              <img :src="item.image" :alt="item.name" class="room-list-card__image">
            </component>
            <div class="room-list-card__overlay" />
          </div>

          <div class="room-list-card__body">
            <div class="room-list-card__glass">
              <h3 class="room-list-card__title">
                {{ item.name }}
              </h3>
              <p v-if="item.description" class="room-list-card__description">
                {{ item.description }}
              </p>
              <ul v-if="item.amenities.length" class="room-list-card__amenities">
                <li v-for="amenity in item.amenities" :key="amenity">
                  {{ amenity }}
                </li>
              </ul>
              <div class="room-list-card__footer">
                <p v-if="item.price" class="room-list-card__price">
                  {{ item.price }}
                </p>
                <component
                  :is="resolveLink(item.url)?.isInternal ? 'NuxtLink' : 'a'"
                  :to="resolveLink(item.url)?.isInternal ? resolveLink(item.url)?.href : undefined"
                  :href="resolveLink(item.url)?.isInternal ? undefined : resolveLink(item.url)?.href"
                  class="room-list-card__action"
                >
                  {{ item.bookLabel }}
                </component>
              </div>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-room-list-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-room-list-native {
  padding: clamp(4rem, 6vw, 5.5rem) 0;
  background:
    radial-gradient(circle at top left, rgba(185, 130, 90, 0.08), transparent 28%),
    linear-gradient(180deg, #fcfaf6, #f3ecdf);
}

.room-list-shell {
  display: grid;
}

.room-list-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.room-list-card {
  position: relative;
  overflow: hidden;
  border-radius: 1.75rem;
  background: #1a120c;
  box-shadow: 0 20px 50px rgba(48, 35, 27, 0.12);
  transition: box-shadow 0.35s ease;
}

.room-list-card:hover {
  box-shadow: 0 28px 64px rgba(48, 35, 27, 0.18);
}

.room-list-card__media-wrap {
  position: relative;
}

.room-list-card__media {
  display: block;
  aspect-ratio: 4 / 5;
  overflow: hidden;
}

.room-list-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.room-list-card:hover .room-list-card__image {
  transform: scale(1.03);
}

.room-list-card__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(0deg, rgba(14, 8, 4, 0.88) 0%, rgba(14, 8, 4, 0.35) 45%, transparent 100%);
  pointer-events: none;
}

.room-list-card__body {
  position: absolute;
  left: 0.6rem;
  right: 0.6rem;
  bottom: 0.6rem;
  z-index: 1;
}

.room-list-card__glass {
  padding: 1rem 1.1rem;
  border: 1px solid rgba(248, 243, 234, 0.1);
  border-radius: 1.25rem;
  background: rgba(32, 22, 16, 0.38);
  backdrop-filter: blur(12px);
  box-shadow: inset 0 1px 0 rgba(255, 248, 237, 0.06);
}

.room-list-card__title {
  margin: 0;
  color: #fff7ef;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.5rem, 2.2vw, 1.85rem);
  line-height: 1.06;
  font-weight: 600;
}

.room-list-card__description {
  margin: 0.45rem 0 0;
  color: rgba(255, 245, 232, 0.76);
  line-height: 1.6;
  font-size: 0.85rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.room-list-card__amenities {
  display: flex;
  flex-wrap: wrap;
  gap: 0.35rem;
  padding: 0;
  margin: 0.5rem 0 0;
  list-style: none;
}

.room-list-card__amenities li {
  padding: 0.25rem 0.6rem;
  border-radius: 999px;
  background: rgba(108, 116, 79, 0.5);
  color: #e8e3d8;
  font-size: 0.72rem;
  font-weight: 600;
}

.room-list-card__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin-top: 0.6rem;
}

.room-list-card__price {
  margin: 0;
  color: #e3c8a8;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.1em;
}

.room-list-card__action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2.4rem;
  padding: 0 0.9rem;
  border-radius: 999px;
  background: rgba(76, 93, 67, 0.8);
  color: #faf4ea;
  text-decoration: none;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

@media (max-width: 1024px) {
  .room-list-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .room-list-grid {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(82%, 82%);
    grid-template-columns: none;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
  }

  .room-list-grid::-webkit-scrollbar {
    display: none;
  }

  .room-list-card {
    scroll-snap-align: start;
  }

  .room-list-card__footer {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>




