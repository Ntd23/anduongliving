<script setup lang="ts">
import { parseLogoShowcaseBannerBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseLogoShowcaseBannerBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);

const sectionStyle = computed(() =>
  section.value.backgroundImage?.src
    ? { backgroundImage: `url('${section.value.backgroundImage.src}')` }
    : undefined,
);
</script>

<template>
  <section
    v-if="section.logoImage?.src || section.topLines.length || section.bottomLines.length"
    class="shortcode-logo-showcase-native"
    :style="sectionStyle"
  >
    <div class="logo-showcase-overlay" />

    <div class="logo-showcase-shell">
      <div v-if="section.topLines.length" class="logo-showcase-copy is-top">
        <p v-for="line in section.topLines" :key="line">
          {{ line }}
        </p>
      </div>

      <div class="logo-showcase-center">
        <img
          v-if="section.logoImage?.src"
          :src="section.logoImage.src"
          :alt="section.logoImage.alt || 'Showcase logo'"
          class="logo-showcase-mark"
          loading="eager"
        >
      </div>

      <div v-if="section.bottomLines.length" class="logo-showcase-copy is-bottom">
        <p v-for="line in section.bottomLines" :key="line">
          {{ line }}
        </p>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-logo-showcase-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-logo-showcase-native {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: min(100vh, 1280px);
  padding: 4rem 1.5rem 3rem;
  background-color: #090605;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  overflow: hidden;
}

.logo-showcase-overlay {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(180deg, rgba(9, 6, 5, 0.44), rgba(9, 6, 5, 0.62)),
    radial-gradient(circle at center, rgba(255, 255, 255, 0.06), transparent 44%);
}

.logo-showcase-shell {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: min(88vh, 1120px);
  width: min(100%, 74rem);
  text-align: center;
  color: #fff;
}

.logo-showcase-copy {
  width: min(100%, 62rem);
}

.logo-showcase-copy.is-top {
  margin-bottom: auto;
}

.logo-showcase-copy.is-bottom {
  margin-top: auto;
}

.logo-showcase-copy p {
  margin: 0;
  color: rgba(251, 245, 235, 0.9);
  font-size: clamp(0.82rem, 1vw, 1rem);
  line-height: 2;
  letter-spacing: 0.08em;
  text-wrap: balance;
}

.logo-showcase-copy p + p {
  margin-top: 0.45rem;
}

.logo-showcase-center {
  flex: 1 1 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}

.logo-showcase-mark {
  width: min(100%, 11rem);
  filter: drop-shadow(0 22px 40px rgba(0, 0, 0, 0.42));
}

@media (max-width: 767px) {
  .shortcode-logo-showcase-native {
    min-height: 90vh;
    padding: 3rem 1rem 2.5rem;
  }

  .logo-showcase-shell {
    min-height: 78vh;
  }

  .logo-showcase-mark {
    width: min(100%, 8.5rem);
  }
}
</style>
