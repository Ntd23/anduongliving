<script setup lang="ts">
import SidebarWidgetCard from "~/components/sidebar-widgets/SidebarWidgetCard.vue";
import { cmsAppRoutes } from "~~/shared/cms-routing";
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";

const props = defineProps<{
  widget: SidebarWidgetManifest;
}>();

const localePath = useLocalePath();
const query = ref("");

const title = computed(() => String(props.widget.data?.title || "Search"));
const description = computed(() => {
  const value = props.widget.data?.description;
  return typeof value === "string" && value.trim() ? value : null;
});

const submit = async () => {
  if (!query.value.trim()) {
    return;
  }

  await navigateTo(
    localePath({
      path: cmsAppRoutes.blog.search(),
      query: { q: query.value.trim() },
    }),
  );
};
</script>

<template>
  <SidebarWidgetCard :title="title" :description="description">
    <form class="sidebar-widget-search" @submit.prevent="submit">
      <input
        v-model="query"
        type="search"
        class="sidebar-widget-search__input"
        placeholder="Search blog posts"
        autocomplete="off"
      >
      <button type="submit" class="sidebar-widget-search__button">
        Search
      </button>
    </form>
  </SidebarWidgetCard>
</template>

<style scoped>
.sidebar-widget-search {
  display: grid;
  gap: 0.75rem;
}

.sidebar-widget-search__input {
  width: 100%;
  border: 1px solid rgba(63, 53, 45, 0.14);
  background: #fff;
  padding: 0.85rem 1rem;
  color: #2f241d;
}

.sidebar-widget-search__button {
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
</style>
