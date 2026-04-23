<script setup lang="ts">
import { computed } from "vue";
import {
  parseServicesBlock,
  type FeatureAreaSectionData,
  type ShortcodeBlock,
} from "~/features/shortcodes/core";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed<FeatureAreaSectionData>(() => parseServicesBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const sectionStyle = computed(() =>
  section.value.backgroundColor
    ? { background: section.value.backgroundColor }
    : undefined,
);
</script>

<template>
  <section class="shortcode-services-hero" :style="sectionStyle">
    <!-- Full-width background image -->
    <div
      v-if="section.image?.src"
      class="services-hero__backdrop"
      :style="{ backgroundImage: `url(${section.image.src})` }"
    />
    <div class="services-hero__veil" />

    <!-- Glass panel overlay with text -->
    <div
      v-if="section.subtitle || section.title || section.description || section.action"
      class="container services-hero__shell"
    >
      <div class="services-hero__glass">
        <p v-if="section.subtitle" class="services-hero__eyebrow shortcode-narrative-eyebrow">
          {{ section.subtitle }}
        </p>
        <h2 v-if="section.title" class="services-hero__title shortcode-narrative-title">
          {{ section.title }}
        </h2>
        <p v-if="section.description" class="services-hero__description">
          {{ section.description }}
        </p>
        <NuxtLink
          v-if="section.action?.href && section.action?.label"
          :to="section.action.href"
          class="services-hero__action"
        >
          {{ section.action.label }}
        </NuxtLink>
      </div>
    </div>

    <div v-else class="container services-hero__shell">
      <div class="services-hero__fallback" v-html="sanitizedHtml" />
    </div>
  </section>
</template>

<style scoped>
.shortcode-services-hero {
  position: relative;
  overflow: hidden;
  min-height: clamp(30rem, 70vh, 48rem);
  display: flex;
  align-items: flex-end;
}

/* â”€â”€ Full-width background image â”€â”€ */
.services-hero__backdrop {
  position: absolute;
  inset: 0;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  transform: scale(1.02);
}

/* â”€â”€ Gradient veil between image and content â”€â”€ */
.services-hero__veil {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 20% 80%, rgba(185, 130, 90, 0.14), transparent 30%),
    linear-gradient(0deg,
      rgba(21, 16, 11, 0.72) 0%,
      rgba(21, 16, 11, 0.45) 35%,
      rgba(21, 16, 11, 0.12) 70%,
      rgba(21, 16, 11, 0.06) 100%
    );
}

/* â”€â”€ Shell â”€â”€ */
.services-hero__shell {
  position: relative;
  z-index: 1;
  width: 100%;
  padding-top: 6rem;
  padding-bottom: clamp(3rem, 6vw, 5rem);
}

/* â”€â”€ Glass panel â”€â”€ */
.services-hero__glass {
  max-width: min(38rem, 92vw);
  padding: clamp(1.5rem, 3vw, 2.5rem);
  border: 1px solid rgba(248, 243, 234, 0.16);
  border-radius: 1.75rem;
  background: rgba(32, 22, 16, 0.38);
  backdrop-filter: blur(14px);
  box-shadow:
    0 28px 72px rgba(0, 0, 0, 0.28),
    inset 0 1px 0 rgba(255, 248, 237, 0.08);
}

.services-hero__eyebrow {
  margin: 0 0 0.75rem;
  color: #e3c8a8;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.services-hero__title {
  margin: 0;
  color: #fff9f0;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.5rem, 5vw, 4.5rem);
  line-height: 0.95;
  font-weight: 600;
}

.services-hero__description {
  margin: 1.25rem 0 0;
  max-width: 32rem;
  color: rgba(255, 249, 240, 0.8);
  line-height: 1.8;
}

.services-hero__action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3.25rem;
  margin-top: 1.75rem;
  padding: 0 1.4rem;
  border-radius: 999px;
  background: linear-gradient(135deg, var(--retreat-clay, #b9825a), #9f6f49);
  color: #fff9f0;
  text-decoration: none;
  font-size: 0.88rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  box-shadow: 0 16px 36px rgba(185, 130, 90, 0.22);
  transition: box-shadow 0.25s ease;
}

.services-hero__action:hover {
  box-shadow: 0 20px 44px rgba(185, 130, 90, 0.3);
}

/* â”€â”€ Responsive â”€â”€ */
@media (max-width: 767px) {
  .shortcode-services-hero {
    min-height: 28rem;
  }

  .services-hero__veil {
    background:
      radial-gradient(circle at 20% 80%, rgba(185, 130, 90, 0.12), transparent 30%),
      linear-gradient(0deg,
        rgba(21, 16, 11, 0.78) 0%,
        rgba(21, 16, 11, 0.5) 40%,
        rgba(21, 16, 11, 0.18) 100%
      );
  }

  .services-hero__glass {
    max-width: 100%;
    padding: 1.25rem;
  }

  .services-hero__shell {
    padding-top: 4rem;
  }
}
</style>




