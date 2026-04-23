<script setup lang="ts">
import { parseOnsenSpaGalleryBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseOnsenSpaGalleryBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();

const actions = computed(() =>
  section.value.actions
    .map((action) => {
      const link = resolveLink(action.href);

      if (!link) {
        return null;
      }

      return {
        ...action,
        link,
      };
    })
    .filter(Boolean),
);

const sectionStyle = computed(() =>
  section.value.backgroundImage?.src
    ? { backgroundImage: `url('${section.value.backgroundImage.src}')` }
    : undefined,
);
</script>

<template>
  <section
    v-if="section.items.length || section.subtitle || actions.length"
    class="shortcode-onsen-gallery-native"
    :style="sectionStyle"
  >
    <div class="onsen-gallery-overlay" />

    <div class="onsen-gallery-shell">
      <div v-if="section.backgroundTitle" class="onsen-gallery-backtitle">
        {{ section.backgroundTitle }}
      </div>

      <p v-if="section.subtitle" class="onsen-gallery-subtitle">
        {{ section.subtitle }}
      </p>

      <div v-if="section.items.length" class="onsen-gallery-grid">
        <article
          v-for="(item, index) in section.items"
          :key="`${item.title || 'item'}-${index}`"
          class="onsen-gallery-item"
        >
          <p v-if="item.title" class="onsen-gallery-item__title">
            {{ item.title }}
          </p>
          <img
            v-if="item.image?.src"
            :src="item.image.src"
            :alt="item.image.alt || item.title || 'Gallery image'"
            loading="lazy"
          >
        </article>
      </div>

      <div v-if="section.sectionLabel || actions.length" class="onsen-gallery-footer">
        <div v-if="section.sectionLabel" class="onsen-gallery-footer__label">
          {{ section.sectionLabel }}
        </div>

        <div v-if="actions.length" class="onsen-gallery-footer__actions">
          <template v-for="action in actions" :key="`${action.label}-${action.link.href}`">
            <NuxtLink
              v-if="action.link.isInternal"
              :to="action.link.href"
              class="onsen-gallery-footer__button"
            >
              {{ action.label }}
            </NuxtLink>
            <a
              v-else
              :href="action.link.href"
              class="onsen-gallery-footer__button"
            >
              {{ action.label }}
            </a>
          </template>
        </div>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-onsen-gallery-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-onsen-gallery-native {
  position: relative;
  overflow: hidden;
  padding: 2.25rem 1rem 3.2rem;
  background-color: #080605;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}

.onsen-gallery-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(8, 6, 5, 0.5), rgba(8, 6, 5, 0.74));
}

.onsen-gallery-shell {
  position: relative;
  z-index: 1;
  width: min(100%, 65rem);
  margin: 0 auto;
  text-align: center;
}

.onsen-gallery-backtitle {
  color: rgba(255, 249, 239, 0.12);
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(3rem, 9vw, 7rem);
  font-style: italic;
  line-height: 0.9;
}

.onsen-gallery-subtitle {
  margin: 1.25rem 0 1.75rem;
  color: rgba(252, 246, 235, 0.92);
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.5rem, 3vw, 2.6rem);
  line-height: 1.04;
}

.onsen-gallery-grid {
  display: grid;
  gap: 1rem 0.85rem;
  grid-template-columns: repeat(4, minmax(0, 1fr));
}

.onsen-gallery-item {
  text-align: center;
}

.onsen-gallery-item:nth-child(5) {
  grid-column: 2 / 3;
}

.onsen-gallery-item:nth-child(6) {
  grid-column: 3 / 4;
}

.onsen-gallery-item__title {
  min-height: 1.4rem;
  margin: 0 0 0.65rem;
  color: #fff7ea;
  font-size: 0.82rem;
  line-height: 1.5;
}

.onsen-gallery-item img {
  display: block;
  width: 100%;
  aspect-ratio: 1.22;
  object-fit: cover;
  border-radius: 1.15rem;
  box-shadow: 0 18px 36px rgba(0, 0, 0, 0.22);
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.onsen-gallery-item:hover img {
  transform: scale(1.03);
}

.onsen-gallery-footer {
  margin-top: 1.8rem;
}

.onsen-gallery-footer__label {
  margin-bottom: 0.8rem;
  color: #bba067;
  font-size: 0.78rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.onsen-gallery-footer__actions {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.onsen-gallery-footer__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 11rem;
  padding: 0.85rem 1.1rem;
  border-radius: 999px;
  background: linear-gradient(135deg, #b19143, #9a7c38);
  color: #fff8ee;
  text-decoration: none;
  font-weight: 600;
  box-shadow: 0 12px 28px rgba(177, 145, 67, 0.22);
  transition: box-shadow 0.2s ease;
}

.onsen-gallery-footer__button:hover {
  box-shadow: 0 16px 36px rgba(177, 145, 67, 0.3);
}

@media (max-width: 991px) {
  .onsen-gallery-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .onsen-gallery-item:nth-child(5),
  .onsen-gallery-item:nth-child(6) {
    grid-column: auto;
  }
}

@media (max-width: 575px) {
  .onsen-gallery-grid {
    grid-template-columns: 1fr;
  }
}
</style>




