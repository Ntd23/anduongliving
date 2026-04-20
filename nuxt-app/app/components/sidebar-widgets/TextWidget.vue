<script setup lang="ts">
import SidebarWidgetCard from "~/components/sidebar-widgets/SidebarWidgetCard.vue";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";

const props = defineProps<{
  widget: SidebarWidgetManifest;
  variant?: "sidebar" | "footer";
}>();

const title = computed(() => String(props.widget.data?.name || "").trim() || null);
const content = computed(() => String(props.widget.data?.content || "").trim());
const sanitizedContent = useSanitizedCmsHtml(() => content.value);
const variant = computed(() => props.variant || "sidebar");
</script>

<template>
  <SidebarWidgetCard v-if="variant === 'sidebar'" :title="title">
    <div class="text-widget" v-html="sanitizedContent" />
  </SidebarWidgetCard>

  <article v-else class="footer-text-widget">
    <h2 v-if="title" class="footer-text-widget__title">
      {{ title }}
    </h2>

    <div class="footer-text-widget__content" v-html="sanitizedContent" />
  </article>
</template>

<style scoped>
.text-widget,
.footer-text-widget__content {
  color: inherit;
}

.text-widget :deep(p),
.text-widget :deep(li),
.footer-text-widget__content :deep(p),
.footer-text-widget__content :deep(li) {
  line-height: 1.8;
}

.text-widget :deep(a) {
  color: var(--retreat-clay);
}

.footer-text-widget__title {
  margin: 0 0 1rem;
  color: #fff9f1;
  font-family: var(--font-display);
  font-size: 2rem;
  line-height: 1;
}

.footer-text-widget__content {
  color: rgba(248, 244, 236, 0.7);
}

.footer-text-widget__content :deep(a) {
  color: rgba(255, 249, 241, 0.86);
}
</style>
