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
        <div class="team-header__content">
          <h2 v-if="section.title" class="team-title animate-on-scroll" style="--delay: 200ms">
            {{ section.title }}
          </h2>
          <div class="team-header__text">
            <p v-if="section.subtitle" class="team-subtitle animate-on-scroll" style="--delay: 300ms">
              {{ section.subtitle }}
            </p>
            <p v-if="section.description" class="team-description animate-on-scroll" style="--delay: 400ms">
              {{ section.description }}
            </p>
          </div>
        </div>
        <div class="team-header__decoration animate-on-scroll" style="--delay: 500ms">
          <div class="decoration-line"></div>
          <div class="decoration-dot"></div>
        </div>
      </header>
      
      <div class="team-grid animate-on-scroll" style="--delay: 600ms">
        <article 
          v-for="(member, index) in section.members" 
          :key="`${member.name}-${member.profileUrl || member.image?.src || 'card'}`"
          class="team-card animate-on-scroll"
          :style="{ '--delay': `${index * 100 + 700}ms` }"
        >
          <div class="team-card__media-wrap">
            <div class="team-card__image-container">
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
                  class="team-card__image"
                >
              </component>
              <div class="team-card__media-overlay" />
              <div class="team-card__media-shine" />
            </div>
          </div>

          <div class="team-card__body">
            <div class="team-card__glass">
              <div class="team-card__info">
                <h3 class="team-card__name">
                  <component
                    :is="member.profileUrl ? 'a' : 'span'"
                    :href="member.profileUrl || undefined"
                    class="team-card__name-link"
                  >
                    {{ member.name }}
                  </component>
                </h3>

                <p v-if="member.title" class="team-card__title">
                  {{ member.title }}
                </p>
              </div>

              <div v-if="member.socials.length" class="team-card__socials">
                <a
                  v-for="social in member.socials"
                  :key="`${member.name}-${social.platform}-${social.url}`"
                  :href="social.url"
                  target="_blank"
                  rel="noreferrer"
                  :aria-label="`${member.name} on ${social.platform}`"
                  class="team-card__social-link"
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
  padding: clamp(5rem, 10vw, 8rem) 0;
  background:
    radial-gradient(circle at top left, rgba(185, 130, 90, 0.15), transparent 35%),
    radial-gradient(circle at bottom right, rgba(108, 116, 79, 0.08), transparent 40%),
    linear-gradient(180deg, rgba(255, 252, 246, 0.95), rgba(243, 236, 223, 0.8));
  position: relative;
  overflow: hidden;
}

.team-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(ellipse at center top, rgba(227, 200, 168, 0.03) 0%, transparent 50%),
    radial-gradient(ellipse at center bottom, rgba(108, 116, 79, 0.02) 0%, transparent 50%);
  pointer-events: none;
}

.team-veil {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    linear-gradient(135deg, rgba(26, 18, 12, 0.02) 0%, transparent 25%),
    linear-gradient(225deg, rgba(108, 116, 79, 0.01) 0%, transparent 25%);
  pointer-events: none;
}

.team-shell {
  position: relative;
  z-index: 1;
  padding-top: clamp(2rem, 4vw, 3rem);
  padding-bottom: clamp(2rem, 4vw, 3rem);
}

/* ====== HEADER SECTION ====== */
.team-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: clamp(3rem, 6vw, 5rem);
  gap: 2rem;
}

.team-header__content {
  flex: 1;
}

.team-title {
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 300;
  line-height: 1.1;
  color: #1a120c;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  margin: 0 0 1.5rem 0;
  letter-spacing: -0.02em;
}

.team-header__text {
  max-width: 600px;
}

.team-subtitle {
  font-size: clamp(1.1rem, 2vw, 1.3rem);
  font-weight: 400;
  color: #6c744f;
  margin: 0 0 1rem 0;
  line-height: 1.4;
}

.team-description {
  font-size: clamp(0.95rem, 1.5vw, 1.1rem);
  color: rgba(26, 18, 12, 0.7);
  line-height: 1.6;
  margin: 0;
}

.team-header__decoration {
  position: relative;
  width: 120px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.decoration-line {
  position: absolute;
  width: 100%;
  height: 1px;
  background: linear-gradient(90deg, transparent, #e3c8a8, transparent);
  opacity: 0.3;
}

.decoration-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #e3c8a8;
  position: relative;
  z-index: 1;
  box-shadow: 0 0 20px rgba(227, 200, 168, 0.3);
}

/* ====== TEAM GRID ====== */
.team-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(1, minmax(0, 1fr));
}

/* ====== TEAM CARD ====== */
.team-card {
  position: relative;
  overflow: hidden;
  border-radius: 1.2rem;
  background: #1a120c;
  box-shadow: 
    0 10px 30px rgba(26, 18, 12, 0.1),
    0 1px 8px rgba(26, 18, 12, 0.06);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  transform: translateY(0);
}

.team-card:hover {
  transform: translateY(-8px);
  box-shadow: 
    0 20px 40px rgba(26, 18, 12, 0.15),
    0 2px 16px rgba(26, 18, 12, 0.1);
}

.team-card__media-wrap {
  position: relative;
  aspect-ratio: 4/5;
  overflow: hidden;
}

.team-card__image-container {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.team-card__media {
  display: block;
  width: 100%;
  height: 100%;
  text-decoration: none;
}

.team-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.team-card:hover .team-card__image {
  transform: scale(1.05);
}

.team-card__media-overlay {
  position: absolute;
  inset: 0;
  background: 
    linear-gradient(0deg, rgba(26, 18, 12, 0.8) 0%, 
    rgba(26, 18, 12, 0.4) 40%, 
    rgba(26, 18, 12, 0.1) 70%, 
    transparent 100%);
  pointer-events: none;
  z-index: 1;
}

.team-card__media-shine {
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(
    45deg,
    transparent 30%,
    rgba(255, 255, 255, 0.1) 50%,
    transparent 70%
  );
  transform: rotate(45deg) translateX(-100%);
  transition: transform 0.6s ease;
  pointer-events: none;
  z-index: 2;
}

.team-card:hover .team-card__media-shine {
  transform: rotate(45deg) translateX(100%);
}

.team-card__body {
  position: absolute;
  left: 1rem;
  right: 1rem;
  bottom: 1rem;
  z-index: 3;
}

.team-card__glass {
  padding: 1.2rem 1.5rem;
  border: 1px solid rgba(248, 243, 234, 0.15);
  border-radius: 1rem;
  background: rgba(32, 22, 16, 0.4);
  backdrop-filter: blur(16px) saturate(180%);
  box-shadow: 
    inset 0 1px 0 rgba(255, 248, 237, 0.1),
    0 8px 32px rgba(26, 18, 12, 0.2);
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.team-card.animate-in .team-card__glass {
  opacity: 1;
  transform: translateY(0);
}

.team-card__info {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.team-card__name {
  margin: 0;
  font-size: clamp(1.2rem, 2.5vw, 1.5rem);
  font-weight: 400;
  line-height: 1.2;
  color: #fff7ef;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  letter-spacing: -0.01em;
}

.team-card__name-link {
  color: inherit;
  text-decoration: none;
  transition: color 0.3s ease;
}

.team-card__name-link:hover {
  color: #e3c8a8;
}

.team-card__title {
  margin: 0;
  color: #e3c8a8;
  font-size: clamp(0.85rem, 1.5vw, 0.95rem);
  font-weight: 500;
  line-height: 1.4;
  opacity: 0.9;
}

.team-card__socials {
  display: flex;
  justify-content: center;
  gap: 0.6rem;
  margin-top: 0.5rem;
}

.team-card__social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.4rem;
  height: 2.4rem;
  border-radius: 50%;
  background: rgba(108, 116, 79, 0.6);
  color: #fffdf9;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid rgba(255, 255, 255, 0.1);
  font-size: 1rem;
}

.team-card__social-link:hover {
  background: rgba(108, 116, 79, 0.8);
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 4px 12px rgba(108, 116, 79, 0.3);
}

.team-card__social-link svg {
  width: 1rem;
  height: 1rem;
}

/* ====== ANIMATION STATES ====== */
.team-card.animate-in .team-card__name {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 100ms);
}

.team-card.animate-in .team-card__title {
  opacity: 0.9;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 200ms);
}

.team-card.animate-in .team-card__socials {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: calc(var(--delay, 0ms) + 300ms);
}

/* ====== RESPONSIVE DESIGN ====== */
@media (min-width: 640px) {
  .team-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  
  .team-header {
    flex-direction: row;
  }
}

@media (min-width: 768px) {
  .team-grid {
    gap: 2rem;
  }
  
  .team-card__body {
    left: 1.5rem;
    right: 1.5rem;
    bottom: 1.5rem;
  }
}

@media (min-width: 1024px) {
  .team-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (min-width: 1280px) {
  .team-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
  
  .team-header__decoration {
    width: 160px;
  }
}

@media (max-width: 639px) {
  .team-header {
    flex-direction: column;
    text-align: center;
    gap: 1.5rem;
  }
  
  .team-header__decoration {
    width: 80px;
    height: 40px;
  }
  
  .team-card__body {
    left: 0.8rem;
    right: 0.8rem;
    bottom: 0.8rem;
  }
  
  .team-card__glass {
    padding: 1rem 1.2rem;
  }
}
</style>
