<script setup lang="ts">
import { parseWhyChooseUsBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseWhyChooseUsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const sectionStyle = computed(() =>
  section.value.backgroundColor ? { background: section.value.backgroundColor } : undefined,
);
</script>

<template>
  <section
    v-if="section.title || section.subtitle || section.items.length || section.image?.src"
    id="skill"
    class="shortcode-skill"
    :style="sectionStyle"
  >
    <div v-if="section.backgroundImage?.src" class="skill-decor">
      <img
        :src="section.backgroundImage.src"
        :alt="section.backgroundImage.alt || section.title || 'Skill decoration'"
        loading="lazy"
      >
    </div>

    <div class="container">
      <div class="skill-layout">
        <div class="skill-copy">
          <div v-if="section.subtitle || section.title" class="skill-header">
            <p v-if="section.subtitle" class="skill-eyebrow">
              {{ section.subtitle }}
            </p>
            <h2 v-if="section.title" class="skill-title">
              {{ section.title }}
            </h2>
          </div>

          <p v-if="section.description" class="skill-description">
            {{ section.description }}
          </p>

          <div v-if="section.items.length" class="skill-bars">
            <article
              v-for="item in section.items"
              :key="`${item.title}-${item.percentage}`"
              class="skill-bar"
            >
              <div class="skill-bar__meta">
                <span>{{ item.title }}</span>
                <strong>{{ item.percentage }}%</strong>
              </div>
              <div class="skill-bar__track">
                <span class="skill-bar__fill" :style="{ width: `${item.percentage}%` }" />
              </div>
            </article>
          </div>
        </div>

        <div v-if="section.image?.src" class="skill-media">
          <img
            :src="section.image.src"
            :alt="section.image.alt || section.title || 'Skill image'"
            loading="lazy"
          >
        </div>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-skill">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-skill {
  position: relative;
  overflow: hidden;
  padding: 5.5rem 0;
  color: #f8f3ea;
}

.skill-decor {
  position: absolute;
  top: 1rem;
  left: clamp(-2rem, -1vw, -0.5rem);
  opacity: 0.32;
  pointer-events: none;
}

.skill-layout {
  position: relative;
  display: grid;
  gap: 2.5rem;
  align-items: center;
}

.skill-header {
  max-width: 44rem;
}

.skill-eyebrow {
  margin: 0 0 0.75rem;
  color: #dcc3a0;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.skill-title {
  margin: 0;
  font-size: clamp(2rem, 5vw, 4.25rem);
  line-height: 1.02;
  color: #f8f3ea;
}

.skill-description {
  max-width: 42rem;
  margin: 1.25rem 0 0;
  color: rgba(248, 243, 234, 0.76);
  font-size: 1.02rem;
}

.skill-bars {
  display: grid;
  gap: 1.25rem;
  margin-top: 2rem;
}

.skill-bar__meta {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 0.6rem;
  font-weight: 700;
}

.skill-bar__track {
  overflow: hidden;
  height: 0.95rem;
  border-radius: 999px;
  background: rgba(248, 243, 234, 0.16);
}

.skill-bar__fill {
  display: block;
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, #b9825a, #dfc09b);
  box-shadow: 0 8px 24px rgba(185, 130, 90, 0.28);
  transition: width 0.6s ease;
}

.skill-media {
  justify-self: end;
  width: min(100%, 38rem);
  padding: 1rem;
  border: 1px solid rgba(248, 243, 234, 0.12);
  border-radius: 1.75rem;
  background: rgba(248, 243, 234, 0.05);
  box-shadow: 0 30px 80px rgba(20, 10, 6, 0.24);
}

.skill-media img {
  display: block;
  width: 100%;
  border-radius: 1.15rem;
  object-fit: cover;
}

@media (min-width: 1024px) {
  .skill-layout {
    grid-template-columns: minmax(0, 1.05fr) minmax(20rem, 0.95fr);
  }
}
</style>
