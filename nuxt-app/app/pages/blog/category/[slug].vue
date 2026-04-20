<script setup lang="ts">
import { normalizeBlogPostCollection, useBlogCategory, useFilteredBlogPosts } from "~/composables/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";

const route = useRoute();
const { locale } = useI18n();
const localePath = useLocalePath();
const routeFullPath = computed(() => route.fullPath);

const slug = computed(() => String(route.params.slug || ""));
const currentPage = computed(() => {
  const page = Number.parseInt(String(route.query.page || "1"), 10);
  return Number.isNaN(page) || page < 1 ? 1 : page;
});

const asyncKey = computed(() => `blog-category-${slug.value}-${locale.value}-${routeFullPath.value}`);

const { data, error } = await useAsyncData(
  asyncKey,
  async () => {
    const category = await useBlogCategory(slug.value, locale.value);
    const posts = await useFilteredBlogPosts({
      locale: locale.value,
      page: currentPage.value,
      per_page: 10,
      categories: category.id,
    });

    return { category, posts };
  },
  { watch: [slug, locale, routeFullPath] },
);

if (error.value) {
  throw error.value;
}

definePageMeta({
  layout: "cms-blog-sidebar",
  key: (route) => route.fullPath,
});

const category = computed(() => data.value?.category);
const postsData = computed(() => normalizeBlogPostCollection(data.value?.posts));
const posts = computed(() => postsData.value.items);
const meta = computed(() => postsData.value.meta);
const breadcrumbs = computed(() => [
  { label: "Blog", to: localePath(cmsAppRoutes.blog.index()) },
  { label: category.value?.name || "Category", to: null },
]);

useSeoMeta({
  title: computed(() => category.value?.name || "Blog Category"),
  description: computed(() => category.value?.description || "Browse posts in this category."),
});
</script>

<template>
  <CmsBreadcrumbs :title="category?.name || 'Blog Category'" :crumbs="breadcrumbs" />

  <BlogArchiveShell
    :title="category?.name || 'Blog Category'"
    :posts="posts"
    empty-message="No posts found in this category."
    :current-page="currentPage"
    :last-page="meta?.last_page"
  />
</template>
