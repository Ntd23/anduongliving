<script setup lang="ts">
import { computed } from "vue";
import { parseRoomListBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";

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
          <component
            :is="resolveLink(item.url)?.isInternal ? 'NuxtLink' : 'a'"
            v-if="item.image"
            :to="resolveLink(item.url)?.isInternal ? resolveLink(item.url)?.href : undefined"
            :href="resolveLink(item.url)?.isInternal ? undefined : resolveLink(item.url)?.href"
            class="room-list-card__media"
          >
            <img :src="item.image" :alt="item.name" class="room-list-card__image">
          </component>

          <div class="room-list-card__body">
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
  padding: clamp(3.5rem, 6vw, 5rem) 0;
}

.room-list-shell {
  display: grid;
}

.room-list-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1.75rem;
}

.room-list-card {
  overflow: hidden;
  border-radius: 1.75rem;
  background: rgba(255, 251, 244, 0.88);
  border: 1px solid rgba(124, 103, 76, 0.12);
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
}

.room-list-card__body {
  display: grid;
  gap: 0.95rem;
  padding: 1.5rem;
}

.room-list-card__title {
  margin: 0;
  color: #241812;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.9rem;
  line-height: 1.04;
  font-weight: 600;
}

.room-list-card__description {
  margin: 0;
  color: rgba(58, 43, 31, 0.76);
  line-height: 1.7;
}

.room-list-card__amenities {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  padding: 0;
  margin: 0;
  list-style: none;
}

.room-list-card__amenities li {
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  background: rgba(110, 131, 89, 0.1);
  color: #4d5d43;
  font-size: 0.8rem;
}

.room-list-card__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.room-list-card__price {
  margin: 0;
  color: #8a6545;
  font-size: 0.85rem;
  font-weight: 700;
  letter-spacing: 0.14em;
}

.room-list-card__action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2.75rem;
  padding: 0 1.15rem;
  border-radius: 999px;
  background: #4c5d43;
  color: #faf4ea;
  text-decoration: none;
  font-size: 0.84rem;
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
    grid-template-columns: minmax(0, 1fr);
  }

  .room-list-card__footer {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>
