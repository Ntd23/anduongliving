<script setup lang="ts">
import { normalizeBlogPostCollection, useFilteredBlogPosts } from "~/composables/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";

const route = useRoute();
const { locale } = useI18n();
const localePath = useLocalePath();
const routeFullPath = computed(() => route.fullPath);

const searchQuery = computed(() => String(route.query.q || "").trim());
const currentPage = computed(() => {
  const page = Number.parseInt(String(route.query.page || "1"), 10);
  return Number.isNaN(page) || page < 1 ? 1 : page;
});

const asyncKey = computed(() => `blog-search-${locale.value}-${routeFullPath.value}`);

const { data, error } = await useAsyncData(
  asyncKey,
  () => {
    if (!searchQuery.value) {
      return Promise.resolve({ data: [], meta: { current_page: 1, last_page: 1, total: 0 } });
    }

    return useFilteredBlogPosts({
      locale: locale.value,
      page: currentPage.value,
      per_page: 10,
      search: searchQuery.value,
    });
  },
  { watch: [locale, searchQuery, routeFullPath] },
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
const pageTitle = computed(() => (searchQuery.value ? `Search: ${searchQuery.value}` : "Blog Search"));
const breadcrumbs = computed(() => [
  { label: "Blog", to: localePath(cmsAppRoutes.blog.index()) },
  { label: pageTitle.value, to: null },
]);
const emptyMessage = computed(() =>
  searchQuery.value ? "No posts matched your search." : "Enter a keyword to start searching.",
);

useSeoMeta({
  title: pageTitle,
  description: "Search blog posts and stories.",
});
</script>

<template>
  <CmsBreadcrumbs :title="pageTitle" :crumbs="breadcrumbs" />

  <BlogArchiveShell
    :title="pageTitle"
    :posts="posts"
    :empty-message="emptyMessage"
    :current-page="currentPage"
    :last-page="meta?.last_page"
  />
</template>
