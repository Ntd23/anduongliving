<script setup lang="ts">
import SidebarWidgetCard from "~/features/cms/ui/sidebar-widgets/SidebarWidgetCard.vue";
import { useBlogCategories, type BlogCategory } from "~/features/blog/data/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";
import type { SidebarWidgetManifest } from "~/features/cms/ui/sidebar-widgets/registry";

const props = defineProps<{
  widget: SidebarWidgetManifest;
}>();

const { locale } = useI18n();
const localePath = useLocalePath();

const limit = computed(() => Number.parseInt(String(props.widget.data?.limit || 5), 10) || 5);
const type = computed<"popular" | "all">(() => (limit.value > 0 ? "popular" : "all"));
const title = computed(() => String(props.widget.data?.title || "Categories"));

const { data } = await useAsyncData(
  () => `sidebar-categories-${props.widget.id}-${locale.value}`,
  () => useBlogCategories({ locale: locale.value, limit: limit.value, type: type.value }),
  { watch: [locale, limit, type] },
);

const categories = computed<BlogCategory[]>(() => data.value || []);
</script>

<template>
  <SidebarWidgetCard :title="title">
    <ul v-if="categories.length" class="sidebar-taxonomy-list">
      <li v-for="category in categories" :key="category.id" class="sidebar-taxonomy-list__item">
        <NuxtLink :to="localePath(cmsAppRoutes.blog.category(category.slug))" class="sidebar-taxonomy-list__link">
          {{ category.name }}
        </NuxtLink>
        <span v-if="category.posts_count !== undefined" class="sidebar-taxonomy-list__count">
          {{ category.posts_count }}
        </span>
      </li>
    </ul>
    <p v-else class="sidebar-widget-empty">
      No categories yet.
    </p>
  </SidebarWidgetCard>
</template>

<style scoped>
.sidebar-taxonomy-list {
  display: grid;
  gap: 0.7rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.sidebar-taxonomy-list__item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid rgba(56, 41, 30, 0.08);
}

.sidebar-taxonomy-list__item:last-child {
  padding-bottom: 0;
  border-bottom: 0;
}

.sidebar-taxonomy-list__link {
  color: var(--retreat-ink);
  text-decoration: none;
}

.sidebar-taxonomy-list__link:hover {
  color: var(--retreat-clay);
}

.sidebar-taxonomy-list__count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 2rem;
  min-height: 2rem;
  padding: 0.2rem 0.65rem;
  border-radius: 999px;
  background: rgba(167, 122, 84, 0.12);
  color: var(--retreat-clay);
  font-size: 0.76rem;
  font-weight: 700;
}

.sidebar-widget-empty {
  margin: 0;
  color: var(--retreat-ink-soft);
}
</style>



