<script setup lang="ts">
import { parseTeamBlock } from "~/utils/shortcode-content";
import type { ShortcodeBlock } from "~/composables/usePage";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const socialIcons = {
  facebook: "ph:facebook-logo-fill",
  twitter: "ph:x-logo-fill",
  instagram: "ph:instagram-logo-fill",
  website: "ph:globe-simple-fill",
} as const;

const section = computed(() => parseTeamBlock(props.block.raw));
</script>

<template>
  <section v-if="section.members.length" class="shortcode-team">
    <div class="container">
      <div class="team-grid">
        <article
          v-for="member in section.members"
          :key="`${member.name}-${member.profileUrl || member.image?.src || 'card'}`"
          class="team-card"
        >
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

          <div class="team-card__body">
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
        </article>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-team">
    <div v-html="block.raw" />
  </section>
</template>

<style scoped>
.shortcode-team {
  padding: 5.5rem 0;
}

.team-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(1, minmax(0, 1fr));
}

.team-card {
  overflow: hidden;
  border: 1px solid rgba(15, 23, 42, 0.1);
  border-radius: 1.25rem;
  background: #fff;
  box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease,
    border-color 0.2s ease;
}

.team-card:hover {
  transform: translateY(-4px);
  border-color: rgba(0, 124, 195, 0.22);
  box-shadow: 0 28px 60px rgba(15, 23, 42, 0.12);
}

.team-card__media {
  display: block;
  aspect-ratio: 15 / 17;
  overflow: hidden;
  background:
    radial-gradient(circle at top, rgba(197, 160, 89, 0.28), transparent 55%),
    linear-gradient(180deg, #f8fafc, #eef4f7);
}

.team-card__media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.team-card__body {
  padding: 1.4rem 1.25rem 1.5rem;
  text-align: center;
}

.team-card__name {
  margin: 0;
  font-size: 1.35rem;
  line-height: 1.2;
  color: #0f172a;
}

.team-card__name a,
.team-card__name span {
  color: inherit;
  text-decoration: none;
}

.team-card__name a:hover {
  color: #007cc3;
}

.team-card__title {
  margin: 0.45rem 0 0;
  color: #007cc3;
  font-weight: 600;
}

.team-card__socials {
  display: flex;
  justify-content: center;
  gap: 0.6rem;
  margin-top: 1rem;
}

.team-card__socials a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 999px;
  background: #007cc3;
  color: #fff;
  text-decoration: none;
  transition:
    transform 0.2s ease,
    background-color 0.2s ease;
}

.team-card__socials a:hover {
  transform: translateY(-1px);
  background: #005a8f;
}

@media (min-width: 640px) {
  .team-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (min-width: 1100px) {
  .team-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
}
</style>
