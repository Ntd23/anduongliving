<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted } from "vue";
import { parseTeamsBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { useScrollAnimation } from "~/composables/useScrollAnimation";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const socialIcons = {
  facebook: "ph:facebook-logo-fill",
  twitter: "ph:x-logo-fill",
  instagram: "ph:instagram-logo-fill",
  website: "ph:globe-simple-fill",
} as const;

const section = computed(() => parseTeamsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);

const { setupScrollAnimations } = useScrollAnimation()

const toggleExpanded = (event: MouseEvent) => {
  const target = event.currentTarget as HTMLElement
  const expandedContent = target.closest('.team-card__content')?.querySelector('.team-card__expanded-content')
  if (expandedContent) {
    expandedContent.classList.toggle('expanded')
    target.classList.toggle('expanded')
  }
}

onMounted(async () => {
  await nextTick()
  setupScrollAnimations()
})

onUnmounted(() => {
  // Cleanup handled by composable
})
</script>

<template>
  <section v-if="section.members.length" class="shortcode-team-native">
    <div class="team-backdrop" />
    <div class="team-veil" />
    
    <div class="container team-shell animate-on-scroll">
      <header class="team-header animate-on-scroll" style="--delay: 100ms">
        <h2 v-if="section.title" class="team-title animate-on-scroll" style="--delay: 200ms">
          {{ section.title }}
        </h2>
        <p v-if="section.subtitle" class="team-description animate-on-scroll" style="--delay: 300ms">
          {{ section.subtitle }}
        </p>
        <p v-if="section.description" class="team-description animate-on-scroll" style="--delay: 400ms">
          {{ section.description }}
        </p>
      </header>
      
      <div class="team-grid animate-on-scroll" style="--delay: 400ms">
        <article 
          v-for="(member, index) in section.members" 
          :key="`${member.name}-${member.profileUrl || member.image?.src || 'card'}`"
          class="team-card animate-on-scroll"
          :style="{ '--delay': `${index * 150 + 500}ms` }"
        >
          <div class="team-card__media-wrap">
            <component
              :is="member.profileUrl ? 'a' : 'div'"
              class="team-card__media"
              :href="member.profileUrl || undefined"
            >
              <img
                v-if="member.image?.src"
                :src="member.image.src"
                :alt="member.image.alt || member.name"
                loading="lazy"
              >
            </component>
            <div class="team-card__media-overlay" />
          </div>

          <div class="team-card__body">
            <div class="team-card__glass">
              <h3 class="team-card__name">
                <component
                  :is="member.profileUrl ? 'a' : 'span'"
                  :href="member.profileUrl || undefined"
                >
                  {{ member.name }}
                </component>
              </h3>

              <p v-if="member.title" class="team-card__title">
                {{ member.title }}
              </p>

              <div v-if="member.socials.length" class="team-card__socials">
                <a
                  v-for="social in member.socials"
                  :key="`${member.name}-${social.platform}-${social.url}`"
                  :href="social.url"
                  target="_blank"
                  rel="noreferrer"
                  :aria-label="`${member.name} on ${social.platform}`"
                >
                  <Icon :name="socialIcons[social.platform]" />
                </a>
              </div>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-team">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-team {
  padding: clamp(4.5rem, 8vw, 6rem) 0;
  background:
    radial-gradient(circle at top left, rgba(185, 130, 90, 0.1), transparent 28%),
    linear-gradient(180deg, rgba(255, 252, 246, 0.9), rgba(243, 236, 223, 0.72));
}

.team-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(1, minmax(0, 1fr));
}

.team-card {
  position: relative;
  overflow: hidden;
  border-radius: 1.6rem;
  background: #1a120c;
  box-shadow: 0 20px 50px rgba(47, 36, 29, 0.12);
  transition: box-shadow 0.3s ease;
}

.team-card:hover {
  box-shadow: 0 24px 56px rgba(47, 36, 29, 0.18);
}

.team-card__media-wrap {
  position: relative;
}

.team-card__image-container {
  position: relative;
  aspect-ratio: 1 / 1;
  overflow: hidden;
}

.team-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.team-card:hover .team-card__media img {
  transform: scale(1.03);
}

.team-card__media-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(0deg, rgba(14, 8, 4, 0.82) 0%, rgba(14, 8, 4, 0.2) 45%, transparent 100%);
  pointer-events: none;
}

.team-card__body {
  position: absolute;
  left: 0.6rem;
  right: 0.6rem;
  bottom: 0.6rem;
  z-index: 1;
}

.team-card__glass {
  padding: 0.9rem 1.1rem;
  border: 1px solid rgba(248, 243, 234, 0.1);
  border-radius: 1.15rem;
  background: rgba(32, 22, 16, 0.36);
  backdrop-filter: blur(12px);
  box-shadow: inset 0 1px 0 rgba(255, 248, 237, 0.06);
  text-align: center;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  opacity: 0;
  transform: translateY(20px);
}

.team-card.animate-in .team-card__info {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 400ms);
}

.team-card__name {
  margin: 0;
  font-size: 1.3rem;
  line-height: 1.2;
  color: #fff7ef;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
}

.team-card.animate-in .team-card__name {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 600ms);
}

.team-card__title {
  margin: 0 0 1.5rem 0;
  color: rgba(255, 249, 240, 0.9);
  font-size: 1.1rem;
  font-weight: 400;
  line-height: 1.4;
  opacity: 0;
  transform: translateY(15px);
}

.team-card__title {
  margin: 0.3rem 0 0;
  color: #e3c8a8;
  font-weight: 600;
  font-size: 0.82rem;
}

.team-card__socials {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 0.65rem;
}

.team-card__social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.2rem;
  height: 2.2rem;
  border-radius: 999px;
  background: rgba(108, 116, 79, 0.7);
  color: #fffdf9;
  text-decoration: none;
  transition: background-color 0.2s ease;
}

.team-card__socials a:hover {
  background: rgba(108, 116, 79, 0.9);
}

@media (min-width: 640px) {
  .team-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (min-width: 1100px) {
  .team-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
</style>




