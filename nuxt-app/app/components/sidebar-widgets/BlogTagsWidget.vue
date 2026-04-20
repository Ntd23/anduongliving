<script setup lang="ts">
import SidebarWidgetCard from "~/components/sidebar-widgets/SidebarWidgetCard.vue";
import { useBlogTags, type BlogTag } from "~/composables/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";

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
  gap: 0.6rem;
}

.sidebar-tag-cloud__link {
  border: 1px solid rgba(63, 53, 45, 0.1);
  background: #fff;
  padding: 0.45rem 0.8rem;
  color: #3f352d;
  font-size: 0.9rem;
  text-decoration: none;
}

.sidebar-widget-empty {
  margin: 0;
  color: #7b6b5d;
}
</style>
