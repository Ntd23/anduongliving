<script setup lang="ts">
import { useSidebarWidgets } from "~/features/cms/data/useSidebarWidgets";
import {
  isNativeSidebarWidget,
  resolveSidebarWidgetComponent,
  type SidebarWidgetManifest,
} from "~/features/cms/ui/sidebar-widgets/registry";

const props = withDefaults(
  defineProps<{
    sidebarId?: string;
  }>(),
  {
    sidebarId: "blog_sidebar",
  },
);

const { locale, t } = useI18n();

const activeLocale = computed(() => locale.value);

const { data, error } = await useAsyncData(
  () => `sidebar-${props.sidebarId}-${activeLocale.value}`,
  () => useSidebarWidgets(props.sidebarId, activeLocale.value),
  { watch: [activeLocale] },
);

const widgets = computed<SidebarWidgetManifest[]>(() => data.value?.items || []);
const errorMessage = computed(() => error.value?.statusMessage || error.value?.message || "");
</script>

<template>
  <div class="blog-sidebar-widgets">
    <template v-if="widgets.length">
      <component
        :is="resolveSidebarWidgetComponent(widget.widget_id)"
        v-for="widget in widgets"
        :key="widget.id"
        :widget="widget"
        :class="{
          'blog-sidebar-widgets__native': isNativeSidebarWidget(widget.widget_id),
          'blog-sidebar-widgets__fallback': !isNativeSidebarWidget(widget.widget_id),
        }"
      />
    </template>

    <div v-else class="blog-sidebar-widgets__empty">
      <p class="blog-sidebar-widgets__empty-title">
        {{ t("Sidebar") }}
      </p>
      <p class="blog-sidebar-widgets__empty-text">
        {{ errorMessage || "Chua co widget nao cho blog sidebar o ngon ngu nay." }}
      </p>
    </div>
  </div>
</template>

<style scoped>
.blog-sidebar-widgets {
  display: grid;
  gap: 2rem;
  min-width: 0;
}

.blog-sidebar-widgets__empty {
  border: 1px solid rgba(63, 53, 45, 0.1);
  background: rgba(255, 250, 244, 0.94);
  padding: 1.5rem;
  box-shadow: 0 16px 40px rgba(63, 53, 45, 0.08);
}

.blog-sidebar-widgets__empty-title {
  margin: 0 0 0.5rem;
  color: #8d7355;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.blog-sidebar-widgets__empty-text {
  margin: 0;
  color: #5d5147;
  line-height: 1.75;
}

.blog-sidebar-widgets__fallback:deep(.widget),
.blog-sidebar-widgets__fallback:deep(.custom-blog-post-sidebar) {
  margin-bottom: 0;
  border: 1px solid rgba(63, 53, 45, 0.08);
  background: rgba(255, 251, 246, 0.96);
  padding: 1.5rem;
  box-shadow: 0 16px 40px rgba(63, 53, 45, 0.06);
}

.blog-sidebar-widgets__fallback:deep(.widget-title) {
  margin: 0 0 1rem;
  color: #2f241d;
  font-family: "Iowan Old Style", "Palatino Linotype", "Book Antiqua", Georgia, serif;
  font-size: 1.35rem;
  line-height: 1.2;
}

.blog-sidebar-widgets__fallback:deep(ul) {
  margin: 0;
  padding: 0;
  list-style: none;
}

.blog-sidebar-widgets__fallback:deep(li) {
  margin: 0;
  padding: 0.8rem 0;
  border-bottom: 1px solid rgba(63, 53, 45, 0.08);
}

.blog-sidebar-widgets__fallback:deep(li:last-child) {
  border-bottom: 0;
  padding-bottom: 0;
}

.blog-sidebar-widgets__fallback:deep(li:first-child) {
  padding-top: 0;
}

.blog-sidebar-widgets__fallback:deep(a) {
  color: #3f352d;
  text-decoration: none;
  transition: color 0.2s ease;
}

.blog-sidebar-widgets__fallback:deep(a:hover) {
  color: #8d7355;
}

.blog-sidebar-widgets__fallback:deep(.float-end) {
  color: #8d7355;
  float: right;
}

.blog-sidebar-widgets__fallback:deep(.custom-search-form) {
  display: grid;
  gap: 0.75rem;
}

.blog-sidebar-widgets__fallback:deep(.search-field) {
  width: 100%;
  border: 1px solid rgba(63, 53, 45, 0.14);
  background: #fff;
  padding: 0.85rem 1rem;
  color: #2f241d;
}

.blog-sidebar-widgets__fallback:deep(.btn),
.blog-sidebar-widgets__fallback:deep(.btn-custom) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3rem;
  border: 0;
  background: #7c5c3b;
  color: #fff;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.blog-sidebar-widgets__fallback:deep(.widget-social) {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.blog-sidebar-widgets__fallback:deep(.widget-social a) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.75rem;
  height: 2.75rem;
  border: 1px solid rgba(63, 53, 45, 0.12);
  background: #fff;
}

.blog-sidebar-widgets__fallback:deep(.tagcloud) {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
}

.blog-sidebar-widgets__fallback:deep(.tag-cloud-link) {
  border: 1px solid rgba(63, 53, 45, 0.1);
  background: #fff;
  padding: 0.45rem 0.8rem;
  font-size: 0.9rem !important;
}

.blog-sidebar-widgets__fallback:deep(.custom-blog-post-name) {
  margin-bottom: 0.3rem;
  font-weight: 600;
}

.blog-sidebar-widgets__fallback:deep(small) {
  color: #7b6b5d;
}
</style>



