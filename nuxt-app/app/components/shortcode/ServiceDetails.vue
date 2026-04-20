<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { parseServiceDetailsBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useScrollAnimation } from "~/composables/useScrollAnimation";

import { Swiper, SwiperSlide } from 'swiper/vue';
import { Pagination } from 'swiper/modules';


import 'swiper/css';
import 'swiper/css/pagination';
const props = defineProps<{
  block: ShortcodeBlock;
}>();
const modules = [Pagination];
const section = computed(() => parseServiceDetailsBlock(props.block.raw));
const hoveredIndex = ref(null);
const activeIndex = ref(0);

const { setupScrollAnimations } = useScrollAnimation();

onMounted(() => {
  setupScrollAnimations();
});

const getGridClass = (index) => {
  const classes = [
    'lg:col-span-1 lg:row-span-1',                               
    'lg:col-span-1 lg:row-span-1 lg:col-start-1 lg:row-start-2', 
    'lg:col-span-2 lg:row-span-2 lg:col-start-2 lg:row-start-1', 
    'lg:col-span-1 lg:row-span-1 lg:col-start-4 lg:row-start-1', 
    'lg:col-span-1 lg:row-span-1 lg:col-start-4 lg:row-start-2', 
  ];
  
  return classes[index] || 'lg:col-span-1 lg:row-span-1';
};
</script>

<template>
  <section v-if="section.title || (section.items && section.items.length)" class="relative py-12 lg:py-32 bg-[#F7F5F1] overflow-hidden">
    
    <div v-if="section.bgImageUrl" class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none">
      <img :src="section.bgImageUrl" alt="Background" class="w-full h-full object-cover">
    </div>

    <div class="container mx-auto px-4 relative z-10">
      
      <div class="text-center max-w-3xl mx-auto mb-10 lg:mb-16 animate-on-scroll">
        <h5 v-if="section.subtitle" class="text-[14px] font-bold text-[#8a5a3c] mb-[10px] uppercase tracking-[1px] flex items-center justify-center gap-2 md:text-base animate-on-scroll">
          <i class="fal fa-star text-yellow-400"></i>
          {{ section.subtitle }}
          <i class="fal fa-star text-yellow-400"></i>
        </h5>
        <h2 v-if="section.title" class="text-[53.36px] text-[#7b4d35] font-medium mb-[20px] font-serif md:text-5xl leading-tight animate-on-scroll">
          {{ section.title }}
        </h2>
        <p v-if="section.description" class="text-[18px] leading-[1.76] text-[rgba(69,53,43,0.88)] max-w-[700px] mx-auto animate-on-scroll">

          {{ section.description }}

        </p>
      </div>

      <div class="hidden lg:grid grid-cols-4 grid-rows-2 gap-6 min-h-[500px] relative">
        <div 
          class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 transition-all duration-300 pointer-events-none rounded-xl"
          :class="hoveredIndex !== null ? 'opacity-100' : 'opacity-0'"
        ></div>

        <div 
          v-for="(item, index) in section.items" 
          :key="`desktop-${index}`"
          class="group relative rounded-xl overflow-hidden shadow-md cursor-pointer transition-all duration-500"
          :class="[
            getGridClass(index),
            hoveredIndex === index ? 'scale-[1.03] z-20 shadow-2xl' : (hoveredIndex !== null ? 'z-0 scale-95' : 'z-10 hover:-translate-y-1')
          ]"
          @mouseenter="hoveredIndex = index"
          @mouseleave="hoveredIndex = null"
        >
          <img v-if="item.thumbImgUrl" :src="item.thumbImgUrl" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
          <div class="absolute inset-x-0 bottom-0 p-6 pt-16 bg-gradient-to-t from-black/90 via-black/40 to-transparent">
            <h3 class="text-xl font-bold text-white drop-shadow-md animate-on-scroll">{{ item.title }}</h3>
          </div>
        </div>
      </div>

      <div class="lg:hidden service-details-swiper">
        <swiper
          :modules="modules"
          :slides-per-view="1"
          :space-between="20"
          :pagination="{ clickable: true }"
          class="rounded-2xl shadow-lg"
        >
          <swiper-slide 
            v-for="(item, index) in section.items" 
            :key="`mobile-${index}`"
            class="relative aspect-[3/2] overflow-hidden"
          >
            <img :src="item.thumbImgUrl" class="w-full h-full object-cover">
            <div class="absolute inset-x-0 bottom-0 p-8 pt-20 bg-gradient-to-t from-black/90 via-black/20 to-transparent">
              <h3 class="text-2xl font-bold text-white">{{ item.title }}</h3>
            </div>
          </swiper-slide>
        </swiper>
      </div>

    </div>
  </section>

  <section v-else class="shortcode-service-details-fallback">
    <div v-html="block?.raw || ''" />
  </section>
</template>

<style scoped>
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

.animate-on-scroll.delay-100 {
  transition-delay: var(--delay, 100ms);
}

.animate-on-scroll:nth-child(1) {
  transition-delay: 0.1s;
}

.animate-on-scroll:nth-child(2) {
  transition-delay: 0.2s;
}

.animate-on-scroll:nth-child(3) {
  transition-delay: 0.3s;
}

.animate-on-scroll:nth-child(4) {
  transition-delay: 0.4s;
}

.animate-on-scroll:nth-child(5) {
  transition-delay: 0.5s;
}

.service-details-swiper :deep(.swiper-pagination) {
  position: relative;
  bottom: 0;
  margin-top: 15px;
}

.service-details-swiper :deep(.swiper-pagination-bullet) {
  width: 10px;
  height: 10px;
  background: #d1d5db;
  opacity: 1;
  transition: all 0.3s ease;
}

.service-details-swiper :deep(.swiper-pagination-bullet-active) {
  background: #cda274;
  width: 30px;
  border-radius: 5px;
}
</style>