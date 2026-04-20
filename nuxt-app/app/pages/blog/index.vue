<script setup lang="ts">
import { normalizeBlogPostCollection, useBlogPosts } from "~/composables/useBlog";

const route = useRoute();
const { locale } = useI18n();
const routeFullPath = computed(() => route.fullPath);

const currentPage = computed(() => {
  const page = Number.parseInt(String(route.query.page || "1"), 10);
  return Number.isNaN(page) || page < 1 ? 1 : page;
});

const asyncKey = computed(() => `blog-index-${locale.value}-${routeFullPath.value}`);

const { data, error } = await useAsyncData(
  asyncKey,
  () => useBlogPosts({ locale: locale.value, page: currentPage.value, per_page: 10 }),
  { watch: [locale, routeFullPath] },
);

if (error.value) {
  throw error.value;
}

definePageMeta({
  layout: "cms-blog-sidebar",
  key: (route) => route.fullPath,
});

const postsData = computed(() => normalizeBlogPostCollection(data.value));
const posts = computed(() => postsData.value.items);
const meta = computed(() => postsData.value.meta);
const pageTitle = computed(() => "Blog");
const breadcrumbs = computed(() => [
  { label: "Blog", to: null },
]);

useSeoMeta({
  title: pageTitle,
  description: "Stories, updates, and retreat notes from Anduong Living.",
});
</script>

<template>
  <CmsBreadcrumbs :title="pageTitle" :crumbs="breadcrumbs" />

  <BlogArchiveShell
    :title="pageTitle"
    :posts="posts"
    empty-message="No posts found."
    :current-page="currentPage"
    :last-page="meta?.last_page"
  />
</template>
