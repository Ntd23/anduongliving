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

const modules = [Navigation, Pagination];

const swiperOptions = {
  slidesPerView: 1,
  spaceBetween: 20,
  loop: true,
  navigation: false,
  pagination: {
    clickable: true,
    renderBullet: (index: number, className: string) => {
      return `<span class="${className}"></span>`;
    },
  },
  breakpoints: {
    640: {
      slidesPerView: 1,
      spaceBetween: 20,
    },
    768: {
      slidesPerView: 1,
      spaceBetween: 30,
    },
    1024: {
      slidesPerView: 1,
      spaceBetween: 30,
    },
  },
};

const onSwiper = (swiper: SwiperType) => {
  swiperRef.value = swiper;
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
        
        <div class="custom-nav-button prev" @click="goPrev">
          <Icon name="heroicons:arrow-left" class="text-4xl" />
        </div>
        <div class="custom-nav-button next" @click="goNext">
          <Icon name="heroicons:arrow-right" class="text-4xl" />
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
  padding-bottom : 50px;
  position: relative;
}

.section-header {
  margin-bottom: 50px;
}

.section-header h5 {
  font-size: 18px;
  font-weight: 600;
  color: #666;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.section-header h2 {
  font-size: 36px;
  font-weight: 700;
  color: #333;
  margin-bottom: 20px;
}

.section-header p {
  font-size: 16px;
  line-height: 1.6;
  color: #666;
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
  opacity: 0.3;
  transform: scale(0.9);
  transition: all 0.3s ease;
}

.services-swiper :deep(.swiper-slide-active) {
  opacity: 1;
  transform: scale(1);
}

.services-swiper :deep(.swiper-slide-prev),
.services-swiper :deep(.swiper-slide-next) {
  opacity: 0;
  transform: scale(0.8);
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
  padding: 20px;
  display: block;
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
  background: white;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
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
  left: 25px;
}

.custom-nav-button.next {
  right: 25px;
}

.custom-nav-button:hover {
  transform: translateY(-50%) scale(1.1);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
  background: var(--retreat-clay);
  color: white;
}

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
</style>
