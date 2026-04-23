<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from "vue";
import { parseFaqsBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { useScrollAnimation } from "~/composables/useScrollAnimation";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseFaqsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const openId = ref<string | null>(section.value.items[0]?.id || null);

const { setupScrollAnimations } = useScrollAnimation()

onMounted(async () => {
  await nextTick()
  setupScrollAnimations()
})

onUnmounted(() => {
  // Cleanup handled by composable
})

watch(
  () => section.value.items,
  (items) => {
    openId.value = items[0]?.id || null;
  },
  { deep: true },
);
</script>

<template>
  <section v-if="section.items.length" class="shortcode-faqs-native">
    <div class="faqs-backdrop" />
    <div class="faqs-veil" />
    
    <div class="container faqs-shell animate-on-scroll">
      <header class="faqs-header animate-on-scroll" style="--delay: 100ms">
        <h2 v-if="section.title" class="faqs-title animate-on-scroll" style="--delay: 200ms">
          {{ section.title }}
        </h2>
        <p v-if="section.description" class="faqs-description animate-on-scroll" style="--delay: 300ms">
          {{ section.description }}
        </p>
      </header>
      
      <div class="faqs-grid animate-on-scroll" style="--delay: 400ms">
        <article 
          v-for="(item, index) in section.items" 
          :key="item.id" 
          class="faq-card animate-on-scroll"
          :style="{ '--delay': `${index * 100 + 500}ms` }"
        >
          <button 
            type="button" 
            class="faq-card__button" 
            @click="openId = openId === item.id ? null : item.id"
          >
            <div class="faq-card__question">
              <span class="faq-card__question-text">{{ item.question }}</span>
              <span class="faq-card__toggle">{{ openId === item.id ? "âˆ’" : "+" }}</span>
            </div>
          </button>
          <div 
            v-if="openId === item.id" 
            class="faq-card__answer"
          >
            <div class="faq-card__answer-content">
              {{ item.answer }}
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-faqs-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-faqs-native {
  position: relative;
  padding: clamp(4rem, 8vw, 7rem) 0;
  min-height: 100vh;
  display: flex;
  align-items: center;
}

.faqs-backdrop {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #faf8f5 0%, #f5f2ed 100%);
  z-index: 1;
}

.faqs-veil {
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at top, rgba(138, 110, 72, 0.12) 0%, transparent 50%);
  z-index: 2;
}

.faqs-shell {
  position: relative;
  z-index: 3;
  display: grid;
  gap: 3rem;
  max-width: min(72rem, 92vw);
  margin: 0 auto;
}

.faqs-header {
  text-align: center;
  margin-bottom: 2rem;
}

.faqs-title {
  margin: 0 0 1.5rem 0;
  color: #2d2018;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.8rem, 5vw, 4.5rem);
  font-weight: 600;
  line-height: 0.95;
  opacity: 0;
  transform: translateY(30px);
}

.faqs-title.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.faqs-description {
  margin: 0;
  color: rgba(45, 32, 24, 0.78);
  font-size: 1.1rem;
  line-height: 1.6;
  max-width: 42rem;
  margin: 0 auto;
  opacity: 0;
  transform: translateY(20px);
}

.faqs-description.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.faqs-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  opacity: 0;
  transform: translateY(40px);
}

.faqs-grid.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.faq-card {
  border: 1px solid rgba(45, 32, 24, 0.15);
  border-radius: 1.5rem;
  background: rgba(255, 252, 248, 0.85);
  backdrop-filter: blur(12px);
  box-shadow: 0 20px 60px rgba(45, 32, 24, 0.08);
  overflow: hidden;
  transition: all 0.4s ease;
  opacity: 0;
  transform: translateY(30px);
}

.faq-card.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.faq-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
  border-color: rgba(248, 243, 234, 0.25);
}

.faq-card__button {
  width: 100%;
  padding: 0;
  background: none;
  border: none;
  text-align: left;
  cursor: pointer;
  transition: all 0.3s ease;
}

.faq-card__button:hover {
  background: rgba(255, 249, 240, 0.05);
}

.faq-card__question {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.75rem 2rem;
  gap: 1.5rem;
}

.faq-card__question-text {
  color: #2d2018;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.4rem;
  font-weight: 500;
  line-height: 1.3;
  flex: 1;
}

.faq-card__toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 3rem;
  height: 3rem;
  color: #e3c8a8;
  font-size: 2rem;
  font-weight: 300;
  border-radius: 50%;
  background: rgba(227, 200, 168, 0.1);
  border: 1px solid rgba(227, 200, 168, 0.2);
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.faq-card__button:hover .faq-card__toggle {
  background: rgba(227, 200, 168, 0.15);
  border-color: rgba(227, 200, 168, 0.3);
  transform: scale(1.05);
}

.faq-card__answer {
  border-top: 1px solid rgba(45, 32, 24, 0.1);
  background: rgba(255, 252, 248, 0.03);
  opacity: 1;
  transform: translateY(0);
  transition: all 0.3s ease;
}

.faq-card__answer-content {
  padding: 1.75rem 2rem;
  color: rgba(45, 32, 24, 0.82);
  font-size: 1.05rem;
  line-height: 1.7;
}

/* Scroll animations */
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

/* Responsive Design */
@media (max-width: 1024px) {
  .faqs-shell {
    gap: 2rem;
  }
  
  .faqs-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .faq-card__question {
    padding: 1.5rem 1.75rem;
  }
  
  .faq-card__question-text {
    font-size: 1.25rem;
  }
  
  .faq-card__toggle {
    width: 2.5rem;
    height: 2.5rem;
    font-size: 1.5rem;
  }
  
  .faq-card__answer-content {
    padding: 1.5rem 1.75rem;
    font-size: 1rem;
  }
}

@media (max-width: 768px) {
  .shortcode-faqs-native {
    padding: clamp(3rem, 6vw, 5rem) 0;
    min-height: auto;
  }
  
  .faqs-shell {
    gap: 1.5rem;
  }
  
  .faqs-grid {
    grid-template-columns: 1fr;
  }
  
  .faqs-title {
    font-size: clamp(2.2rem, 4vw, 3.2rem);
  }
  
  .faqs-description {
    font-size: 1rem;
  }
  
  .faq-card__question {
    padding: 1.25rem 1.5rem;
    gap: 1rem;
  }
  
  .faq-card__question-text {
    font-size: 1.1rem;
  }
  
  .faq-card__toggle {
    width: 2.25rem;
    height: 2.25rem;
    font-size: 1.25rem;
  }
  
  .faq-card__answer-content {
    padding: 1.25rem 1.5rem;
    font-size: 0.95rem;
  }
}

@media (max-width: 480px) {
  .faqs-shell {
    gap: 1rem;
  }
  
  .faq-card__question {
    padding: 1rem 1.25rem;
  }
  
  .faq-card__question-text {
    font-size: 1rem;
  }
  
  .faq-card__toggle {
    width: 2rem;
    height: 2rem;
    font-size: 1.1rem;
  }
  
  .faq-card__answer-content {
    padding: 1rem 1.25rem;
    font-size: 0.9rem;
  }
}
</style>




