<script setup lang="ts">
import SidebarWidgetCard from "~/components/sidebar-widgets/SidebarWidgetCard.vue";
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";

const props = defineProps<{
  widget: SidebarWidgetManifest;
  variant?: "sidebar" | "footer";
}>();

type SocialItem = {
  href: string;
  iconClass: string;
};

const title = computed(() => String(props.widget.data?.title || "Follow Us"));
const variant = computed(() => props.variant || "sidebar");

const socials = computed<SocialItem[]>(() =>
  Array.from({ length: 5 }, (_, index) => index + 1)
    .map((item) => {
      const href = String(props.widget.data?.[`link_${item}`] || "").trim();
      const iconClass = String(props.widget.data?.[`icon_${item}`] || "").trim();

      if (!href || !iconClass) {
        return null;
      }

      return {
        href,
        iconClass,
      } satisfies SocialItem;
    })
    .filter(Boolean) as SocialItem[],
);
</script>

<template>
  <SidebarWidgetCard v-if="variant === 'sidebar'" :title="title">
    <div class="blog-socials-widget">
      <a
        v-for="social in socials"
        :key="`${social.href}-${social.iconClass}`"
        :href="social.href"
        class="blog-socials-widget__link"
        target="_blank"
        rel="noopener noreferrer"
      >
        <i :class="social.iconClass" aria-hidden="true" />
      </a>
    </div>
  </SidebarWidgetCard>

  <article v-else class="footer-socials-widget">
    <h2 class="footer-socials-widget__title">
      {{ title }}
    </h2>

    <div class="footer-socials-widget__list">
      <a
        v-for="social in socials"
        :key="`${social.href}-${social.iconClass}`"
        :href="social.href"
        class="footer-socials-widget__link"
        target="_blank"
        rel="noopener noreferrer"
      >
        <i :class="social.iconClass" aria-hidden="true" />
      </a>
    </div>
  </article>
</template>

<style scoped>
.blog-socials-widget,
.footer-socials-widget__list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.85rem;
}

.blog-socials-widget__link,
.footer-socials-widget__link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 3rem;
  height: 3rem;
  border-radius: 999px;
  text-decoration: none;
  transition:
    transform 0.2s ease,
    border-color 0.2s ease,
    color 0.2s ease,
    background-color 0.2s ease;
}

.blog-socials-widget__link {
  border: 1px solid rgba(63, 53, 45, 0.12);
  background: rgba(255, 255, 255, 0.72);
  color: var(--retreat-ink);
}

.blog-socials-widget__link:hover,
.footer-socials-widget__link:hover {
  transform: translateY(-1px);
}

.blog-socials-widget__link:hover {
  border-color: rgba(141, 115, 85, 0.38);
  color: var(--retreat-clay);
}

.footer-socials-widget__title {
  margin: 0 0 1rem;
  color: #fff9f1;
  font-family: var(--font-display);
  font-size: 2rem;
  line-height: 1;
}

.footer-socials-widget__link {
  border: 1px solid rgba(255, 249, 241, 0.12);
  background: rgba(255, 249, 241, 0.04);
  color: rgba(255, 249, 241, 0.84);
}

.footer-socials-widget__link:hover {
  border-color: rgba(214, 192, 155, 0.42);
  color: #fff9f1;
}
</style>
