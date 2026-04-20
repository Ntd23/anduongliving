<script setup lang="ts">
import type { BlogPostSummary } from "~/composables/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";

const props = defineProps<{
  post: BlogPostSummary;
}>();

const { locale } = useI18n();
const localePath = useLocalePath();
const postUrl = computed(() => localePath(cmsAppRoutes.blog.post(props.post.slug)));

const formattedDate = computed(() => {
  if (!props.post.created_at) {
    return null;
  }

  return new Intl.DateTimeFormat(locale.value, {
    day: "2-digit",
    month: "long",
    year: "numeric",
  }).format(new Date(props.post.created_at));
});
</script>

<template>
  <article class="bsingle__post">
    <div class="bsingle__post-thumb blog-active hover-zoomin">
      <div v-if="post.image" class="slide-post">
        <NuxtLink :to="postUrl" class="blog-post-card__image-link">
          <img :src="post.image" :alt="post.name" class="blog-post-card__image">
        </NuxtLink>
      </div>
    </div>

    <div class="bsingle__content">
      <p v-if="formattedDate" class="date-home">
        {{ formattedDate }}
      </p>
      <h2 class="blog-post-card__title">
        <NuxtLink :to="postUrl">
          {{ post.name }}
        </NuxtLink>
      </h2>
      <p v-if="post.description" class="blog-post-card__description">
        {{ post.description }}
      </p>

      <div class="blog__btn">
        <NuxtLink :to="postUrl">
          Read More
        </NuxtLink>
      </div>
    </div>
  </article>
</template>

<style scoped>
.bsingle__post {
  margin-bottom: 0;
}

.blog-post-card__image-link {
  display: block;
  overflow: hidden;
  background: #f4ede2;
}

.bsingle__post-thumb {
  margin-bottom: 1.35rem;
}

.blog-post-card__image {
  display: block;
  width: 100%;
  aspect-ratio: 16 / 9;
  object-fit: cover;
  transition: transform 0.45s ease;
}

.blog-post-card__image-link:hover .blog-post-card__image {
  transform: scale(1.04);
}

.bsingle__content {
  color: #2f241d;
}

.date-home {
  margin: 0;
  color: #8d7355;
  font-size: 0.92rem;
  font-weight: 500;
}

.blog-post-card__title {
  margin: 0.65rem 0 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.65rem, 2.5vw, 2.35rem);
  line-height: 1.08;
}

.blog-post-card__title a {
  color: inherit;
  text-decoration: none;
}

.blog-post-card__description {
  margin: 0.9rem 0 0;
  color: #5d5147;
  line-height: 1.75;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.blog__btn {
  margin-top: 1rem;
}

.blog__btn a {
  color: #7c5c3b;
  font-weight: 600;
  text-decoration: none;
}

.blog__btn a:hover {
  color: #5f4327;
}
</style>
