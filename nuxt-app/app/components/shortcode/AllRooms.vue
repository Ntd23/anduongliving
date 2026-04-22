<script setup lang="ts">
import { computed } from "vue";
import { parseAllRoomsBlock, type ShortcodeBlock, type AllRoomSectionData } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => {
  const parsed = parseAllRoomsBlock(props.block.raw);
  console.log('AllRooms parsed data:', parsed);
  return parsed;
});
const resolveLink = useResolvedCmsLink();
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const sanitizedPagination = useSanitizedCmsHtml(() => section.value.paginationHtml || "");
</script>

<template>
  <section v-if="section.items.length || section.countLabel" class="shortcode-all-rooms-native">
    <div class="all-rooms-shell">
      <header v-if="section.countLabel" class="all-rooms-shell__header">
        <p class="all-rooms-shell__count">
          {{ section.countLabel }}
        </p>
      </header>

      <div class="all-rooms-grid">
        <article
          v-for="item in section.items"
          :key="`${item.id}-${item.name}`"
          class="all-rooms-card"
        >
          <component
            :is="resolveLink(item.url)?.isInternal ? 'NuxtLink' : 'a'"
            :to="resolveLink(item.url)?.isInternal ? resolveLink(item.url)?.href : undefined"
            :href="resolveLink(item.url)?.isInternal ? undefined : resolveLink(item.url)?.href"
            class="all-rooms-card__media"
          >
            <img v-if="item.image" :src="item.image" :alt="item.name" class="all-rooms-card__image">
          </component>

          <div class="all-rooms-card__body">
            <h3 class="all-rooms-card__title">{{ item.name }}</h3>
            <p v-if="item.description" class="all-rooms-card__description">{{ item.description }}</p>
            
            <div v-if="item.size || item.number_of_beds || item.max_adults || item.max_children" class="all-rooms-card__specs">
              <div v-if="item.size" class="all-rooms-card__spec">
                <Icon name="ph:ruler" />
                <span>{{ item.size }}m²</span>
              </div>
              <div v-if="item.number_of_beds" class="all-rooms-card__spec">
                <Icon name="ph:bed" />
                <span>{{ item.number_of_beds }} Bed{{ item.number_of_beds > 1 ? 's' : '' }}</span>
              </div>
              <div v-if="item.max_adults" class="all-rooms-card__spec">
                <Icon name="ph:user" />
                <span>{{ item.max_adults }} Adult{{ item.max_adults > 1 ? 's' : '' }}</span>
              </div>
              <div v-if="item.max_children" class="all-rooms-card__spec">
                <Icon name="ph:child" />
                <span>{{ item.max_children }} Child{{ item.max_children > 1 ? 'ren' : '' }}</span>
              </div>
            </div>
            
            <component
              :is="resolveLink(item.url)?.isInternal ? 'NuxtLink' : 'a'"
              :to="resolveLink(item.url)?.isInternal ? resolveLink(item.url)?.href : undefined"
              :href="resolveLink(item.url)?.isInternal ? undefined : resolveLink(item.url)?.href"
              class="all-rooms-card__action"
            >
              {{ item.bookLabel }}
            </component>
          </div>
        </article>
      </div>

      <div v-if="section.paginationHtml" class="all-rooms-shell__pagination" v-html="sanitizedPagination" />
    </div>
  </section>

  <section v-else class="shortcode-all-rooms-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-all-rooms-native {
  padding: clamp(2.5rem, 5vw, 4rem) 0;
}

.all-rooms-shell {
  display: grid;
  gap: 1.75rem;
}

.all-rooms-shell__count {
  margin: 0;
  color: #241812;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.8rem, 3vw, 2.5rem);
  line-height: 1.05;
}

.all-rooms-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.5rem;
}

.all-rooms-card {
  display: grid;
  grid-template-columns: minmax(10rem, 0.88fr) minmax(0, 1fr);
  overflow: hidden;
  border-radius: 1.5rem;
  border: 1px solid rgba(124, 103, 76, 0.14);
  background: rgba(255, 251, 244, 0.92);
}

.all-rooms-card__media {
  display: block;
  min-height: 100%;
}

.all-rooms-card__image {
  display: block;
  width: 100%;
  height: 100%;
  min-height: 100%;
  object-fit: cover;
}

.all-rooms-card__body {
  display: grid;
  align-content: center;
  gap: 0.85rem;
  padding: 1.35rem 1.4rem;
}

.all-rooms-card__title {
  margin: 0;
  color: #20150f;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.85rem;
  line-height: 1.06;
}

.all-rooms-card__description {
  margin: 0;
  color: rgba(58, 43, 31, 0.76);
  line-height: 1.68;
}

.all-rooms-card__specs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin: 0.75rem 0;
}

.all-rooms-card__spec {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.85rem;
  color: rgba(58, 43, 31, 0.7);
  font-weight: 500;
}

.all-rooms-card__spec svg {
  width: 1rem;
  height: 1rem;
  opacity: 0.7;
}

.all-rooms-card__action {
  width: fit-content;
  color: #4c5d43;
  text-decoration: none;
  font-size: 0.84rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.all-rooms-shell__pagination :deep(.pagination) {
  justify-content: center;
}

@media (max-width: 1024px) {
  .all-rooms-grid {
    grid-template-columns: minmax(0, 1fr);
  }
}

@media (max-width: 640px) {
  .all-rooms-card {
    grid-template-columns: minmax(0, 1fr);
  }
}
</style>
