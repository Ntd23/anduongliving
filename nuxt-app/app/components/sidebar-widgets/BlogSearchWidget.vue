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
        placeholder="Search journal stories"
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
  gap: 0.8rem;
}

.sidebar-widget-search__input {
  width: 100%;
  min-height: 3.6rem;
  border: 1px solid rgba(56, 41, 30, 0.1);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.78);
  padding: 0.95rem 1.2rem;
  color: var(--retreat-ink);
}

.sidebar-widget-search__input::placeholder {
  color: rgba(75, 59, 49, 0.68);
}

.sidebar-widget-search__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3.4rem;
  border: 0;
  border-radius: 999px;
  background: linear-gradient(180deg, rgba(107, 113, 83, 0.98), rgba(83, 91, 60, 0.98));
  color: #fffdf8;
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.sidebar-widget-search__button:hover {
  transform: translateY(-1px);
}

@media (min-width: 768px) {
  .sidebar-widget-search {
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
  }

  .sidebar-widget-search__button {
    min-width: 8rem;
    padding-inline: 1.3rem;
  }
}
</style>
