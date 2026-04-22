<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted } from "vue";
import { parseTeamsBlock, type ShortcodeBlock } from "~/utils/shortcode";
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
          <div class="team-card__image-container animate-on-scroll" :style="{ '--delay': `${index * 150 + 600}ms` }">
            <img
              v-if="member.image?.src"
              :src="member.image.src"
              :alt="member.image.alt || member.name"
              class="team-card__image"
              loading="lazy"
            >
          </div>

          <div class="team-card__info animate-on-scroll" :style="{ '--delay': `${index * 150 + 700}ms` }">
            <h3 class="team-card__name">
              {{ member.name }}
            </h3>
            <p v-if="member.title" class="team-card__title">
              {{ member.title }}
            </p>
            
            <div v-if="member.socials.length" class="team-card__socials">
              <a
                v-for="(social, socialIndex) in member.socials"
                :key="`${member.name}-${social.platform}-${social.url}`"
                :href="social.url"
                target="_blank"
                rel="noreferrer"
                :aria-label="`${member.name} on ${social.platform}`"
                class="team-card__social-link animate-on-scroll"
                :style="{ '--delay': `${index * 150 + 800 + socialIndex * 100}ms` }"
              >
                <Icon :name="socialIcons[social.platform]" />
              </a>
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
.shortcode-team-native {
  position: relative;
  padding: clamp(4rem, 8vw, 7rem) 0;
  min-height: 100vh;
  display: flex;
  align-items: center;
}

.team-backdrop {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #2d2018 0%, #1a120f 100%);
  z-index: 1;
}

.team-veil {
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at top, rgba(138, 110, 72, 0.12) 0%, transparent 50%);
  z-index: 2;
}

.team-shell {
  position: relative;
  z-index: 3;
  max-width: min(80rem, 92vw);
  margin: 0 auto;
}

.team-header {
  text-align: center;
  margin-bottom: 3rem;
}

.team-title {
  margin: 0 0 1.5rem 0;
  color: #ffffff;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.8rem, 5vw, 4.5rem);
  font-weight: 600;
  line-height: 0.95;
  opacity: 0;
  transform: translateY(30px);
}

.team-title.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.team-description {
  margin: 0 auto;
  max-width: 48rem;
  color: rgba(255, 249, 240, 0.78);
  font-size: 1.1rem;
  line-height: 1.6;
  opacity: 0;
  transform: translateY(20px);
}

.team-description.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.team-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  opacity: 0;
  transform: translateY(40px);
}

.team-grid.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.team-card {
  position: relative;
  overflow: hidden;
  border-radius: 1.5rem;
  background: rgba(211, 211, 211, 0.1);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 1.5rem;
  overflow: hidden;
  transition: all 0.6s ease;
  opacity: 0;
  transform: translateY(40px);
  display: flex;
  flex-direction: column;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.team-card.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.team-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 25px 60px rgba(48, 35, 27, 0.15);
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
  transition: transform 0.6s ease;
}

.team-card:hover .team-card__image {
  transform: scale(1.08);
}

.team-card__info {
  padding: 2rem 1.5rem;
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
  margin: 0 0 0.75rem 0;
  color: #ffffff;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.4rem;
  font-weight: 600;
  line-height: 1.2;
  opacity: 0;
  transform: translateY(15px);
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

.team-card.animate-in .team-card__title {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 700ms);
}

.team-card__content {
  opacity: 0;
  transform: translateY(20px);
}

.team-card__content.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.team-card__additional-info {
  position: relative;
  margin-top: 1rem;
}

.team-card__reveal-btn {
  position: absolute;
  top: -1.5rem;
  right: 1rem;
  width: 2.5rem;
  height: 2.5rem;
  border: none;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 2;
}

.team-card__reveal-btn:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: scale(1.05);
}

.team-card__expanded-content {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.4s ease-out;
}

.team-card__expanded-content.expanded {
  max-height: 300px;
}

.team-card__socials {
  display: flex;
  justify-content: center;
  gap: 0.75rem;
  opacity: 0;
  transform: translateY(15px);
}

.team-card.animate-in .team-card__socials {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 800ms);
}

.team-card__social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.75rem;
  height: 2.75rem;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: #ffffff;
  text-decoration: none;
  transition: all 0.3s ease;
  opacity: 0;
  transform: translateY(10px) scale(0.8);
}

.team-card__social-link.animate-in {
  opacity: 1;
  transform: translateY(0) scale(1);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.team-card__social-link:hover {
  background: rgba(255, 255, 255, 0.25);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-2px) scale(1.05);
}

.team-card:hover .team-card__image {
  transform: scale(1.08);
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
  .team-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
  }
}

@media (max-width: 768px) {
  .shortcode-team-native {
    padding: clamp(3rem, 6vw, 5rem) 0;
    min-height: auto;
  }
  
  .team-shell {
    max-width: min(60rem, 92vw);
  }
  .team-card {
    border-radius: 1.25rem;
  }
  
  .team-card__overlay {
    padding: 1.5rem;
  }
  
  .team-card__name {
    font-size: 1.25rem;
  }
  
  .team-card__title {
    font-size: 0.9rem;
  }
  
  .team-card__socials {
    gap: 0.5rem;
  }
  
  .team-card__social-link {
    width: 2.25rem;
    height: 2.25rem;
  }
}

@media (max-width: 480px) {
  .team-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .team-card__overlay {
    padding: 1rem;
  }
  
  .team-card__name {
    font-size: 1.1rem;
  }
  
  .team-card__title {
    font-size: 0.85rem;
  }
  
  .team-card__social-link {
    width: 2rem;
    height: 2rem;
  }
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
