<script setup lang="ts">
import SidebarWidgetCard from "~/features/cms/ui/sidebar-widgets/SidebarWidgetCard.vue";
import { useBlogTags, type BlogTag } from "~/features/blog/data/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";
import type { SidebarWidgetManifest } from "~/features/cms/ui/sidebar-widgets/registry";

const props = defineProps<{
  widget: SidebarWidgetManifest;
}>();

const { locale } = useI18n();
const localePath = useLocalePath();

const limit = computed(() => Number.parseInt(String(props.widget.data?.limit || 5), 10) || 5);
const type = computed<"popular" | "all">(() => (limit.value > 0 ? "popular" : "all"));
const title = computed(() => String(props.widget.data?.title || "Tags"));

const { data } = await useAsyncData(
  () => `sidebar-tags-${props.widget.id}-${locale.value}`,
  () => useBlogTags({ locale: locale.value, limit: limit.value, type: type.value }),
  { watch: [locale, limit, type] },
);

const tags = computed<BlogTag[]>(() => data.value || []);
</script>

<template>
  <SidebarWidgetCard :title="title">
    <div v-if="tags.length" class="sidebar-tag-cloud">
      <NuxtLink
        v-for="tag in tags"
        :key="tag.id"
        :to="localePath(cmsAppRoutes.blog.tag(tag.slug))"
        class="sidebar-tag-cloud__link"
      >
        {{ tag.name }}
      </NuxtLink>
    </div>
    <p v-else class="sidebar-widget-empty">
      No tags yet.
    </p>
  </SidebarWidgetCard>
</template>

<style scoped>
.sidebar-tag-cloud {
  display: flex;
  flex-wrap: wrap;
  gap: 0.65rem;
}

.sidebar-tag-cloud__link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2.3rem;
  padding: 0.45rem 0.95rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.82);
  color: var(--retreat-ink);
  font-size: 0.82rem;
  font-weight: 600;
  text-decoration: none;
}

.sidebar-tag-cloud__link:hover {
  background: rgba(167, 122, 84, 0.14);
  color: var(--retreat-clay);
}

.sidebar-widget-empty {
  margin: 0;
  color: var(--retreat-ink-soft);
}
</style>



