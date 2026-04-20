<script setup lang="ts">
import SidebarWidgetCard from "~/components/sidebar-widgets/SidebarWidgetCard.vue";
import {
  normalizeBlogPostCollection,
  useBlogPosts,
  type BlogPostSummary,
} from "~/composables/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";

const props = defineProps<{
  widget: SidebarWidgetManifest;
}>();

const { locale } = useI18n();
const localePath = useLocalePath();

const limit = computed(() => Math.max(1, Number.parseInt(String(props.widget.data?.limit || 5), 10) || 5));
const type = computed<"recent" | "popular">(() =>
  String(props.widget.data?.type || "recent").toLowerCase() === "popular" ? "popular" : "recent",
);
const title = computed(() => String(props.widget.data?.title || "Recent Posts"));
const description = computed(() => {
  const value = props.widget.data?.description;
  return typeof value === "string" && value.trim() ? value : null;
});

const { data } = await useAsyncData(
  () => `sidebar-posts-${props.widget.id}-${locale.value}`,
  () => useBlogPosts({ locale: locale.value, limit: limit.value, type: type.value }),
  { watch: [locale, limit, type] },
);

const posts = computed<BlogPostSummary[]>(() => normalizeBlogPostCollection(data.value).items);
const formatDate = (value?: string | null) => {
  if (!value) {
    return "";
  }

  return new Intl.DateTimeFormat(locale.value, {
    day: "2-digit",
    month: "short",
    year: "numeric",
  }).format(new Date(value));
};
</script>

<template>
  <SidebarWidgetCard :title="title" :description="description">
    <ul v-if="posts.length" class="sidebar-post-list">
      <li v-for="post in posts" :key="post.id" class="sidebar-post-list__item">
        <NuxtLink :to="localePath(cmsAppRoutes.blog.post(post.slug))" class="sidebar-post-list__link">
          {{ post.name }}
        </NuxtLink>
        <small v-if="post.created_at" class="sidebar-post-list__date">
          {{ formatDate(post.created_at) }}
        </small>
      </li>
    </ul>
    <p v-else class="sidebar-widget-empty">
      No posts yet.
    </p>
  </SidebarWidgetCard>
</template>

<style scoped>
.sidebar-post-list {
  margin: 0;
  padding: 0;
  list-style: none;
}

.sidebar-post-list__item {
  padding: 0.8rem 0;
  border-bottom: 1px solid rgba(63, 53, 45, 0.08);
}

.sidebar-post-list__item:first-child {
  padding-top: 0;
}

.sidebar-post-list__item:last-child {
  padding-bottom: 0;
  border-bottom: 0;
}

.sidebar-post-list__link {
  display: inline-block;
  color: #3f352d;
  font-weight: 600;
  text-decoration: none;
}

.sidebar-post-list__date {
  display: block;
  margin-top: 0.35rem;
  color: #7b6b5d;
}

.sidebar-widget-empty {
  margin: 0;
  color: #7b6b5d;
}
</style>
