<script setup lang="ts">
import { parseBrandsBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { useResolvedCmsAsset } from "~/composables/useResolvedCmsAsset";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseBrandsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const resolveAsset = useResolvedCmsAsset();
const sectionStyle = computed(() => {
  const style: Record<string, string> = {};

  if (section.value.backgroundColor) {
    style.backgroundColor = section.value.backgroundColor;
  }

  if (section.value.backgroundImage?.src) {
    const backgroundSrc = resolveAsset(section.value.backgroundImage.src) || section.value.backgroundImage.src;

    style.backgroundImage = [
      "linear-gradient(135deg, rgba(28, 22, 17, 0.2), rgba(250, 245, 238, 0.84) 46%, rgba(28, 22, 17, 0.16))",
      `url('${backgroundSrc}')`,
    ].join(", ");
  }

  return Object.keys(style).length ? style : undefined;
});
</script>

<template>
  <section v-if="section.items.length" class="shortcode-brands-native" :style="sectionStyle">
    <div class="brands-atmosphere" aria-hidden="true" />
    <div class="container brands-shell">
      <component
        :is="item.href ? (resolveLink(item.href)?.isInternal ? 'NuxtLink' : 'a') : 'div'"
        v-for="item in section.items"
        :key="item.image?.src"
        v-bind="
          item.href
            ? resolveLink(item.href)?.isInternal
              ? { to: resolveLink(item.href)!.href }
              : { href: item.href }
            : {}
        "
        class="brands-item"
      >
        <img
          :src="resolveAsset(item.image!.src) || item.image!.src"
          :alt="item.image!.alt || item.name || 'Brand logo'"
          class="brands-item__image"
        >
      </component>
    </div>
  </section>

  <section v-else class="shortcode-brands-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-brands-native {
  position: relative;
  isolation: isolate;
  overflow: hidden;
  padding: clamp(4.5rem, 7vw, 7rem) 0;
  background:
    radial-gradient(circle at 18% 20%, rgba(185, 130, 90, 0.18), transparent 34%),
    radial-gradient(circle at 84% 70%, rgba(111, 117, 83, 0.16), transparent 30%),
    linear-gradient(180deg, #faf5ee, #f2e9de);
  background-position: center;
  background-size: cover;
}

.shortcode-brands-native::before {
  position: absolute;
  z-index: -2;
  inset: 0;
  background: linear-gradient(90deg, rgba(250, 245, 238, 0.84), rgba(250, 245, 238, 0.52), rgba(250, 245, 238, 0.88));
  content: "";
}

.brands-atmosphere {
  position: absolute;
  z-index: -1;
  inset: clamp(1rem, 3vw, 2.5rem);
  border: 1px solid rgba(255, 255, 255, 0.5);
  border-radius: clamp(2rem, 5vw, 4rem);
  background: rgba(255, 252, 246, 0.18);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.48), 0 30px 80px rgba(47, 36, 29, 0.08);
  pointer-events: none;
}

.brands-shell {
  display: grid;
  position: relative;
  gap: clamp(1rem, 2vw, 1.4rem);
  grid-template-columns: repeat(4, minmax(0, 1fr));
  align-items: center;
}

.brands-item {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: clamp(11rem, 17vw, 16rem);
  padding: clamp(1rem, 1.8vw, 1.75rem);
  border: 1px solid rgba(255, 255, 255, 0.58);
  border-radius: clamp(1.5rem, 2.8vw, 2.25rem);
  background: rgba(255, 252, 246, 0.68);
  backdrop-filter: blur(10px);
  box-shadow: 0 18px 48px rgba(47, 36, 29, 0.08);
  transition: box-shadow 0.3s ease, transform 0.3s ease, background 0.3s ease;
}

.brands-item:hover {
  background: rgba(255, 252, 246, 0.86);
  box-shadow: 0 24px 64px rgba(47, 36, 29, 0.14);
  transform: translateY(-0.35rem);
}

.brands-item__image {
  width: min(100%, 16rem);
  max-height: clamp(7rem, 11vw, 10rem);
  object-fit: contain;
  filter: saturate(0.6);
  transition: filter 0.3s ease, transform 0.3s ease;
}

.brands-item:hover .brands-item__image {
  filter: saturate(1);
  transform: scale(1.04);
}

@media (max-width: 991px) {
  .brands-shell {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .shortcode-brands-native {
    padding: 3.5rem 0;
  }

  .brands-shell {
    display: flex;
    gap: 0.9rem;
    margin-inline: auto;
    overflow-x: auto;
    padding: 0 4vw 0.75rem;
    scroll-padding-inline: 4vw;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
  }

  .brands-shell::-webkit-scrollbar {
    display: none;
  }

  .brands-item {
    flex: 0 0 min(74vw, 20rem);
    min-height: 13rem;
    scroll-snap-align: start;
  }

  .brands-item__image {
    width: min(100%, 16rem);
    max-height: 9rem;
  }
}
</style>


