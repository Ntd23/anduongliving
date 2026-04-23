<script setup lang="ts">
import { computed, ref } from "vue";
import { parseAllRoomsBlock, type ShortcodeBlock, type AllRoomSectionData } from "~/utils/shortcode";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

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

// Tab state for each room
const activeTabs = ref<Record<string, 'description' | 'amenities'>>({});

const setActiveTab = (roomId: string, tab: 'description' | 'amenities') => {
  activeTabs.value[roomId] = tab;
};

const getActiveTab = (roomId: string): 'description' | 'amenities' => {
  return activeTabs.value[roomId] || 'description';
};

// Handle real image arrays - item.images may contain multiple images
const getRoomImages = (item: any): string[] => {
  // If item.images is an array, use it
  if (item.images && Array.isArray(item.images)) {
    return item.images.filter((img: string) => img && img.trim() !== '');
  }
  console.log('getRoomImages', item);
  
  // If item.image is a string, check if it's a JSON array or single image
  if (item.image) {
    try {
      // Try to parse as JSON array
      const parsed = JSON.parse(item.image);
      if (Array.isArray(parsed)) {
        return parsed.filter((img: string) => img && img.trim() !== '');
      }
    } catch (e) {
      // Not JSON, treat as single image
      return [item.image];
    }
  }
  
  return [];
};

// Swiper modules
const swiperModules = [Navigation, Pagination];

// Swiper configuration functions
const navigationConfig = (roomId: number) => ({
  nextEl: `.swiper-next-${roomId}`,
  prevEl: `.swiper-prev-${roomId}`,
});

const paginationConfig = (roomId: number) => ({
  el: `.swiper-pagination-${roomId}`,
  type: 'fraction' as const,
  clickable: true,
});

// Slider state for each room
const activeSlides = ref<Record<string, number>>({});

const setActiveSlide = (roomId: string, slideIndex: number) => {
  activeSlides.value[roomId] = slideIndex;
};

const getActiveSlide = (roomId: string): number => {
  return activeSlides.value[roomId] || 0;
};

const nextSlide = (roomId: string, totalSlides: number) => {
  const currentSlide = getActiveSlide(roomId);
  if (currentSlide < totalSlides - 1) {
    setActiveSlide(roomId, currentSlide + 1);
  }
};

const prevSlide = (roomId: string) => {
  const currentSlide = getActiveSlide(roomId);
  if (currentSlide > 0) {
    setActiveSlide(roomId, currentSlide - 1);
  }
};
</script>

<template>
  <section v-if="section.items.length || section.countLabel" class="shortcode-all-rooms-native">
    <div class="all-rooms-shell">
      <div class="all-rooms-list">
        <article
          v-for="item in section.items"
          :key="`${item.id}-${item.name}`"
          class="all-rooms-card"
          :class="{ 'all-rooms-card--reverse': item.id % 2 === 0 }"
        >
          <div class="all-rooms-card__media">
            <div v-if="getRoomImages(item).length > 0" class="all-rooms-card__slider">
              <Swiper
                :modules="swiperModules"
                :slides-per-view="1"
                :space-between="0"
                :navigation="navigationConfig(item.id)"
                :pagination="paginationConfig(item.id)"
                :loop="false"
                class="room-swiper"
              >
                <SwiperSlide v-for="(image, index) in getRoomImages(item)" :key="index">
                  <component
                    :is="resolveLink(item.url)?.isInternal ? 'NuxtLink' : 'a'"
                    :to="resolveLink(item.url)?.isInternal ? resolveLink(item.url)?.href : undefined"
                    :href="resolveLink(item.url)?.isInternal ? undefined : resolveLink(item.url)?.href"
                    class="swiper-slide-link"
                  >
                    <img :src="image" :alt="`${item.name} - Image ${index + 1}`" class="all-rooms-card__image">
                  </component>
                </SwiperSlide>
              </Swiper>
              
              <!-- Custom pagination - bottom left -->
              <div v-if="getRoomImages(item).length > 1" :class="['swiper-pagination', `swiper-pagination-${item.id}`]"></div>
              
              <!-- Custom navigation - bottom right -->
              <div v-if="getRoomImages(item).length > 1" class="swiper-navigation">
                <button :class="['swiper-prev', `swiper-prev-${item.id}`]" class="swiper-nav-button swiper-nav-prev">
                  <Icon name="ph:caret-left" />
                </button>
                <button :class="['swiper-next', `swiper-next-${item.id}`]" class="swiper-nav-button swiper-nav-next">
                  <Icon name="ph:caret-right" />
                </button>
              </div>
            </div>
            <div v-else class="all-rooms-card__no-image">
              <div class="no-image-placeholder">
                <Icon name="ph:image" />
                <span>No Image Available</span>
              </div>
            </div>
          </div>

          <div class="all-rooms-card__content">
            <div class="all-rooms-card__header">
              <h3 class="all-rooms-card__title">{{ item.name }}</h3>
              
              <div v-if="item.size || item.number_of_beds || item.max_adults || item.max_children" class="all-rooms-card__specs">
                <div v-if="item.size" class="all-rooms-card__spec">
                  <Icon name="ph:ruler" />
                  <span>{{ item.size }}m²</span>
                </div>
                <div v-if="item.number_of_beds" class="all-rooms-card__spec">
                  <Icon name="ph:bed" />
                  <span>{{ item.number_of_beds }} Phòng ngủ</span>
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
            </div>

            <div class="all-rooms-card__tabs">
              <div class="all-rooms-card__tab-buttons">
                <button
                  @click="setActiveTab(`${item.id}-${item.name}`, 'description')"
                  :class="{ 'all-rooms-card__tab-button--active': getActiveTab(`${item.id}-${item.name}`) === 'description' }"
                  class="all-rooms-card__tab-button"
                >
                  Description
                </button>
                <button
                  v-if="item.amenities && item.amenities.length > 0"
                  @click="setActiveTab(`${item.id}-${item.name}`, 'amenities')"
                  :class="{ 'all-rooms-card__tab-button--active': getActiveTab(`${item.id}-${item.name}`) === 'amenities' }"
                  class="all-rooms-card__tab-button"
                >
                  Amenities
                </button>
              </div>

              <div class="all-rooms-card__tab-content">
                <div
                  v-if="getActiveTab(`${item.id}-${item.name}`) === 'description'"
                  class="all-rooms-card__tab-pane"
                >
                  <p v-if="item.description" class="all-rooms-card__description">{{ item.description }}</p>
                  <p v-else class="all-rooms-card__no-content">No description available</p>
                </div>
                <div
                  v-if="getActiveTab(`${item.id}-${item.name}`) === 'amenities'"
                  class="all-rooms-card__tab-pane"
                >
                  <div v-if="item.amenities && item.amenities.length > 0" class="all-rooms-card__amenities">
                    <div v-for="amenity in item.amenities" :key="amenity" class="all-rooms-card__amenity">
                      <span>{{ amenity }}</span>
                    </div>
                  </div>
                  <p v-else class="all-rooms-card__no-content">No amenities available</p>
                </div>
              </div>
            </div>

            <div class="all-rooms-card__footer">
              <component
                :is="resolveLink(item.url)?.isInternal ? 'NuxtLink' : 'a'"
                :to="resolveLink(item.url)?.isInternal ? resolveLink(item.url)?.href : undefined"
                :href="resolveLink(item.url)?.isInternal ? undefined : resolveLink(item.url)?.href"
                class="all-rooms-card__action"
              >
                {{ item.bookLabel }}
              </component>
            </div>
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

.all-rooms-list {
  display: flex;
  flex-direction: column;
  gap: 3rem;
}

.all-rooms-card {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: center;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.all-rooms-card--reverse {
  direction: rtl;
}

.all-rooms-card--reverse > * {
  direction: ltr;
}

.all-rooms-card__media {
  position: relative;
  aspect-ratio: 4/3;
  overflow: hidden;
}

.all-rooms-card__slider {
  position: relative;
  width: 100%;
  height: 100%;
}

.room-swiper {
  width: 100%;
  height: 100%;
}

.swiper-slide-link {
  display: block;
  width: 100%;
  height: 100%;
  text-decoration: none;
}

.all-rooms-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.all-rooms-card__no-image {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
}

.no-image-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  color: #999;
}

.no-image-placeholder svg {
  width: 3rem;
  height: 3rem;
}

.no-image-placeholder span {
  font-size: 0.9rem;
  font-weight: 500;
}

/* Swiper Pagination */
.swiper-pagination {
  position: absolute !important;
  bottom: 1rem !important;
  left: 1rem !important;
  right: auto !important;
  width: auto !important;
  background: rgba(0, 0, 0, 0.633) !important;
  padding: 0.5rem 0.75rem !important;
  border-radius: 0.25rem !important;
  border: 2px solid rgba(124, 103, 76, 0.3) !important;
  font-size: 0.75rem !important;
  font-weight: 600 !important;
  color: #ffffff !important;
  z-index: 10 !important;
  transform: translate3d(0, 0, 0) !important;
}

.swiper-pagination-fraction {
  font-family: inherit !important;
}

/* Swiper Navigation */
.swiper-navigation {
  position: absolute !important;
  bottom: 1rem !important;
  right: 1rem !important;
  display: flex !important;
  gap: 0.5rem !important;
  z-index: 10 !important;
  transform: translate3d(0, 0, 0) !important;
}

.swiper-nav-button {
  width: 2.5rem;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.633);
  border: 2px solid rgba(124, 103, 76, 0.3);
  border-radius: 0.25rem;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  z-index: 15;
}

.swiper-nav-button:hover {
  background: #4c5d43;
  border-color: #4c5d43;
  color: white;
}

.swiper-nav-button.swiper-button-disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background: rgba(200, 200, 200, 0.5);
  border-color: rgba(200, 200, 200, 0.3);
  color: #999;
}

.swiper-nav-button svg {
  width: 1.2rem;
  height: 1.2rem;
}

/* Swiper overrides and fixes */
.room-swiper {
  --swiper-navigation-size: 24px !important;
  --swiper-pagination-color: #4c674c !important;
  --swiper-pagination-bullet-size: 8px;
  --swiper-pagination-bullet-horizontal-gap: 6px;
}

/* Force Swiper navigation to be visible */
.swiper-button-next,
.swiper-button-prev {
  display: none !important; /* Hide default Swiper buttons */
}

/* Ensure custom navigation is always visible */
.swiper-navigation .swiper-nav-button {
  display: flex !important;
}

.all-rooms-card__content {
  display: flex;
  flex-direction: column;
  padding: 3rem;
  gap: 2rem;
}

.all-rooms-card__header {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.all-rooms-card__title {
  margin: 0;
  color: #1a1a1a;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 2.5rem;
  font-weight: 600;
  line-height: 1.2;
}

.all-rooms-card__specs {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.all-rooms-card__spec {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: rgba(124, 103, 76, 0.1);
  border-radius: 2rem;
  font-size: 0.9rem;
  color: #7c674c;
  font-weight: 500;
}

.all-rooms-card__spec svg {
  width: 1.2rem;
  height: 1.2rem;
  color: #7c674c;
}

.all-rooms-card__tabs {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.all-rooms-card__tab-buttons {
  display: flex;
  gap: 0.5rem;
  border-bottom: 2px solid rgba(124, 103, 76, 0.2);
}

.all-rooms-card__tab-button {
  padding: 0.75rem 1.5rem;
  background: none;
  border: none;
  color: #7c674c;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
}

.all-rooms-card__tab-button:hover {
  color: #4c5d43;
}

.all-rooms-card__tab-button--active {
  color: #4c5d43;
}

.all-rooms-card__tab-button--active::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 2px;
  background: #4c5d43;
}

.all-rooms-card__tab-content {
  min-height: 150px;
}

.all-rooms-card__description {
  margin: 0;
  color: #666;
  line-height: 1.8;
  font-size: 1rem;
}

.all-rooms-card__no-content {
  margin: 0;
  color: #999;
  font-style: italic;
}

.all-rooms-card__amenities {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.all-rooms-card__amenity {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: rgba(124, 103, 76, 0.05);
  border-radius: 0.5rem;
  font-size: 0.95rem;
  color: #666;
}

.all-rooms-card__amenity::before {
  content: '✓';
  display: flex;
  align-items: center;
  justify-content: center;
  width: 1.5rem;
  height: 1.5rem;
  background: #4c5d43;
  color: white;
  border-radius: 50%;
  font-size: 0.8rem;
  font-weight: bold;
}

.all-rooms-card__footer {
  margin-top: auto;
}

.all-rooms-card__action {
  display: inline-block;
  padding: 1rem 2rem;
  background: #4c5d43;
  color: white;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
}

.all-rooms-card__action:hover {
  background: #3a4734;
  transform: translateY(-2px);
}

.all-rooms-shell__pagination :deep(.pagination) {
  justify-content: center;
}

@media (max-width: 1024px) {
  .all-rooms-card {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .all-rooms-card--reverse {
    direction: ltr;
  }
  
  .all-rooms-card__content {
    padding: 2rem;
  }
  
  .all-rooms-card__title {
    font-size: 2rem;
  }
}

@media (max-width: 768px) {
  .all-rooms-list {
    gap: 2rem;
  }
  
  .all-rooms-card {
    gap: 1.5rem;
  }
  
  .all-rooms-card__content {
    padding: 1.5rem;
    gap: 1.5rem;
  }
  
  .all-rooms-card__title {
    font-size: 1.75rem;
  }
  
  .all-rooms-card__specs {
    gap: 1rem;
  }
  
  .all-rooms-card__spec {
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
  }
  
  .all-rooms-card__amenities {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .all-rooms-card__content {
    padding: 1rem;
    gap: 1rem;
  }
  
  .all-rooms-card__title {
    font-size: 1.5rem;
  }
  
  .all-rooms-card__tab-button {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
  }
  
  .all-rooms-card__action {
    padding: 0.75rem 1.5rem;
    font-size: 0.85rem;
  }
  
  .swiper-pagination {
    bottom: 0.5rem;
    left: 0.5rem !important;
    padding: 0.4rem 0.6rem;
    font-size: 0.65rem;
  }
  
  .swiper-navigation {
    bottom: 0.5rem;
    right: 0.5rem;
  }
  
  .swiper-nav-button {
    width: 2rem;
    height: 2rem;
  }
  
  .swiper-nav-button svg {
    width: 1rem;
    height: 1rem;
  }
}
</style>
