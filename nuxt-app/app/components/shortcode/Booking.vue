<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { parseBookingBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useScrollAnimation } from "~/composables/useScrollAnimation";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseBookingBlock(props.block.raw));

// Setup scroll animations
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
    v-if="section.title || section.imageSrc" 
    class="relative flex items-center min-h-[80vh] bg-cover bg-center lg:bg-fixed"
    :style="{ backgroundImage: section.imageSrc ? `url(${section.imageSrc})` : '' }"
  >
    <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-black/40 z-0"></div>

    <div class="container mx-auto px-4 relative z-10 py-24 lg:py-32">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
        <div class="lg:col-span-6 text-white">
          <div class="animate-on-scroll">
            <h5 v-if="section.subtitle" class="text-[#cda274] uppercase font-bold mb-4 tracking-[0.2em] flex items-center gap-2 text-sm md:text-base animate-on-scroll">
              <i class="fal fa-star text-yellow-400"></i> 
              {{ section.subtitle }}
            </h5>
            <h2 v-if="section.title" class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 drop-shadow-lg leading-tight animate-on-scroll">
              {{ section.title }}
            </h2>
            <p class="text-lg text-white/80 leading-relaxed max-w-xl animate-on-scroll">
              {{ section.description ||"Thực hiện đặt phòng nhanh chóng và thuận tiện chỉ với vài bước đơn giản. Chọn thời gian lưu trú, loại phòng phù hợp và tận hưởng kỳ nghỉ thoải mái với dịch vụ chuyên nghiệp của chúng tôi." }}
            </p>
          </div>
        </div>

        <div class="lg:col-span-5 lg:col-start-8">
          <div class="bg-white p-6 md:p-8 lg:p-10 rounded-2xl shadow-2xl border-t-4 border-[#cda274] relative animate-on-scroll">
            
            <div class="mb-8 border-b border-gray-100 pb-4">
              <h4 v-if="section.subtitle" class="text-2xl font-bold text-gray-900 mb-2 animate-on-scroll">Đặt phòng ngay</h4>
              <p class="text-gray-500 text-sm animate-on-scroll">Cam kết giá tốt nhất khi đặt trực tiếp</p>
            </div>

            <form :action="section.actionUrl || '/booking'" method="post" class="space-y-5">
              <input type="hidden" name="_token" value="" autocomplete="off">
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="animate-on-scroll">
                  <label for="booking-form-start-date" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fal fa-calendar-check text-[#cda274] mr-2"></i>Nhận phòng
                  </label>
                  <input 
                    type="date" 
                    id="booking-form-start-date" 
                    name="start_date"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-[#cda274] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#cda274] transition-colors"
                  >
                </div>

                <div class="animate-on-scroll">
                  <label for="booking-form-end-date" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fal fa-calendar-times text-[#cda274] mr-2"></i>Trả phòng
                  </label>
                  <input 
                    type="date" 
                    id="booking-form-end-date" 
                    name="end_date"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-[#cda274] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#cda274] transition-colors"
                  >
                </div>
              </div>

              <div class="animate-on-scroll">
                <label for="adults" class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fal fa-users text-[#cda274] mr-2"></i>Số lượng khách
                </label>
                <select 
                  name="adults" 
                  id="adults"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-[#cda274] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#cda274] transition-colors appearance-none"
                >
                  <option v-for="n in 10" :key="n" :value="n">{{ n }} Người lớn</option>
                </select>
              </div>

              <div class="animate-on-scroll">
                <label for="room" class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fal fa-bed text-[#cda274] mr-2"></i>Hạng phòng
                </label>
                <select 
                  name="room_id" 
                  id="room"
                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-[#cda274] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#cda274] transition-colors appearance-none"
                >
                  <option v-for="room in section.rooms" :key="room.value" :value="room.value">
                    {{ room.label }}
                  </option>
                </select>
              </div>

              <div class="pt-4">
                <button 
                  type="submit" 
                  class="w-full bg-[#cda274] hover:bg-[#b58c60] text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_10px_20px_rgba(205,162,116,0.3)] flex items-center justify-center uppercase tracking-wider animate-on-scroll"
                >
                  <span>Xác nhận đặt phòng</span> 
                  <i class="fal fa-long-arrow-right ml-3 text-lg"></i>
                </button>
              </div>
            </form>

          </div>
        </div>
        
      </div>
    </div>
  </section>

  <section v-else class="shortcode-booking-fallback">
    <div v-html="block.raw" />
  </section>
</template>