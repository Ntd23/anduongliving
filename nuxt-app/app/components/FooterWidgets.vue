<script setup lang="ts">
import type { ThemeOptionsData } from "~/composables/useThemeOptions";
import BlogSocialsWidget from "~/components/sidebar-widgets/BlogSocialsWidget.vue";
import ContactInformationWidget from "~/components/sidebar-widgets/ContactInformationWidget.vue";
import CoreSimpleMenuWidget from "~/components/sidebar-widgets/CoreSimpleMenuWidget.vue";
import CustomMenuWidget from "~/components/sidebar-widgets/CustomMenuWidget.vue";
import NewsletterWidget from "~/components/sidebar-widgets/NewsletterWidget.vue";
import RoomContactWidget from "~/components/sidebar-widgets/RoomContactWidget.vue";
import TextWidget from "~/components/sidebar-widgets/TextWidget.vue";
import { useSidebarWidgets } from "~/composables/useSidebarWidgets";
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";

defineProps<{
  themeOptions?: ThemeOptionsData | null;
}>();

const { locale, t } = useI18n();

const activeLocale = computed(() => locale.value);

const { data, error } = await useAsyncData(
  () => `footer-sidebar-${activeLocale.value}`,
  () => useSidebarWidgets("footer_sidebar", activeLocale.value),
  { watch: [activeLocale] },
);

const widgets = computed<SidebarWidgetManifest[]>(() => data.value?.items || []);
const errorMessage = computed(() => error.value?.statusMessage || error.value?.message || "");
const hasWidgets = computed(() => widgets.value.length > 0);
</script>

<template>
  <div class="footer-widgets">
    <template v-if="hasWidgets">
      <template v-for="widget in widgets" :key="widget.id">
        <CustomMenuWidget
          v-if="widget.widget_id === 'CustomMenuWidget'"
          class="footer-widgets__item footer-widgets__item--native"
          :widget="widget"
          variant="footer"
        />

        <ContactInformationWidget
          v-else-if="widget.widget_id === 'ContactInformationMenuWidget'"
          class="footer-widgets__item footer-widgets__item--native"
          :widget="widget"
          :theme-options="themeOptions"
          variant="footer"
        />

        <NewsletterWidget
          v-else-if="widget.widget_id === 'NewsletterWidget'"
          class="footer-widgets__item footer-widgets__item--native"
          :widget="widget"
          variant="footer"
        />

        <CoreSimpleMenuWidget
          v-else-if="widget.widget_id === 'CoreSimpleMenu'"
          class="footer-widgets__item footer-widgets__item--native"
          :widget="widget"
          variant="footer"
        />

        <TextWidget
          v-else-if="widget.widget_id === 'Text'"
          class="footer-widgets__item footer-widgets__item--native"
          :widget="widget"
          variant="footer"
        />

        <BlogSocialsWidget
          v-else-if="widget.widget_id === 'BlogSocialsWidget'"
          class="footer-widgets__item footer-widgets__item--native"
          :widget="widget"
          variant="footer"
        />

        <RoomContactWidget
          v-else-if="widget.widget_id === 'RoomContactWidget'"
          class="footer-widgets__item footer-widgets__item--native"
          :widget="widget"
          variant="footer"
        />

        <div
          v-else
          class="footer-widgets__item footer-widgets__item--fallback"
          v-html="widget.rendered_html || ''"
        />
      </template>
    </template>

    <div v-else class="footer-widgets__empty">
      <p class="footer-widgets__empty-title">
        {{ t("Footer") }}
      </p>
      <p class="footer-widgets__empty-text">
        {{ errorMessage || "Chua co widget nao cho footer o ngon ngu nay." }}
      </p>
    </div>
  </div>
</template>

<style scoped>
.footer-widgets {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 2.5rem;
}

.footer-widgets__item {
  min-width: 0;
  align-self: start;
}

.footer-widgets__empty {
  grid-column: 1 / -1;
  padding: 1rem 0;
}

.footer-widgets__empty-title {
  margin: 0 0 0.5rem;
  color: #fff9f1;
  font-family: var(--font-display);
  font-size: 2rem;
}

.footer-widgets__empty-text {
  margin: 0;
  color: rgba(248, 244, 236, 0.7);
  line-height: 1.85;
}

.footer-widgets__item--fallback:deep(.widget),
.footer-widgets__item--fallback:deep(.single-footer-widget),
.footer-widgets__item--fallback:deep(.footer-widget) {
  min-width: 0;
}

.footer-widgets__item--fallback:deep(.widget-title),
.footer-widgets__item--fallback:deep(.footer-title),
.footer-widgets__item--fallback:deep(h4),
.footer-widgets__item--fallback:deep(h5) {
  margin: 0 0 1.1rem;
  color: #fff9f1;
  font-family: var(--font-display);
  font-size: 2rem;
  line-height: 1;
}

.footer-widgets__item--fallback:deep(p),
.footer-widgets__item--fallback:deep(li),
.footer-widgets__item--fallback:deep(span),
.footer-widgets__item--fallback:deep(address) {
  color: rgba(248, 244, 236, 0.7);
}

.footer-widgets__item--fallback:deep(ul) {
  margin: 0;
  padding: 0;
  list-style: none;
}

.footer-widgets__item--fallback:deep(li + li) {
  margin-top: 0.65rem;
}

.footer-widgets__item--fallback:deep(a) {
  color: rgba(255, 249, 241, 0.86);
  text-decoration: none;
}

.footer-widgets__item--fallback:deep(a:hover) {
  color: #d6c09b;
}

.footer-widgets__item--fallback:deep(img) {
  max-width: 100%;
  height: auto;
}

.footer-widgets__item--fallback:deep(.footer-social),
.footer-widgets__item--fallback:deep(.widget-social) {
  display: flex;
  flex-wrap: wrap;
  gap: 0.8rem;
}

.footer-widgets__item--fallback:deep(.footer-social a),
.footer-widgets__item--fallback:deep(.widget-social a) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.8rem;
  height: 2.8rem;
  border: 1px solid rgba(255, 249, 241, 0.12);
  border-radius: 999px;
  background: rgba(255, 249, 241, 0.04);
}

.footer-widgets__item--fallback:deep(input),
.footer-widgets__item--fallback:deep(textarea),
.footer-widgets__item--fallback:deep(select) {
  width: 100%;
  border: 1px solid rgba(255, 249, 241, 0.14);
  background: rgba(255, 249, 241, 0.05);
  padding: 0.95rem 1rem;
  color: #fff9f1;
}

.footer-widgets__item--fallback:deep(.btn),
.footer-widgets__item--fallback:deep(.btn-custom),
.footer-widgets__item--fallback:deep(button[type="submit"]) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3.1rem;
  border: 0;
  border-radius: 999px;
  background: linear-gradient(180deg, #8f7659 0%, #72634d 100%);
  padding: 0.8rem 1.4rem;
  color: #fffdf8;
  font-size: 0.76rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

@media (max-width: 991px) {
  .footer-widgets {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}
</style>
