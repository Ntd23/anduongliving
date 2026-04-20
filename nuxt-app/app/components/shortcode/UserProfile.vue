<script setup lang="ts">
import { parseUserProfileBlock, type ShortcodeBlock } from "~/utils/shortcode";
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
  background: linear-gradient(180deg, #faf5ee, #ede3d5);
}
.user-profile-shell {
  display: grid;
  gap: 2rem;
}
.user-profile-metrics {
  display: grid;
  gap: 1rem;
}
.user-profile-metric {
  padding: 1.2rem 1.3rem;
  border-radius: 1.35rem;
  background: rgba(255, 252, 246, 0.9);
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
  background: rgba(107, 116, 79, 0.12);
  overflow: hidden;
}
.user-profile-metric__fill {
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, #6c744f, #9f845a);
}
.user-profile-images {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}
.user-profile-images__item {
  margin: 0;
  overflow: hidden;
  border-radius: 1.6rem;
  box-shadow: 0 18px 44px rgba(48, 35, 27, 0.08);
}
.user-profile-images__item img {
  width: 100%;
  display: block;
  object-fit: cover;
}
@media (max-width: 640px) {
  .user-profile-images {
    grid-template-columns: 1fr;
  }
}
</style>
