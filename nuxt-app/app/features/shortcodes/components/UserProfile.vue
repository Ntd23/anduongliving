<script setup lang="ts">
import { parseUserProfileBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseUserProfileBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
</script>

<template>
  <section v-if="section.metrics.length || section.images.length" class="shortcode-user-profile-native">
    <div class="container user-profile-shell">
      <div v-if="section.metrics.length" class="user-profile-metrics">
        <article v-for="metric in section.metrics" :key="metric.title" class="user-profile-metric">
          <div class="user-profile-metric__head">
            <span>{{ metric.title }}</span>
            <span>{{ metric.percentage }}%</span>
          </div>
          <div class="user-profile-metric__track">
            <div class="user-profile-metric__fill" :style="{ width: `${metric.percentage}%` }" />
          </div>
        </article>
      </div>

      <div v-if="section.images.length" class="user-profile-images">
        <figure v-for="(image, index) in section.images" :key="`${image.src}-${index}`" class="user-profile-images__item">
          <img :src="image.src" :alt="image.alt || `Profile image ${index + 1}`">
        </figure>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-user-profile-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-user-profile-native {
  padding: clamp(4rem, 8vw, 7rem) 0;
  background:
    radial-gradient(circle at bottom right, rgba(185, 130, 90, 0.1), transparent 28%),
    linear-gradient(180deg, #faf5ee, #ede3d5);
}

.user-profile-shell {
  display: grid;
  gap: 2rem;
}

.user-profile-metrics {
  display: grid;
  gap: 0.75rem;
}

.user-profile-metric {
  padding: 1.2rem 1.3rem;
  border-radius: 1.35rem;
  border: 1px solid rgba(111, 117, 83, 0.08);
  background: rgba(255, 252, 246, 0.82);
  backdrop-filter: blur(8px);
  box-shadow: 0 14px 34px rgba(48, 35, 27, 0.06);
}

.user-profile-metric__head {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  color: #2f241d;
  font-weight: 600;
}

.user-profile-metric__track {
  height: 0.5rem;
  margin-top: 0.9rem;
  border-radius: 999px;
  background: rgba(107, 116, 79, 0.1);
  overflow: hidden;
}

.user-profile-metric__fill {
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, #6c744f, #9f845a);
  box-shadow: 0 4px 12px rgba(108, 116, 79, 0.18);
  transition: width 0.6s ease;
}

.user-profile-images {
  display: grid;
  gap: 0.75rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.user-profile-images__item {
  margin: 0;
  overflow: hidden;
  border-radius: 1.6rem;
  box-shadow: 0 18px 44px rgba(48, 35, 27, 0.1);
}

.user-profile-images__item img {
  width: 100%;
  display: block;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.user-profile-images__item:hover img {
  transform: scale(1.03);
}

@media (max-width: 640px) {
  .user-profile-images {
    grid-template-columns: 1fr;
  }
}
</style>




