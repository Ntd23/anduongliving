<script setup lang="ts">
import { normalizeBlogPostCollection, useBlogTag, useFilteredBlogPosts } from "~/composables/useBlog";
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

const asyncKey = computed(() => `blog-tag-${slug.value}-${locale.value}-${routeFullPath.value}`);

const { data, error } = await useAsyncData(
  asyncKey,
  async () => {
    const tag = await useBlogTag(slug.value, locale.value);
    const posts = await useFilteredBlogPosts({
      locale: locale.value,
      page: currentPage.value,
      per_page: 10,
      tags: tag.id,
    });

    return { tag, posts };
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

const tag = computed(() => data.value?.tag);
const postsData = computed(() => normalizeBlogPostCollection(data.value?.posts));
const posts = computed(() => postsData.value.items);
const meta = computed(() => postsData.value.meta);
const breadcrumbs = computed(() => [
  { label: "Blog", to: localePath(cmsAppRoutes.blog.index()) },
  { label: tag.value?.name || "Tag", to: null },
]);

useSeoMeta({
  title: computed(() => tag.value?.name || "Blog Tag"),
  description: computed(() => tag.value?.description || "Browse posts with this tag."),
});
</script>

<template>
  <CmsBreadcrumbs :title="tag?.name || 'Blog Tag'" :crumbs="breadcrumbs" />

  <BlogArchiveShell
    :title="tag?.name || 'Blog Tag'"
    :posts="posts"
    empty-message="No posts found with this tag."
    :current-page="currentPage"
    :last-page="meta?.last_page"
  />
</template>
