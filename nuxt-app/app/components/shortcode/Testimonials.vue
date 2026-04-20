<script setup lang="ts">
import { computed, ref } from "vue";
import { parseTestimonialsBlock, type ShortcodeBlock } from "~/utils/shortcode";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseTestimonialsBlock(props.block.raw));
const activeIndex = ref(0);

const activeItem = computed(() => {
  if (!section.value.items.length) return null;
  return section.value.items[activeIndex.value] || section.value.items[0];
});

const showCarousel = computed(() => section.value.items.length > 0 && activeItem.value !== null);
const hasSectionContent = computed(
  () =>
    section.value.title ||
    section.value.subtitle ||
    section.value.description ||
    section.value.backgroundImage ||
    section.value.testimonialIds.length > 0 ||
    showCarousel.value,
);

const goPrev = () => {
  if (!section.value.items.length) return;
  activeIndex.value =
    activeIndex.value === 0
      ? section.value.items.length - 1
      : activeIndex.value - 1;
};

const goNext = () => {
  if (!section.value.items.length) return;
  activeIndex.value =
    activeIndex.value === section.value.items.length - 1
      ? 0
      : activeIndex.value + 1;
};

const goTo = (index: number) => {
  activeIndex.value = index;
};
</script>

<template>
  <section v-if="hasSectionContent" class="shortcode-testimonials">
    <div class="container">
      <div class="testimonials-shell">
        <div class="testimonials-bg-circle"></div>
        <div class="testimonials-bg-lines"></div>
        <div class="testimonials-quote-mark">”</div>

        <div class="testimonials-header">
        
          <p v-if="section.subtitle" class="testimonials-eyebrow">
            {{ section.subtitle }}
          </p>
          <h2 v-if="section.title" class="testimonials-title">
            {{ section.title }}
          </h2>
          <p v-if="section.description" class="testimonials-description">
            {{ section.description }}
          </p>
        </div>

        <article v-if="showCarousel" class="testimonial-spotlight">
          <div class="testimonial-person">
            <div v-if="activeItem!.image?.src" class="testimonial-avatar">
              <img
                :src="activeItem!.image.src"
                :alt="activeItem!.image.alt || activeItem!.name"
                loading="lazy"
              />
            </div>

            <div class="testimonial-meta">
              <h3 class="testimonial-name">{{ activeItem!.name }}</h3>
              <p v-if="activeItem!.title" class="testimonial-role">
                {{ activeItem!.title }}
              </p>
            </div>
          </div>

          <blockquote class="testimonial-content">
            {{ activeItem!.content }}
          </blockquote>

          <div class="testimonial-controls" v-if="section.items.length > 1">
            <button type="button" class="testimonial-arrow" @click="goPrev" aria-label="Previous testimonial">
              <span>‹</span>
            </button>

            <div class="testimonial-dots">
              <button
                v-for="(item, index) in section.items"
                :key="`${item.name}-${index}`"
                type="button"
                class="testimonial-dot"
                :class="{ 'testimonial-dot--active': index === activeIndex }"
                :aria-label="`Go to testimonial ${index + 1}`"
                @click="goTo(index)"
              />
            </div>

            <button type="button" class="testimonial-arrow" @click="goNext" aria-label="Next testimonial">
              <span>›</span>
            </button>
          </div>
        </article>

        <div v-else-if="section.testimonialIds.length" class="testimonial-placeholder">
          <p>
            Đang hiển thị nội dung testimonials cho các ID: {{ section.testimonialIds.join(", ") }}.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-testimonials">
    <div v-html="block.raw" />
  </section>
</template>

<style scoped>
.shortcode-testimonials {
  position: relative;
  overflow: hidden;
  padding: 5rem 0 4rem;
  background: #efefef;
}

.container {
  width: min(1200px, calc(100% - 40px));
  margin: 0 auto;
}

.testimonials-shell {
  position: relative;
  padding: 0 0 2rem;
}

.testimonials-bg-circle {
  position: absolute;
  top: 7rem;
  left: 14%;
  width: min(760px, 72vw);
  aspect-ratio: 1;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.28);
  pointer-events: none;
}

.testimonials-bg-lines {
  position: absolute;
  inset: 10rem auto 1.5rem -2rem;
  width: min(620px, 58vw);
  background-image: radial-gradient(circle at 1px 1px, rgba(205, 176, 131, 0.38) 1px, transparent 0);
  background-size: 18px 18px;
  opacity: 0.25;
  mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.95), rgba(0, 0, 0, 0.2));
  -webkit-mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.95), rgba(0, 0, 0, 0.2));
  pointer-events: none;
}

.testimonials-quote-mark {
  position: absolute;
  top: 11rem;
  right: 1.5rem;
  font-size: clamp(8rem, 13vw, 14rem);
  line-height: 0.8;
  color: #a8b6ab;
  font-weight: 700;
  pointer-events: none;
}

.testimonials-header {
  position: relative;
  z-index: 2;
  max-width: 980px;
  margin-bottom: 2.5rem;
}

.testimonials-eyebrow {
  margin: 0 0 0.75rem;
  color: #d9b942;
  font-size: clamp(1.15rem, 2vw, 1.8rem);
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.02em;
}

.testimonials-title {
  margin: 0;
  color: #006b1f;
  font-size: clamp(1.6rem, 4vw, 2rem);
  line-height: 1.02;
  font-weight: 800;
  text-transform: uppercase;
}

.testimonials-description {
  margin: 1.25rem 0 0;
  max-width: 920px;
  color: #4b4b4b;
  font-size: 1.1rem;
  line-height: 1.75;
}

.testimonial-spotlight {
  position: relative;
  z-index: 2;
  max-width: 980px;
}

.testimonial-person {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 1.75rem;
}

.testimonial-avatar {
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
}

.testimonial-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.testimonial-meta {
  color: #006b1f;
}

.testimonial-name {
  margin: 0;
  font-size: clamp(1rem, 1.5vw, 1.5rem);
  line-height: 1.15;
  font-weight: 800;
}

.testimonial-role {
  margin: 0.75rem 0 0;
  font-size: clamp(1.3rem, 2.6vw, 2rem);
  line-height: 1.3;
  font-weight: 500;
  color: #0c7a2a;
}

.testimonial-content {
  margin: 0;
  max-width: 1100px;
  color: #474747;
  font-size: clamp(1rem, 1.5vw, 1.5rem);
  line-height: 1.45;
  font-weight: 400;
}

.testimonial-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 2.5rem;
}

.testimonial-arrow {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 3.4rem;
  height: 3.4rem;
  border: 2px solid #6a9e70;
  border-radius: 999px;
  background: transparent;
  color: #6a9e70;
  font-size: 2rem;
  cursor: pointer;
  transition:
    background-color 0.2s ease,
    color 0.2s ease,
    transform 0.2s ease;
}

.testimonial-arrow:hover {
  background: #6a9e70;
  color: #fff;
  transform: translateY(-1px);
}

.testimonial-dots {
  display: flex;
  align-items: center;
  gap: 0.7rem;
}

.testimonial-dot {
  width: 0.9rem;
  height: 0.9rem;
  border: 0;
  border-radius: 50%;
  background: #bdbdbd;
  cursor: pointer;
  transition: transform 0.2s ease, background-color 0.2s ease;
}

.testimonial-dot--active {
  background: #00711f;
  transform: scale(1.1);
}

@media (max-width: 991px) {
  .shortcode-testimonials {
    padding: 4rem 0 3rem;
  }

  .testimonials-bg-circle {
    left: 5%;
    width: 85vw;
  }

  .testimonials-quote-mark {
    top: 13rem;
    right: 0;
    font-size: 8rem;
  }

  .testimonial-person {
    align-items: flex-start;
  }

  .testimonial-avatar {
    width: 3.5rem;
    height: 3.5rem;
  }

  .testimonial-content {
    font-size: 1.5rem;
  }
}

@media (max-width: 640px) {
  .container {
    width: min(100%, calc(100% - 24px));
  }

  .testimonials-header {
    margin-bottom: 2rem;
  }

  .testimonials-description {
    font-size: 1rem;
  }

  .testimonial-person {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .testimonial-avatar {
    width: 3rem;
    height: 3rem;
  }

  .testimonial-role {
    font-size: 1.1rem;
  }

  .testimonial-content {
    font-size: 1.2rem;
    line-height: 1.7;
  }

  .testimonials-quote-mark {
    top: 15rem;
    font-size: 6rem;
    opacity: 0.85;
  }

  .testimonial-controls {
    margin-top: 2rem;
  }
}
</style>