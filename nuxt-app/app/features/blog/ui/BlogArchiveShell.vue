<script setup lang="ts">
import type { PropType } from "vue";
import type { BlogPostSummary } from "~/features/blog/data/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";

const localePath = useLocalePath();

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  posts: {
    type: Array as PropType<BlogPostSummary[]>,
    default: () => [],
  },
  emptyMessage: {
    type: String,
    default: "No posts found.",
  },
  currentPage: {
    type: Number,
    default: 1,
  },
  lastPage: {
    type: Number,
    default: 1,
  },
});

const safePosts = computed(() => (Array.isArray(props.posts) ? props.posts : []));
</script>

<template>
  <main class="blog-archive">
    <h1 class="sr-only">
      {{ props.title }}
    </h1>

    <div v-if="safePosts.length" class="blog-archive__grid">
      <div
        v-for="post in safePosts"
        :key="post.id"
        class="blog-archive__item"
      >
        <BlogPostCard :post="post" />
      </div>
    </div>

    <section v-else class="blog-archive__empty cms-quiet-surface">
      <p class="cms-shell-eyebrow blog-archive__empty-eyebrow">
        Journal
      </p>
      <h2 class="blog-archive__empty-title">
        {{ props.title }}
      </h2>
      <p class="blog-archive__empty-copy">
        {{ props.emptyMessage }}
      </p>
      <NuxtLink :to="localePath(cmsAppRoutes.blog.index())" class="cms-link-arrow blog-archive__empty-link">
        Browse all posts
      </NuxtLink>
    </section>

    <BlogPagination
      v-if="props.currentPage && props.lastPage && props.lastPage > 1"
      :current-page="props.currentPage"
      :last-page="props.lastPage"
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
  gap: 2.4rem 2rem;
}

.blog-archive__item {
  min-width: 0;
}

.blog-archive__empty {
  padding: clamp(2rem, 4vw, 3rem);
  border-radius: var(--retreat-radius-card);
}

.blog-archive__empty-eyebrow {
  margin-bottom: 1.2rem;
}

.blog-archive__empty-title {
  margin: 0;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: clamp(2.2rem, 5vw, 3.8rem);
  font-weight: 600;
  line-height: 0.98;
}

.blog-archive__empty-copy {
  max-width: 34rem;
  margin: 1rem 0 0;
  color: var(--retreat-ink-soft);
  font-size: 1rem;
  line-height: 1.9;
}

.blog-archive__empty-link {
  margin-top: 1.4rem;
}

@media (min-width: 992px) {
  .blog-archive__grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 2.8rem 2.2rem;
  }
}
</style>



