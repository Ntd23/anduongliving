<script setup lang="ts">
import { computed, ref, onMounted } from "vue";
import { parseServiceBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination } from 'swiper/modules';
import type { Swiper as SwiperType } from 'swiper';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseServiceBlock(props.block.raw));
const swiperRef = ref<SwiperType>();
const isAtStart = ref(true);
const isAtEnd = ref(false);

const modules = [Navigation, Pagination];

const swiperOptions = {
  slidesPerView: 1,
  spaceBetween: 20,
  loop: false,
  navigation: false,
  pagination: {
    clickable: true,
    type: 'bullets' as const,
  },
  breakpoints: {
    640: {
      slidesPerView: 1,
      spaceBetween: 20,
    },
    768: {
      slidesPerView: 2,
      spaceBetween: 30,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 30,
    },
  },
};

const onSwiper = (swiper: SwiperType) => {
  swiperRef.value = swiper;
  
  const updateButtonStates = () => {
    isAtStart.value = swiper.isBeginning;
    isAtEnd.value = swiper.isEnd;
  };
  
  updateButtonStates();
  
  swiper.on('slideChange', updateButtonStates);
  swiper.on('reachBeginning', () => { isAtStart.value = true; });
  swiper.on('reachEnd', () => { isAtEnd.value = true; });
};

const goPrev = () => {
  if (swiperRef.value) {
    swiperRef.value.slidePrev();
  }
};

const goNext = () => {
  if (swiperRef.value) {
    swiperRef.value.slideNext();
  }
};

const { setupScrollAnimations } = useScrollAnimation({
  root: null,
  rootMargin: '0px',
  threshold: 0.1,
  animationClass: 'animate-on-scroll',
  inClass: 'animate-in',
  outClass: 'animate-out'
});

onMounted(() => {
  setupScrollAnimations();
});
</script>

<template>
  <section
    v-if="section.title || section.subtitle || section.items.length"
    class="shortcode-services"
  >
    <div class="container">
      <div v-if="section.subtitle || section.title" class="section-header text-center mb-50">
        <h5 v-if="section.subtitle" class="animate-on-scroll">{{ section.subtitle }}</h5>
        <h2 v-if="section.title" class="animate-on-scroll">{{ section.title }}</h2>
        <p v-if="section.description" class="animate-on-scroll">{{ section.description }}</p>
      </div>
      
      <div class="services-swiper-container">
        <Swiper
          :modules="modules"
          :slides-per-view="swiperOptions.slidesPerView"
          :space-between="swiperOptions.spaceBetween"
          :loop="swiperOptions.loop"
          :navigation="swiperOptions.navigation"
          :pagination="swiperOptions.pagination"
          :breakpoints="swiperOptions.breakpoints"
          @swiper="onSwiper"
          class="services-swiper"
        >
          <SwiperSlide 
            v-for="item in section.items" 
            :key="item.id"
          >
            <article class="service-card animate-on-scroll">
              <div class="service-image">
                <img :src="item.image" :alt="item.name" />
              </div>
              
              <div class="service-content">
                <h3>
                  <a :href="item.url">{{ item.name }}</a>
                </h3>
                
                <NuxtLink :to="item.url" class="book-button">
                  {{ item.bookLabel }}
                </NuxtLink>
              </div>
            </article>
          </SwiperSlide>
        </Swiper>
        
        <div 
          class="custom-nav-button prev" 
          :class="{ disabled: isAtStart }"
          @click="goPrev"
        >
          <Icon name="heroicons:chevron-left" class="text-4xl w-15 h-15" />
        </div>
        <div 
          class="custom-nav-button next" 
          :class="{ disabled: isAtEnd }"
          @click="goNext"
        >
          <Icon name="heroicons:chevron-right" class="text-4xl w-15 h-15" />
        </div>
      </div>
    </div>
  </section>

  <section v-else>
    <div v-html="block.raw" />
  </section>
</template>

<style scoped>
.shortcode-services {
  padding-top: 90px;
  padding-bottom : 90px;
  position: relative;
}

.section-header {
  margin-bottom: 50px;
}

.section-header h5 {
  font-size: 14px;
  font-weight: 700;
  color: #8a5a3c;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.section-header h2 {
  font-size: 53.36px;
  color: #7b4d35;
  font-weight: 500;
  margin-bottom: 20px;
  font-family: "Times New Roman", Georgia, serif;
}

.section-header p {
  font-size: 18px;
  line-height: 1.76;
  color: rgba(69, 53, 43, 0.88);
  max-width: 600px;
  margin: 0 auto;
}

.services-swiper-container {
  position: relative;
  margin-top: 50px;
  overflow: hidden;
}

.services-swiper {
  padding: 0 60px;
}

.services-swiper :deep(.swiper-slide) {
  opacity: 1;
  transform: scale(1);
  transition: all 0.3s ease;
}

.service-card {
  background: white;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
  border-radius: 12px;
  padding-bottom : 20px;
}

.service-image {
  width: 100%;
  height: 560px;
  overflow: hidden;
  border-radius: 12px 12px 0 0;
}

.service-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
}

/* Responsive adjustments for tablet and desktop */
@media (min-width: 768px) {
  .services-swiper .service-image {
    height: 400px !important;
  }
  
  .services-swiper .service-content {
    padding: 15px;
  }
}

@media (min-width: 1024px) {
  .services-swiper .service-image {
    height: 320px !important;
  }
  
  .services-swiper .service-content {
    padding: 12px;
  }
  
  .services-swiper .service-content h3 {
    font-size: 18px;
    margin: 0 0 10px 0;
  }
  
  .services-swiper .book-button {
    font-size: 14px;
    padding: 8px 16px;
  }
}

.service-content {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
  text-align: center;
  box-sizing: border-box;
}

.service-content h3 {
  margin: 0 0 15px 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #9f6f49;
}

.service-content h3 a {
  color: inherit;
  text-decoration: none;
  transition: color 0.3s ease;
}

.service-content h3 a:hover {
  color: #e19720;
}


.book-button {
  display: inline-block;
  padding: 12px 24px;
  background: linear-gradient(135deg, var(--retreat-clay), #9f6f49);
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 500;
  width: fit-content;
  align-self: center;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.book-button:hover {
  transform: translateY(-1px);
  box-shadow: 0 18px 36px rgba(185, 130, 90, 0.28);
}

.custom-nav-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 70px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
  color: var(--retreat-clay);
  font-size: 24px;
}

.custom-nav-button.prev {
  left: 0;
}

.custom-nav-button.next {
  right: 0;
}

.custom-nav-button.disabled {
  opacity: 0.3;
  cursor: not-allowed;
  pointer-events: none;
}

/* .custom-nav-button:not(.disabled):hover {
  
} */

:deep(.swiper-pagination) {
  position: relative;
  margin-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

:deep(.swiper-pagination-bullet) {
  background: #9f6f49 !important;
  opacity: 0.5;
  transition: all 0.3s ease;
  width: 10px !important;
  height: 10px !important;
  margin: 0 5px !important;
  border-radius: 50% !important;
}

:deep(.swiper-pagination-bullet-active) {
  opacity: 1 !important;
  transform: scale(1.2) !important;
  background: #9f6f49 !important;
}

@media (max-width: 768px) {
  .services-swiper {
    padding: 0 20px;
  }
  
  .custom-nav-button {
    display: none;
  }
  
  .service-image {
    height: 300px;
  }
  
  .service-content {
    padding: 15px;
  }
  
  .service-content h3 {
    font-size: 1.2rem;
    margin-bottom: 10px;
  }
  
  .book-button {
    padding: 10px 20px;
    font-size: 14px;
  }
  
  .section-header h2 {
    font-size: 28px;
  }
}

@media (min-width: 769px) {
  :deep(.swiper-pagination) {
    display: none !important;
  }
  
  .custom-nav-button {
    display: flex;
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.animate-on-scroll {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.8s ease-out;
  color: #e3c8a8
}

.animate-on-scroll.animate-in {
  opacity: 1;
  transform: translateY(0);
}

.animate-on-scroll.animate-out {
  opacity: 0;
  transform: translateY(30px);
}

.services-swiper .swiper-slide:nth-child(1) .service-card.animate-on-scroll {
  transition-delay: 0.1s;
}

.services-swiper .swiper-slide:nth-child(2) .service-card.animate-on-scroll {
  transition-delay: 0.2s;
}

.services-swiper .swiper-slide:nth-child(3) .service-card.animate-on-scroll {
  transition-delay: 0.3s;
}

.services-swiper .swiper-slide:nth-child(4) .service-card.animate-on-scroll {
  transition-delay: 0.4s;
}

.services-swiper .swiper-slide:nth-child(5) .service-card.animate-on-scroll {
  transition-delay: 0.5s;
}

/* Swiper pagination styling for mobile */
:deep(.swiper-pagination) {
  position: relative;
  margin-top: 20px;
  text-align: center;
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-bottom: 20px;
}

:deep(.swiper-pagination-bullet) {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 2px solid #e0e0e0;
  background: #f8f9fa;
  cursor: pointer;
  transition: all 0.3s ease;
  opacity: 1;
}

:deep(.swiper-pagination-bullet:hover) {
  background: #9f6f49;
  transform: scale(1.1);
}

:deep(.swiper-pagination-bullet-active) {
  background: #9f6f49;
  border-color: #9f6f49;
  transform: scale(1.2);
}

/* Hide pagination on desktop, show on mobile */
@media (min-width: 768px) {
  :deep(.swiper-pagination) {
    display: none !important;
  }
}

@media (max-width: 767px) {
  :deep(.swiper-pagination) {
    display: flex !important;
  }
}

</style>
