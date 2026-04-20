<script setup lang="ts">
import type { BlogPostSummary } from "~/composables/useBlog";
import { useBlogPost } from "~/composables/useBlog";
import {
  cmsAppRoutes,
  sanitizeCmsHtmlContent,
} from "~~/shared/cms-routing";
import {
  formatCmsDate,
  formatCmsInteger,
  normalizeUiLocale,
} from "~/utils/locale-format";
import { useBlogSeo } from "~/composables/useBlogSeo";

const route = useRoute();
const { locale } = useI18n();
const localePath = useLocalePath();
const runtimeConfig = useRuntimeConfig();

const slug = computed(() => String(route.params.slug || ""));
const asyncKey = computed(() => `blog-post-${slug.value}-${locale.value}`);

const { data, error } = await useAsyncData(
  asyncKey,
  () => useBlogPost(slug.value, locale.value),
  { watch: [slug, locale] },
);

if (error.value) {
  throw error.value;
}

definePageMeta({
  layout: "cms-blog-sidebar",
  key: (currentRoute) => currentRoute.fullPath,
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

const localeCode = computed(() => normalizeUiLocale(locale.value));
const formatBlogDate = (input?: string | null, monthStyle?: "short" | "long") =>
  formatCmsDate(input, localeCode.value, { monthStyle });

const formattedDate = computed(() => formatBlogDate(post.value?.created_at, "long"));

const viewCount = computed(() => {
  const value = Number(post.value?.views || 0);

  if (!Number.isFinite(value)) {
    return "0";
  }

  return formatCmsInteger(value, localeCode.value);
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

const relatedPosts = computed(() => post.value?.related_posts || []);
const safeContent = computed(() =>
  sanitizeCmsHtmlContent(post.value?.content, runtimeConfig.public.siteUrl),
);
const safeAuthorBio = computed(() =>
  sanitizeCmsHtmlContent(post.value?.author?.bio, runtimeConfig.public.siteUrl),
);

useBlogSeo({
  title: computed(() => post.value?.name || "Blog Post"),
  description: computed(() => post.value?.description || ""),
  image: computed(() => post.value?.image || undefined),
  type: computed(() => "article"),
});
</script>

<template>
  <CmsBreadcrumbs
    v-if="post"
    :title="post.name"
    :crumbs="breadcrumbs"
  />

  <main v-if="post" class="blog-post-detail">
    <article class="blog-post-detail__article">
      <header class="blog-post-detail__hero cms-quiet-surface">
        <p class="cms-shell-eyebrow blog-post-detail__eyebrow">
          {{ firstCategory?.name || "Journal" }}
        </p>

        <h1 class="blog-post-detail__title">
          {{ post.name }}
        </h1>

        <p v-if="post.description" class="blog-post-detail__lede">
          {{ post.description }}
        </p>

        <ul class="blog-post-detail__meta">
          <li class="blog-post-detail__meta-item">
            <Icon name="ph:eye-bold" />
            <span>{{ viewCount }} views</span>
          </li>
          <li v-if="formattedDate" class="blog-post-detail__meta-item">
            <Icon name="ph:calendar-blank-bold" />
            <span>{{ formattedDate }}</span>
          </li>
        </ul>
      </header>

      <div class="blog-post-detail__body-shell">
        <div class="ck-content blog-post-detail__body" v-html="safeContent" />
      </div>

      <section v-if="post.tags?.length" class="blog-post-detail__tags cms-quiet-surface">
        <div class="blog-post-detail__section-heading">
          <p class="cms-shell-eyebrow blog-post-detail__section-eyebrow">
            Filed Under
          </p>
          <h2 class="blog-post-detail__section-title">
            Related Tags
          </h2>
        </div>

        <ul class="blog-post-detail__tag-list">
          <li v-for="tag in post.tags" :key="tag.id">
            <NuxtLink :to="localePath(cmsAppRoutes.blog.tag(tag.slug))" class="blog-post-detail__tag-link">
              {{ tag.name }}
            </NuxtLink>
          </li>
        </ul>
      </section>

      <section v-if="relatedPosts.length" class="blog-post-detail__related">
        <div class="blog-post-detail__section-heading">
          <p class="cms-shell-eyebrow blog-post-detail__section-eyebrow">
            Continue Reading
          </p>
          <h2 class="blog-post-detail__section-title">
            Related Stories
          </h2>
        </div>

        <div class="blog-post-detail__related-grid">
          <article
            v-for="entry in relatedPosts"
            :key="entry.id"
            class="blog-post-detail__related-card cms-quiet-surface"
          >
            <NuxtLink :to="resolvePostLink(entry)" class="blog-post-detail__related-media">
              <img
                v-if="entry.image"
                :src="entry.image"
                :alt="entry.name"
                class="blog-post-detail__related-image"
              >
              <div v-else class="blog-post-detail__related-image blog-post-detail__related-image--placeholder" />
            </NuxtLink>

            <div class="blog-post-detail__related-body">
              <p v-if="entry.created_at" class="blog-post-detail__related-date">
                {{ formatBlogDate(entry.created_at, "short") }}
              </p>
              <h3 class="blog-post-detail__related-name">
                <NuxtLink :to="resolvePostLink(entry)">
                  {{ entry.name }}
                </NuxtLink>
              </h3>
            </div>
          </article>
        </div>
      </section>

      <nav
        class="blog-post-detail__navigation"
        :class="{ 'blog-post-detail__navigation--empty': !post.navigation?.previous && !post.navigation?.next }"
      >
        <div class="blog-post-detail__navigation-grid">
          <div v-if="post.navigation?.previous" class="blog-post-detail__navigation-card cms-quiet-surface">
            <span class="blog-post-detail__navigation-label">Previous Story</span>
            <h3 class="blog-post-detail__navigation-title">
              <NuxtLink :to="resolvePostLink(post.navigation.previous)">
                {{ post.navigation.previous?.name }}
              </NuxtLink>
            </h3>
          </div>
          <div v-else class="blog-post-detail__navigation-card blog-post-detail__navigation-card--placeholder" />

          <NuxtLink
            v-if="firstCategory"
            :to="localePath(cmsAppRoutes.blog.category(firstCategory.slug))"
            class="blog-post-detail__navigation-filter"
            :aria-label="firstCategory.name"
          >
            <span class="blog-post-detail__navigation-filter-grid">
              <span />
              <span />
              <span />
              <span />
            </span>
          </NuxtLink>
          <div v-else class="blog-post-detail__navigation-filter blog-post-detail__navigation-filter--placeholder" aria-hidden="true" />

          <div v-if="post.navigation?.next" class="blog-post-detail__navigation-card cms-quiet-surface blog-post-detail__navigation-card--next">
            <span class="blog-post-detail__navigation-label">Next Story</span>
            <h3 class="blog-post-detail__navigation-title">
              <NuxtLink :to="resolvePostLink(post.navigation.next)">
                {{ post.navigation.next?.name }}
              </NuxtLink>
            </h3>
          </div>
          <div v-else class="blog-post-detail__navigation-card blog-post-detail__navigation-card--placeholder" />
        </div>
      </nav>

      <section v-if="post.author" class="blog-post-detail__author cms-quiet-surface">
        <div class="blog-post-detail__author-avatar-wrap">
          <img
            v-if="post.author.avatar_url"
            class="blog-post-detail__author-avatar"
            :src="post.author.avatar_url"
            :alt="post.author.name"
          >
          <div v-else class="blog-post-detail__author-avatar blog-post-detail__author-avatar--placeholder" />
        </div>

        <div class="blog-post-detail__author-copy">
          <p class="cms-shell-eyebrow blog-post-detail__author-eyebrow">
            Written By
          </p>
          <h2 class="blog-post-detail__author-name">
            {{ post.author.name }}
          </h2>

          <div v-if="post.author.socials?.length" class="blog-post-detail__author-social">
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

          <div
            v-if="safeAuthorBio"
            class="blog-post-detail__author-bio"
            v-html="safeAuthorBio"
          />
        </div>
      </section>
    </article>
  </main>
</template>

<style scoped>
.blog-post-detail {
  min-width: 0;
}

.blog-post-detail__article {
  min-width: 0;
}

.blog-post-detail__hero {
  padding: clamp(1.8rem, 4vw, 3rem);
  border-radius: var(--retreat-radius-card);
}

.blog-post-detail__eyebrow {
  margin-bottom: 1.1rem;
}

.blog-post-detail__title {
  margin: 0;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: clamp(3rem, 6vw, 5.4rem);
  font-weight: 600;
  line-height: 0.92;
  letter-spacing: -0.03em;
}

.blog-post-detail__lede {
  max-width: 44rem;
  margin: 1.25rem 0 0;
  color: var(--retreat-ink-soft);
  font-size: 1.03rem;
  line-height: 1.95;
}

.blog-post-detail__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.8rem 1.5rem;
  margin: 1.4rem 0 0;
  padding: 0;
  list-style: none;
}

.blog-post-detail__meta-item {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
  color: var(--retreat-ink-soft);
  font-size: 0.88rem;
  line-height: 1.6;
}

.blog-post-detail__meta-item :deep(svg) {
  color: var(--retreat-clay);
  font-size: 1rem;
}

.blog-post-detail__body-shell {
  margin-top: 2.4rem;
}

.blog-post-detail__body {
  color: var(--retreat-ink-soft);
  font-size: 1rem;
  line-height: 1.95;
}

.blog-post-detail__body:deep(> *:first-child) {
  margin-top: 0;
}

.blog-post-detail__body:deep(> *:last-child) {
  margin-bottom: 0;
}

.blog-post-detail__body:deep(h2),
.blog-post-detail__body:deep(h3),
.blog-post-detail__body:deep(h4) {
  margin: 2.7rem 0 1rem;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-weight: 600;
  line-height: 0.98;
  letter-spacing: -0.02em;
}

.blog-post-detail__body:deep(h2) {
  font-size: clamp(2.35rem, 4vw, 3.7rem);
}

.blog-post-detail__body:deep(h3) {
  font-size: clamp(2rem, 3vw, 3rem);
}

.blog-post-detail__body:deep(h4) {
  font-size: clamp(1.7rem, 2.3vw, 2.35rem);
}

.blog-post-detail__body:deep(p),
.blog-post-detail__body:deep(ul),
.blog-post-detail__body:deep(ol) {
  margin: 1rem 0;
}

.blog-post-detail__body:deep(img) {
  display: block;
  max-width: 100%;
  height: auto;
  margin: 2rem 0;
  border-radius: 1.6rem;
}

.blog-post-detail__body:deep(figure) {
  margin: 2rem 0;
}

.blog-post-detail__body:deep(blockquote) {
  margin: 2.6rem 0;
  padding: clamp(1.8rem, 4vw, 2.6rem);
  border-left: 3px solid rgba(167, 122, 84, 0.45);
  border-radius: 1.6rem;
  background: linear-gradient(180deg, rgba(255, 251, 246, 0.98), rgba(243, 234, 222, 0.92));
  color: var(--retreat-ink);
  box-shadow: 0 22px 50px rgba(35, 22, 13, 0.06);
}

.blog-post-detail__body:deep(blockquote p) {
  margin: 0;
  font-family: var(--font-display);
  font-size: clamp(1.7rem, 3vw, 2.6rem);
  line-height: 1.1;
}

.blog-post-detail__body:deep(blockquote cite) {
  display: inline-block;
  margin-top: 0.9rem;
  color: var(--retreat-ink-soft);
  font-size: 0.82rem;
  font-style: normal;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.blog-post-detail__section-heading {
  margin-bottom: 1.35rem;
}

.blog-post-detail__section-eyebrow {
  margin-bottom: 0.8rem;
}

.blog-post-detail__section-title {
  margin: 0;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 600;
  line-height: 0.98;
}

.blog-post-detail__tags {
  margin-top: 2.6rem;
  padding: 1.8rem;
  border-radius: var(--retreat-radius-card);
}

.blog-post-detail__tag-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.7rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.blog-post-detail__tag-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2.4rem;
  padding: 0.45rem 1rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.85);
  color: var(--retreat-ink);
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
}

.blog-post-detail__tag-link:hover {
  background: rgba(167, 122, 84, 0.14);
  color: var(--retreat-clay);
}

.blog-post-detail__related {
  margin-top: 3rem;
}

.blog-post-detail__related-grid {
  display: grid;
  gap: 1.25rem;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
}

.blog-post-detail__related-card {
  overflow: hidden;
  border-radius: var(--retreat-radius-card);
}

.blog-post-detail__related-media {
  display: block;
}

.blog-post-detail__related-image {
  display: block;
  width: 100%;
  aspect-ratio: 16 / 11;
  object-fit: cover;
}

.blog-post-detail__related-image--placeholder {
  background:
    linear-gradient(135deg, rgba(167, 122, 84, 0.12), rgba(107, 113, 83, 0.12)),
    #efe5d8;
}

.blog-post-detail__related-body {
  padding: 1.2rem 1.2rem 1.35rem;
}

.blog-post-detail__related-date {
  margin: 0 0 0.45rem;
  color: var(--retreat-clay);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
}

.blog-post-detail__related-name {
  margin: 0;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: 1.55rem;
  font-weight: 600;
  line-height: 1;
}

.blog-post-detail__related-name a {
  color: inherit;
  text-decoration: none;
}

.blog-post-detail__related-name a:hover {
  color: var(--retreat-clay);
}

.blog-post-detail__navigation {
  margin-top: 3rem;
}

.blog-post-detail__navigation--empty {
  display: none;
}

.blog-post-detail__navigation-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
  align-items: center;
  gap: 1.2rem;
}

.blog-post-detail__navigation-card {
  min-height: 100%;
  padding: 1.4rem 1.5rem;
  border-radius: var(--retreat-radius-soft);
}

.blog-post-detail__navigation-card--placeholder {
  visibility: hidden;
}

.blog-post-detail__navigation-card--next {
  text-align: right;
}

.blog-post-detail__navigation-label {
  display: inline-block;
  margin-bottom: 0.65rem;
  color: var(--retreat-clay);
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
}

.blog-post-detail__navigation-title {
  margin: 0;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: 1.65rem;
  font-weight: 600;
  line-height: 1;
}

.blog-post-detail__navigation-title a {
  color: inherit;
  text-decoration: none;
}

.blog-post-detail__navigation-title a:hover {
  color: var(--retreat-clay);
}

.blog-post-detail__navigation-filter,
.blog-post-detail__navigation-filter--placeholder {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 4.5rem;
  height: 4.5rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.72);
  box-shadow: 0 20px 40px rgba(35, 22, 13, 0.07);
}

.blog-post-detail__navigation-filter-grid {
  display: grid;
  grid-template-columns: repeat(2, 0.38rem);
  gap: 0.26rem;
}

.blog-post-detail__navigation-filter-grid span {
  width: 0.38rem;
  height: 0.38rem;
  border-radius: 999px;
  background: var(--retreat-clay);
}

.blog-post-detail__author {
  display: grid;
  gap: 1.4rem;
  margin-top: 3rem;
  padding: clamp(1.7rem, 4vw, 2.5rem);
  border-radius: var(--retreat-radius-card);
}

.blog-post-detail__author-avatar-wrap {
  display: flex;
  justify-content: center;
}

.blog-post-detail__author-avatar {
  width: 7.5rem;
  height: 7.5rem;
  border-radius: 999px;
  object-fit: cover;
  box-shadow: 0 20px 40px rgba(35, 22, 13, 0.12);
}

.blog-post-detail__author-avatar--placeholder {
  background:
    linear-gradient(135deg, rgba(167, 122, 84, 0.12), rgba(107, 113, 83, 0.12)),
    #efe5d8;
}

.blog-post-detail__author-copy {
  text-align: center;
}

.blog-post-detail__author-eyebrow {
  justify-content: center;
  margin-bottom: 0.8rem;
}

.blog-post-detail__author-name {
  margin: 0;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: clamp(2.2rem, 5vw, 3.4rem);
  font-weight: 600;
  line-height: 0.96;
}

.blog-post-detail__author-social {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.65rem;
  margin-top: 1rem;
}

.blog-post-detail__author-social a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.8);
  color: var(--retreat-clay);
  text-decoration: none;
}

.blog-post-detail__author-social a:hover {
  color: var(--retreat-olive);
  transform: translateY(-1px);
}

.blog-post-detail__author-bio {
  max-width: 42rem;
  margin: 1.15rem auto 0;
  color: var(--retreat-ink-soft);
  line-height: 1.9;
}

.blog-post-detail__author-bio:deep(p) {
  margin: 0;
}

@media (max-width: 767px) {
  .blog-post-detail__navigation-grid {
    grid-template-columns: 1fr;
  }

  .blog-post-detail__navigation-card--next {
    text-align: left;
  }

  .blog-post-detail__navigation-filter,
  .blog-post-detail__navigation-filter--placeholder {
    order: -1;
    margin-inline: auto;
  }
}

@media (min-width: 992px) {
  .blog-post-detail__author {
    grid-template-columns: auto minmax(0, 1fr);
    align-items: center;
  }

  .blog-post-detail__author-copy {
    text-align: left;
  }

  .blog-post-detail__author-eyebrow {
    justify-content: flex-start;
  }

  .blog-post-detail__author-social {
    justify-content: flex-start;
  }

  .blog-post-detail__author-bio {
    margin-inline: 0;
  }
}
</style>
