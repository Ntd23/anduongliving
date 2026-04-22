<script setup lang="ts">
import type { BlogPostSummary } from "~/composables/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";
import { formatCmsDate } from "~/utils/locale-format";
import { useResolvedCmsAsset } from "~/composables/useResolvedCmsAsset";

const props = defineProps<{
  post: BlogPostSummary;
}>();

const { locale } = useI18n();
const localePath = useLocalePath();
const resolveAsset = useResolvedCmsAsset();

const postUrl = computed(() => localePath(cmsAppRoutes.blog.post(props.post.slug)));
const postImage = computed(() => resolveAsset(props.post.image) || null);
const formattedDate = computed(() => {
  if (!props.post.created_at) {
    return null;
  }

  return formatCmsDate(props.post.created_at, locale.value, { monthStyle: "long" });
});
const firstCategory = computed(() => props.post.categories?.[0]?.name || "Journal");
</script>

<template>
  <article class="blog-post-card cms-quiet-surface">
    <div class="blog-post-card__media cms-editorial-frame">
      <NuxtLink :to="postUrl" class="blog-post-card__image-link">
        <img
          v-if="postImage"
          :src="postImage"
          :alt="post.name"
          class="blog-post-card__image"
        >
        <div v-else class="blog-post-card__image-placeholder" aria-hidden="true" />
      </NuxtLink>

      <p v-if="formattedDate" class="blog-post-card__date">
        {{ formattedDate }}
      </p>
    </div>

    <div class="blog-post-card__content">
      <p class="blog-post-card__eyebrow">
        {{ firstCategory }}
      </p>

      <h2 class="blog-post-card__title">
        <NuxtLink :to="postUrl">
          {{ post.name }}
        </NuxtLink>
      </h2>

      <p v-if="post.description" class="blog-post-card__description">
        {{ post.description }}
      </p>

      <div class="blog-post-card__footer">
        <NuxtLink :to="postUrl" class="cms-link-arrow">
          Read story
        </NuxtLink>
      </div>
    </div>
  </article>
</template>

<style scoped>
.blog-post-card {
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
  border-radius: var(--retreat-radius-card);
}

.blog-post-card__media {
  position: relative;
  border-radius: 0;
  background: linear-gradient(180deg, rgba(239, 229, 216, 0.84), rgba(226, 213, 196, 0.9));
}

.blog-post-card__image-link {
  display: block;
  overflow: hidden;
}

.blog-post-card__image {
  display: block;
  width: 100%;
  aspect-ratio: 1 / 0.84;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.blog-post-card__image-placeholder {
  width: 100%;
  aspect-ratio: 1 / 0.84;
  background:
    radial-gradient(circle at top left, rgba(255, 255, 255, 0.54), transparent 28%),
    linear-gradient(135deg, rgba(167, 122, 84, 0.12), rgba(107, 113, 83, 0.12)),
    #efe5d8;
}

.blog-post-card__image-link:hover .blog-post-card__image {
  transform: scale(1.04);
}

.blog-post-card__date {
  position: absolute;
  bottom: 1.15rem;
  left: 1.15rem;
  margin: 0;
  padding: 0.52rem 0.95rem;
  border-radius: 999px;
  background: rgba(248, 244, 236, 0.94);
  color: var(--retreat-ink);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  box-shadow: 0 12px 30px rgba(37, 27, 21, 0.1);
}

.blog-post-card__content {
  display: flex;
  flex: 1;
  flex-direction: column;
  padding: 1.8rem 1.7rem 1.9rem;
}

.blog-post-card__eyebrow {
  margin: 0;
  color: var(--retreat-clay);
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
}

.blog-post-card__title {
  margin: 0.75rem 0 0;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: clamp(2rem, 2.6vw, 3rem);
  font-weight: 600;
  line-height: 0.96;
  letter-spacing: -0.025em;
}

.blog-post-card__title a {
  color: inherit;
  text-decoration: none;
}

.blog-post-card__title a:hover {
  color: var(--retreat-clay);
}

.blog-post-card__description {
  margin: 1rem 0 0;
  color: var(--retreat-ink-soft);
  font-size: 0.98rem;
  line-height: 1.9;
  display: -webkit-box;
  overflow: hidden;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}

.blog-post-card__footer {
  margin-top: auto;
  padding-top: 1.55rem;
}
</style>
