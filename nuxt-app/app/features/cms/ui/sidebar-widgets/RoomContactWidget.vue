<script setup lang="ts">
import SidebarWidgetCard from "~/features/cms/ui/sidebar-widgets/SidebarWidgetCard.vue";
import type { SidebarWidgetManifest } from "~/features/cms/ui/sidebar-widgets/registry";

const props = defineProps<{
  widget: SidebarWidgetManifest;
  variant?: "sidebar" | "footer";
}>();

const title = computed(() => String(props.widget.data?.title || "Room contact"));
const phone = computed(() => String(props.widget.data?.phone || "").trim());
const phoneHref = computed(() => `tel:${phone.value.replace(/[^\d+]/g, "")}`);
const variant = computed(() => props.variant || "sidebar");
</script>

<template>
  <SidebarWidgetCard v-if="variant === 'sidebar'" :title="title">
    <a v-if="phone" :href="phoneHref" class="room-contact-widget__phone">
      {{ phone }}
    </a>
  </SidebarWidgetCard>

  <article v-else class="footer-room-contact-widget">
    <h2 class="footer-room-contact-widget__title">
      {{ title }}
    </h2>

    <a v-if="phone" :href="phoneHref" class="footer-room-contact-widget__phone">
      {{ phone }}
    </a>
  </article>
</template>

<style scoped>
.room-contact-widget__phone,
.footer-room-contact-widget__phone {
  display: inline-flex;
  color: inherit;
  text-decoration: none;
}

.room-contact-widget__phone {
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: 2rem;
  line-height: 1;
}

.footer-room-contact-widget__title {
  margin: 0 0 1rem;
  color: #fff9f1;
  font-family: var(--font-display);
  font-size: 2rem;
  line-height: 1;
}

.footer-room-contact-widget__phone {
  color: rgba(255, 249, 241, 0.84);
  font-family: var(--font-display);
  font-size: 2rem;
  line-height: 1;
}
</style>



