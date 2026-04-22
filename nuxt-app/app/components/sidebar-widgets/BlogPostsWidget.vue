<script setup lang="ts">
import SidebarWidgetCard from "~/components/sidebar-widgets/SidebarWidgetCard.vue";
import {
  normalizeBlogPostCollection,
  useBlogPosts,
  type BlogPostSummary,
} from "~/composables/useBlog";
import { cmsAppRoutes } from "~~/shared/cms-routing";
import { formatCmsDate } from "~/utils/locale-format";
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";
import { useResolvedCmsAsset } from "~/composables/useResolvedCmsAsset";

const props = defineProps<{
  widget: SidebarWidgetManifest;
}>();

const { locale } = useI18n();
const localePath = useLocalePath();
const resolveAsset = useResolvedCmsAsset();

const limit = computed(() => Math.max(1, Number.parseInt(String(props.widget.data?.limit || 5), 10) || 5));
const type = computed<"recent" | "popular">(() =>
  String(props.widget.data?.type || "recent").toLowerCase() === "popular" ? "popular" : "recent",
);
const title = computed(() => String(props.widget.data?.title || "Recent Posts"));
const description = computed(() => {
  const value = props.widget.data?.description;
  return typeof value === "string" && value.trim() ? value : null;
});

const { data } = await useAsyncData(
  () => `sidebar-posts-${props.widget.id}-${locale.value}`,
  () => useBlogPosts({ locale: locale.value, limit: limit.value, type: type.value }),
  { watch: [locale, limit, type] },
);

const posts = computed<BlogPostSummary[]>(() => normalizeBlogPostCollection(data.value).items);
const formatDate = (value?: string | null) => {
  if (!value) {
    return "";
  }

  return formatCmsDate(value, locale.value, { monthStyle: "short" }) || "";
};
</script>

<template>
  <SidebarWidgetCard :title="title" :description="description">
    <ul v-if="posts.length" class="sidebar-post-list">
      <li v-for="post in posts" :key="post.id" class="sidebar-post-list__item">
        <NuxtLink :to="localePath(cmsAppRoutes.blog.post(post.slug))" class="sidebar-post-list__image-link">
          <img
            v-if="resolveAsset(post.image)"
            :src="resolveAsset(post.image)!"
            :alt="post.name"
            class="sidebar-post-list__image"
          >
          <div v-else class="sidebar-post-list__image-placeholder" aria-hidden="true" />
        </NuxtLink>

        <div class="sidebar-post-list__copy">
          <p v-if="post.created_at" class="sidebar-post-list__date">
            {{ formatDate(post.created_at) }}
          </p>
          <NuxtLink :to="localePath(cmsAppRoutes.blog.post(post.slug))" class="sidebar-post-list__link">
            {{ post.name }}
          </NuxtLink>
        </div>
      </li>
    </ul>
    <p v-else class="sidebar-widget-empty">
      No posts yet.
    </p>
  </SidebarWidgetCard>
</template>

<style scoped>
.sidebar-post-list {
  display: grid;
  gap: 1rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.sidebar-post-list__item {
  display: grid;
  grid-template-columns: 5.2rem minmax(0, 1fr);
  gap: 0.9rem;
  align-items: center;
}

.sidebar-post-list__image-link {
  display: block;
}

.sidebar-post-list__image,
.sidebar-post-list__image-placeholder {
  width: 100%;
  aspect-ratio: 1 / 1;
  border-radius: 1.25rem;
}

.sidebar-post-list__image {
  object-fit: cover;
}

.sidebar-post-list__image-placeholder {
  background:
    linear-gradient(135deg, rgba(167, 122, 84, 0.12), rgba(107, 113, 83, 0.12)),
    #efe5d8;
}

.sidebar-post-list__copy {
  min-width: 0;
}

.sidebar-post-list__date {
  margin: 0 0 0.35rem;
  color: var(--retreat-clay);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
}

.sidebar-post-list__link {
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: 1.35rem;
  font-weight: 600;
  line-height: 0.98;
  text-decoration: none;
}

.sidebar-post-list__link:hover {
  color: var(--retreat-clay);
}

.sidebar-widget-empty {
  margin: 0;
  color: var(--retreat-ink-soft);
}
</style>
