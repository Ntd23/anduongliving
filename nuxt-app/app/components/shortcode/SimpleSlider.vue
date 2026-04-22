<script setup lang="ts">
import { parseSimpleSliderBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { decodeHtmlEntities } from "~/utils/shortcode/core";
import { useResolvedCmsLink } from "~/composables/useResolvedCmsLink";
import { useResolvedCmsAsset } from "~/composables/useResolvedCmsAsset";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseSimpleSliderBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const resolveAsset = useResolvedCmsAsset();
const activeIndex = ref(0);
let autoplayTimer: ReturnType<typeof setInterval> | null = null;

const normalizeCaptionText = (value: string | null | undefined) =>
  decodeHtmlEntities((value || "").replace(/<[^>]+>/g, " ").replace(/\s+/g, " ").trim());

const fallbackCaptions = computed(() =>
  Array.from(
    props.block.raw.matchAll(
      /<p\b[^>]*class=(['"])[^"'<>]*slider-showcase-caption[^"'<>]*\1[^>]*>([\s\S]*?)<\/p>/gi,
    ),
  ).map((match) => normalizeCaptionText(match[2])),
);

const slides = computed(() =>
  section.value.slides.map((slide, index) => ({
    ...slide,
    id: `${index}-${slide.image?.src || slide.caption || "slide"}`,
    caption: normalizeCaptionText(slide.caption) || normalizeCaptionText(slide.image?.alt) || "",
    link: resolveLink(slide.href),
  })),
);

const displayCaptions = computed(() =>
  slides.value.map(
    (slide, index) =>
      fallbackCaptions.value[index] ||
      slide.caption ||
      "",
  ),
);

const activeCaption = computed(() => displayCaptions.value[activeIndex.value] || "");

const stopAutoplay = () => {
  if (autoplayTimer) {
    clearInterval(autoplayTimer);
    autoplayTimer = null;
  }
};

const startAutoplay = () => {
  stopAutoplay();

  if (slides.value.length < 2) {
    return;
  }

  autoplayTimer = setInterval(() => {
    activeIndex.value = (activeIndex.value + 1) % slides.value.length;
  }, 6000);
};

const goToSlide = (index: number) => {
  activeIndex.value = index;
  startAutoplay();
};

/* ── Touch swipe ── */
let pointerStartX = 0;
let pointerStartY = 0;
let isSwiping = false;

const onPointerDown = (e: PointerEvent) => {
  pointerStartX = e.clientX;
  pointerStartY = e.clientY;
  isSwiping = true;
};

const onPointerUp = (e: PointerEvent) => {
  if (!isSwiping) return;
  isSwiping = false;

  const dx = e.clientX - pointerStartX;
  const dy = e.clientY - pointerStartY;

  if (Math.abs(dx) < 40 || Math.abs(dy) > Math.abs(dx)) return;

  if (dx < 0) {
    goToSlide((activeIndex.value + 1) % slides.value.length);
  } else {
    goToSlide((activeIndex.value - 1 + slides.value.length) % slides.value.length);
  }
};

onMounted(() => {
  startAutoplay();
});

onBeforeUnmount(() => {
  stopAutoplay();
});

watch(
  () => slides.value.length,
  () => {
    activeIndex.value = 0;
    startAutoplay();
  },
);
</script>

<template>
  <section
    v-if="slides.length"
    class="shortcode-simple-slider-native"
    @pointerdown="onPointerDown"
    @pointerup="onPointerUp"
  >
    <div class="simple-slider-shell">
      <article
        v-for="(slide, index) in slides"
        :key="slide.id"
        class="simple-slider-slide"
        :class="{ 'is-active': index === activeIndex }"
      >
        <div class="simple-slider-media">
          <img
            v-if="slide.image?.src"
            :src="resolveAsset(slide.image.src) || slide.image.src"
            :alt="slide.image.alt || slide.caption || 'Slide image'"
            loading="eager"
          >

          <NuxtLink
            v-if="slide.link?.isInternal"
            :to="slide.link.href"
            class="simple-slider-media__link"
            :aria-label="slide.caption || 'Open slide'"
          />
          <a
            v-else-if="slide.link"
            :href="slide.link.href"
            class="simple-slider-media__link"
            :aria-label="slide.caption || 'Open slide'"
          />
        </div>

        <div class="simple-slider-overlay" />
      </article>

      <div v-if="activeCaption" class="simple-slider-caption">
        <p class="simple-slider-caption__inner">{{ activeCaption }}</p>
      </div>

      <div v-if="slides.length > 1" class="simple-slider-controls">
        <div class="simple-slider-dots">
          <button
            v-for="(slide, index) in slides"
            :key="`${slide.id}-dot`"
            type="button"
            class="simple-slider-dot"
            :class="{ 'is-active': index === activeIndex }"
            :aria-label="`Go to slide ${index + 1}`"
            @click="goToSlide(index)"
          />
        </div>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-simple-slider-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-simple-slider-native {
  position: relative;
  background: #0d0907;
  touch-action: pan-y;
}

.simple-slider-shell {
  position: relative;
  min-height: min(100vh, 1100px);
  overflow: hidden;
}

.simple-slider-slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  pointer-events: none;
  transition:
    opacity 1.1s cubic-bezier(0.4, 0, 0.2, 1),
    transform 1.1s cubic-bezier(0.4, 0, 0.2, 1);
  transform: scale(1.04);
}

.simple-slider-slide.is-active {
  opacity: 1;
  pointer-events: auto;
  transform: scale(1);
}

.simple-slider-media,
.simple-slider-media img,
.simple-slider-media__link,
.simple-slider-overlay {
  position: absolute;
  inset: 0;
}

.simple-slider-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.simple-slider-media__link {
  z-index: 2;
}

.simple-slider-overlay {
  z-index: 1;
  background:
    linear-gradient(0deg, rgba(14, 10, 8, 0.52) 0%, rgba(14, 10, 8, 0.05) 40%, rgba(14, 10, 8, 0.02) 100%),
    radial-gradient(ellipse at 50% 100%, rgba(14, 10, 8, 0.32), transparent 60%);
}

/* ── Caption glass panel ── */
.simple-slider-caption {
  position: absolute;
  left: clamp(1.5rem, 4vw, 4rem);
  right: clamp(1.5rem, 4vw, 4rem);
  bottom: clamp(4.5rem, 10vw, 7rem);
  z-index: 3;
  display: flex;
  justify-content: center;
  pointer-events: none;
}

.simple-slider-caption__inner {
  width: min(100%, 56rem);
  margin: 0;
  padding: clamp(1.15rem, 2.5vw, 1.6rem) clamp(1.25rem, 3vw, 2rem);
  border: 1px solid rgba(248, 243, 234, 0.16);
  border-radius: 1.75rem;
  background: rgba(24, 15, 10, 0.42);
  backdrop-filter: blur(14px);
  box-shadow:
    0 24px 64px rgba(0, 0, 0, 0.32),
    inset 0 1px 0 rgba(255, 248, 237, 0.08);
  color: #fff7ef;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.5rem, 3vw, 2.7rem);
  font-weight: 600;
  line-height: 1.28;
  letter-spacing: 0.015em;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
  text-align: center;
  text-wrap: balance;
}

/* ── Controls ── */
.simple-slider-controls {
  position: absolute;
  left: 50%;
  bottom: 1.5rem;
  z-index: 4;
  display: flex;
  align-items: center;
  transform: translateX(-50%);
}

.simple-slider-dots {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.45rem 0.75rem;
  border-radius: 999px;
  background: rgba(14, 10, 8, 0.38);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(248, 243, 234, 0.1);
}

.simple-slider-dot {
  position: relative;
  width: 0.6rem;
  height: 0.6rem;
  padding: 0;
  border: 0;
  border-radius: 999px;
  background: rgba(255, 248, 237, 0.35);
  cursor: pointer;
  transition:
    transform 0.25s ease,
    background-color 0.25s ease,
    box-shadow 0.25s ease;
}

/* 44px mobile hit area */
.simple-slider-dot::before {
  content: "";
  position: absolute;
  inset: -1rem;
}

.simple-slider-dot.is-active {
  background: #f7efe1;
  transform: scale(1.3);
  box-shadow: 0 0 0 3px rgba(247, 239, 225, 0.18);
}

.simple-slider-dot:hover:not(.is-active) {
  background: rgba(255, 248, 237, 0.6);
  transform: scale(1.1);
}

@media (max-width: 767px) {
  .simple-slider-shell {
    min-height: 82vh;
  }

  .simple-slider-controls {
    bottom: 1rem;
  }

  .simple-slider-caption {
    left: 1rem;
    right: 1rem;
    bottom: 4.5rem;
  }
}
</style>
