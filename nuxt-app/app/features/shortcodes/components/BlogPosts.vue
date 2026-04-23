<script setup lang="ts">
import { parseBlogPostsBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { useResolvedCmsAsset } from "~/features/cms/data/useResolvedCmsAsset";
import { normalizeCmsInternalPath } from "~~/shared/cms-routing";

const props = defineProps<{ block: ShortcodeBlock }>();
const section = computed(() => parseBlogPostsBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();
const resolveAsset = useResolvedCmsAsset();

const resolvePostLink = (href?: string | null) => {
  const resolved = resolveLink(href);

  if (!resolved) {
    return null;
  }

  if (!resolved.isInternal) {
    return resolved;
  }

  return resolveLink(
    normalizeCmsInternalPath(resolved.href, {
      referenceType: "Post",
    }),
  );
};
</script>

<template>
  <section v-if="section.items.length" class="shortcode-blog-posts-native">
    <div class="container blog-posts-shell">
      <div class="blog-posts-grid">
        <article v-for="item in section.items" :key="item.title" class="blog-posts-card">
          <NuxtLink
            v-if="item.image?.src && resolvePostLink(item.href)?.isInternal"
            :to="resolvePostLink(item.href)!.href"
            class="blog-posts-card__media-link"
            :aria-label="item.title"
          >
            <img
              :src="resolveAsset(item.image.src) || item.image.src"
              :alt="item.image.alt || item.title"
              class="blog-posts-card__image"
            >
          </NuxtLink>
          <a
            v-else-if="item.image?.src && resolvePostLink(item.href)"
            :href="resolvePostLink(item.href)!.href"
            class="blog-posts-card__media-link"
            :aria-label="item.title"
          >
            <img
              :src="resolveAsset(item.image.src) || item.image.src"
              :alt="item.image.alt || item.title"
              class="blog-posts-card__image"
            >
          </a>
          <img
            v-else-if="item.image?.src"
            :src="resolveAsset(item.image.src) || item.image.src"
            :alt="item.image.alt || item.title"
            class="blog-posts-card__image"
          >
          <div class="blog-posts-card__body">
            <p v-if="item.meta" class="blog-posts-card__meta">{{ item.meta }}</p>
            <h3 class="blog-posts-card__title">{{ item.title }}</h3>
            <p v-if="item.description" class="blog-posts-card__description">{{ item.description }}</p>

            <NuxtLink
              v-if="resolvePostLink(item.href)?.isInternal"
              :to="resolvePostLink(item.href)!.href"
              class="blog-posts-card__link"
            >
              {{ item.actionLabel || "Read more" }}
            </NuxtLink>
            <a v-else-if="resolvePostLink(item.href)" :href="resolvePostLink(item.href)!.href" class="blog-posts-card__link">
              {{ item.actionLabel || "Read more" }}
            </a>
          </div>
        </article>
      </div>

      <div v-if="section.paginationHtml" class="blog-posts-pagination" v-html="section.paginationHtml" />
    </div>
  </section>

  <section v-else class="shortcode-blog-posts-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-blog-posts-native {
  padding: clamp(4rem, 8vw, 7rem) 0;
  background: linear-gradient(180deg, #faf5ee, #f2e9de);
}

.blog-posts-shell {
  display: grid;
  gap: 2rem;
}

.blog-posts-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.blog-posts-card {
  overflow: hidden;
  border-radius: 1.5rem;
  background: rgba(255, 252, 246, 0.88);
  box-shadow: 0 18px 44px rgba(48, 35, 27, 0.08);
}

.blog-posts-card__media-link {
  display: block;
  overflow: hidden;
}

.blog-posts-card__image {
  width: 100%;
  aspect-ratio: 16 / 10;
  display: block;
  object-fit: cover;
  transition: transform 0.45s ease;
}

.blog-posts-card__media-link:hover .blog-posts-card__image {
  transform: scale(1.03);
}

.blog-posts-card__body {
  padding: 1.2rem;
}

.blog-posts-card__meta {
  margin: 0;
  color: #8a6e48;
  font-size: 0.78rem;
}

.blog-posts-card__title {
  margin: 0.55rem 0 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 2rem;
  line-height: 1.02;
}

.blog-posts-card__description {
  margin: 0.75rem 0 0;
  color: rgba(47, 36, 29, 0.74);
  line-height: 1.8;
}

.blog-posts-card__link {
  display: inline-flex;
  margin-top: 0.85rem;
  color: #6c744f;
  text-decoration: none;
  font-weight: 600;
}

.blog-posts-pagination:deep(*) {
  max-width: 100%;
}

.blog-posts-pagination {
  display: flex;
  justify-content: center;
  padding-top: 0.5rem;
}

.blog-posts-pagination:deep(nav) {
  width: 100%;
}

.blog-posts-pagination:deep(.d-none) {
  display: flex !important;
  justify-content: center;
}

.blog-posts-pagination:deep(.pagination) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  flex-wrap: wrap;
  margin: 0;
  padding: 0;
}

.blog-posts-pagination:deep(.page-item) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin: 0;
}

.blog-posts-pagination:deep(.page-link) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 2.7rem;
  height: 2.7rem;
  padding: 0 0.85rem;
  border: 1px solid rgba(167, 122, 84, 0.18);
  border-radius: 999px;
  background: rgba(255, 252, 246, 0.88);
  color: #2f241d;
  text-decoration: none;
  font-weight: 600;
  line-height: 1;
  box-shadow: 0 10px 24px rgba(48, 35, 27, 0.06);
}

.blog-posts-pagination:deep(.page-item.active .page-link) {
  border-color: rgba(167, 122, 84, 0.4);
  background: #a77a54;
  color: #fff;
  box-shadow: 0 12px 28px rgba(167, 122, 84, 0.2);
}

.blog-posts-pagination:deep(.page-item.disabled .page-link) {
  opacity: 0.45;
  pointer-events: none;
}

.blog-posts-pagination:deep(.page-link-navigation) {
  font-size: 1.15rem;
}

@media (max-width: 991px) {
  .blog-posts-grid {
    grid-template-columns: 1fr;
  }
}
</style>




