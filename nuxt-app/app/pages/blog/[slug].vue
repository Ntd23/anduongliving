<script setup lang="ts">
import type { BlogPostSummary } from "~/composables/useBlog";
import { useBlogPost } from "~/composables/useBlog";
import {
  cmsAppRoutes,
  sanitizeCmsHtmlContent,
} from "~~/shared/cms-routing";

const route = useRoute();
const { locale } = useI18n();
const localePath = useLocalePath();
const routeFullPath = computed(() => route.fullPath);
const runtimeConfig = useRuntimeConfig();

const slug = computed(() => String(route.params.slug || ""));
const asyncKey = computed(() => `blog-post-${slug.value}-${locale.value}-${routeFullPath.value}`);

const { data, error } = await useAsyncData(
  asyncKey,
  () => useBlogPost(slug.value, locale.value),
  { watch: [slug, locale, routeFullPath] },
);

if (error.value) {
  throw error.value;
}

definePageMeta({
  layout: "cms-blog-sidebar",
  key: (route) => route.fullPath,
});

const post = computed(() => data.value);
const firstCategory = computed(() => post.value?.categories?.[0] || null);
const breadcrumbs = computed(() => [
  { label: "Blog", to: localePath(cmsAppRoutes.blog.index()) },
  ...(firstCategory.value
    ? [{ label: firstCategory.value.name, to: localePath(cmsAppRoutes.blog.category(firstCategory.value.slug)) }]
    : []),
  { label: post.value?.name || "Blog Post", to: null },
]);

const formattedDate = computed(() => {
  if (!post.value?.created_at) {
    return null;
  }

  return new Intl.DateTimeFormat(locale.value, {
    day: "2-digit",
    month: "short",
    year: "numeric",
  }).format(new Date(post.value.created_at));
});

const viewCount = computed(() => {
  const value = Number(post.value?.views || 0);
  return Number.isFinite(value) ? value.toLocaleString(locale.value) : "0";
});

const socialIcons: Record<string, string> = {
  facebook: "ph:facebook-logo-bold",
  twitter: "ph:twitter-logo-bold",
  instagram: "ph:instagram-logo-bold",
  behance: "ph:behance-logo-bold",
  linkedin: "ph:linkedin-logo-bold",
};

const resolvePostLink = (entry?: BlogPostSummary | null) =>
  entry ? localePath(cmsAppRoutes.blog.post(entry.slug)) : undefined;

const safeContent = computed(() =>
  sanitizeCmsHtmlContent(post.value?.content, runtimeConfig.public.siteUrl),
);
const safeAuthorBio = computed(() =>
  sanitizeCmsHtmlContent(post.value?.author?.bio, runtimeConfig.public.siteUrl),
);

useSeoMeta({
  title: computed(() => post.value?.name || "Blog Post"),
  description: computed(() => post.value?.description || ""),
  ogTitle: computed(() => post.value?.name || "Blog Post"),
  ogDescription: computed(() => post.value?.description || ""),
  ogImage: computed(() => post.value?.image || undefined),
});
</script>

<template>
  <CmsBreadcrumbs
    v-if="post"
    :title="post.name"
    :crumbs="breadcrumbs"
  />

  <main v-if="post" class="blog-post-detail">
    <article class="blog-details-wrap">
      <div class="details__content pb-30">
        <h1 class="details__title">
          {{ post.name }}
        </h1>

        <div class="meta-info">
          <ul>
            <li><span class="meta-info__icon">◔</span>{{ viewCount }}</li>
            <li v-if="formattedDate"><span class="meta-info__icon">◷</span>{{ formattedDate }}</li>
          </ul>
        </div>

        <div class="ck-content details__body" v-html="safeContent" />

        <div v-if="post.tags?.length" class="post__tag">
          <h5>Related Tags</h5>
          <ul>
            <li v-for="tag in post.tags" :key="tag.id">
              <NuxtLink :to="localePath(cmsAppRoutes.blog.tag(tag.slug))">
                {{ tag.name }}
              </NuxtLink>
            </li>
          </ul>
        </div>
      </div>

      <div
        class="posts_navigation pt-35 pb-100"
        :class="{ 'posts_navigation--empty': !post.navigation?.previous && !post.navigation?.next }"
      >
        <div class="posts_navigation__row">
          <div v-if="post.navigation?.previous" class="prev-link">
            <span>Prev Post</span>
            <h4>
              <NuxtLink :to="resolvePostLink(post.navigation.previous)">
                {{ post.navigation.previous?.name }}
              </NuxtLink>
            </h4>
          </div>
          <div v-else class="prev-link prev-link--placeholder" />

          <NuxtLink
            v-if="firstCategory"
            :to="localePath(cmsAppRoutes.blog.category(firstCategory.slug))"
            class="blog-filter"
            :aria-label="firstCategory.name"
          >
            <span class="blog-filter__glyph">
              <span />
              <span />
              <span />
              <span />
            </span>
          </NuxtLink>
          <div v-else class="blog-filter blog-filter--placeholder" aria-hidden="true" />

          <div v-if="post.navigation?.next" class="next-link">
            <span>Next Post</span>
            <h4>
              <NuxtLink :to="resolvePostLink(post.navigation.next)">
                {{ post.navigation.next?.name }}
              </NuxtLink>
            </h4>
          </div>
          <div v-else class="next-link next-link--placeholder" />
        </div>
      </div>

      <div v-if="post.author" class="avatar__wrap text-center mb-45">
        <div class="avatar-img">
          <img
            v-if="post.author.avatar_url"
            class="author-blog-avatar"
            :src="post.author.avatar_url"
            :alt="post.author.name"
          >
        </div>

        <div class="avatar__info">
          <h5>{{ post.author.name }}</h5>

          <div v-if="post.author.socials?.length" class="avatar__info-social">
            <a
              v-for="social in post.author.socials"
              :key="social.name"
              :href="social.url"
              target="_blank"
              rel="noopener noreferrer"
              :aria-label="social.name"
            >
              <Icon :name="socialIcons[social.name] || 'ph:link-bold'" />
            </a>
          </div>
        </div>

        <div
          v-if="safeAuthorBio"
          class="avatar__wrap-content"
          v-html="safeAuthorBio"
        />
      </div>
    </article>
  </main>
</template>

<style scoped>
.blog-post-detail {
  min-width: 0;
}

.blog-details-wrap {
  min-width: 0;
}

.details__content {
  padding-bottom: 1.5rem;
}

.details__title {
  margin: 0;
  color: #111111;
  font-size: clamp(2rem, 3vw, 3rem);
  line-height: 1.12;
}

.meta-info {
  margin: 1rem 0 1.6rem;
  padding: 0.95rem 0;
  border-top: 1px solid rgba(17, 17, 17, 0.08);
  border-bottom: 1px solid rgba(17, 17, 17, 0.08);
}

.meta-info ul {
  display: flex;
  flex-wrap: wrap;
  gap: 1.25rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.meta-info li {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  color: #6c6257;
  font-size: 0.92rem;
}

.meta-info__icon {
  color: #7c5c3b;
  font-size: 0.8rem;
}

.details__body {
  color: #5b5248;
  line-height: 1.9;
}

.details__body:deep(img) {
  display: block;
  max-width: 100%;
  height: auto;
  margin: 1.75rem 0;
}

.details__body:deep(blockquote) {
  margin: 2rem 0;
  padding: 2.25rem;
  background: #111111;
  color: #fff;
}

.details__body:deep(blockquote p),
.details__body:deep(blockquote cite) {
  color: inherit;
}

.details__body:deep(h2),
.details__body:deep(h3),
.details__body:deep(h4) {
  color: #111111;
}

.post__tag {
  margin-top: 2rem;
  padding-top: 1rem;
}

.post__tag h5 {
  margin: 0 0 1rem;
  color: #111111;
  font-size: 1.55rem;
}

.post__tag ul {
  display: flex;
  flex-wrap: wrap;
  gap: 0.55rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.post__tag a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2rem;
  border: 1px solid rgba(17, 17, 17, 0.12);
  padding: 0.35rem 0.75rem;
  color: #6c6257;
  font-size: 0.76rem;
  letter-spacing: 0.04em;
  text-decoration: none;
  text-transform: uppercase;
}

.posts_navigation {
  padding-top: 2rem;
  padding-bottom: 4rem;
}

.posts_navigation--empty {
  padding-bottom: 2rem;
}

.posts_navigation__row {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
  align-items: center;
  gap: 1.5rem;
}

.prev-link span,
.next-link span {
  display: block;
  margin-bottom: 0.45rem;
  color: #9d9387;
  font-size: 0.72rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.prev-link h4,
.next-link h4 {
  margin: 0;
  color: #111111;
  font-size: 1.45rem;
  line-height: 1.2;
}

.prev-link h4 a,
.next-link h4 a {
  color: inherit;
  text-decoration: none;
}

.next-link {
  text-align: right;
}

.blog-filter,
.blog-filter--placeholder {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 4rem;
  height: 4rem;
  border-radius: 999px;
  background: #e2fbfb;
}

.blog-filter__glyph {
  display: grid;
  grid-template-columns: repeat(2, 0.35rem);
  gap: 0.25rem;
}

.blog-filter__glyph span {
  width: 0.35rem;
  height: 0.35rem;
  border-radius: 999px;
  background: #58d7dc;
}

.avatar__wrap {
  border: 1px solid rgba(17, 17, 17, 0.08);
  background: #fbfbfb;
  padding: 2.5rem 2rem;
}

.avatar-img {
  display: flex;
  justify-content: center;
  margin-bottom: 1rem;
}

.author-blog-avatar {
  width: 120px;
  height: 120px;
  object-fit: cover;
}

.avatar__info h5 {
  margin: 0;
  color: #111111;
  font-size: 2rem;
  line-height: 1.15;
}

.avatar__info-social {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.avatar__info-social a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1.6rem;
  height: 1.6rem;
  color: #7c5c3b;
  text-decoration: none;
}

.avatar__wrap-content {
  max-width: 34rem;
  margin: 1rem auto 0;
  color: #6c6257;
  line-height: 1.85;
}

.avatar__wrap-content:deep(p) {
  margin: 0;
}

@media (max-width: 767px) {
  .posts_navigation__row {
    grid-template-columns: 1fr;
    justify-items: start;
  }

  .next-link {
    text-align: left;
  }

  .blog-filter,
  .blog-filter--placeholder {
    order: -1;
  }
}
</style>
