<script setup lang="ts">
import type { BlogPostSummary } from "~/composables/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";

const localePath = useLocalePath();

defineProps<{
  title: string;
  posts: BlogPostSummary[];
  emptyMessage: string;
  currentPage?: number;
  lastPage?: number;
}>();
</script>

<template>
  <main class="blog-archive">
    <h1 class="blog-archive__title sr-only">
      {{ title }}
    </h1>

    <div v-if="posts.length" class="blog-archive__grid">
      <BlogPostCard v-for="post in posts" :key="post.id" :post="post" />
    </div>

    <div v-else class="blog-archive__empty">
      <p>
        {{ emptyMessage }}
      </p>
      <NuxtLink :to="localePath(cmsAppRoutes.blog.index())" class="blog-archive__empty-link">
        Back here
      </NuxtLink>
    </div>

    <BlogPagination
      v-if="currentPage && lastPage && lastPage > 1"
      :current-page="currentPage"
      :last-page="lastPage"
    />
  </main>
</template>

<style scoped>
.blog-archive {
  min-width: 0;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.blog-archive__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.75rem;
}

.blog-archive__empty {
  padding: 1rem 0 0;
  color: #5d5147;
  line-height: 1.8;
}

.blog-archive__empty p {
  margin: 0;
}

.blog-archive__empty-link {
  display: inline-flex;
  margin-top: 0.5rem;
  color: #7c5c3b;
  font-weight: 600;
  text-decoration: underline;
}

@media (min-width: 992px) {
  .blog-archive__grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
