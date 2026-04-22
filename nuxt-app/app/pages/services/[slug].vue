<script setup lang="ts">
import { useService } from "~/composables/useService";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { buildAbsoluteUrl, cmsAppRoutes } from "~~/shared/cms-routing";

const route = useRoute();
const { locale, t } = useI18n();
const localePath = useLocalePath();
const config = useRuntimeConfig();

const slug = computed(() => String(route.params.slug || ""));
const activeLocale = computed(() => locale.value);

const { data, error } = await useAsyncData(
  () => `service-${slug.value}-${activeLocale.value}`,
  () => useService(slug.value, activeLocale.value),
  { watch: [slug, activeLocale] },
);

if (error.value) {
  throw error.value;
}

const service = computed(() => data.value || null);
const sanitizedContent = useSanitizedCmsHtml(() => service.value?.content || "");

useSeoMeta({
  title: computed(() => service.value?.seo?.title || service.value?.name || undefined),
  description: computed(() => service.value?.seo?.description || undefined),
  ogTitle: computed(() => service.value?.seo?.title || service.value?.name || undefined),
  ogDescription: computed(() => service.value?.seo?.description || undefined),
});

useHead(() => {
  if (!service.value?.seo?.canonical_path) {
    return {};
  }

  return {
    link: [
      {
        rel: "canonical",
        href: buildAbsoluteUrl(String(config.public.siteUrl || ""), service.value.seo.canonical_path),
      },
    ],
  };
});

definePageMeta({
  layout: "cms-page",
});
</script>

<template>
  <main>
    <CmsBreadcrumbs
      v-if="service"
      :title="service.name"
      :crumbs="[
        { label: t('home'), to: localePath(cmsAppRoutes.home()) },
        { label: service.name, to: null },
      ]"
    />

    <section class="service-detail">
      <div class="container">
        <div v-if="service" class="service-detail__content" v-html="sanitizedContent" />
        <div v-else class="service-detail__empty">
          Service not found
        </div>
      </div>
    </section>

    <CmsFooter />
  </main>
</template>

<style scoped>
.service-detail {
  padding: clamp(3rem, 6vw, 5rem) 0;
}

.service-detail__content:deep(.about-area5) {
  padding: 0;
}

.service-detail__content:deep(.container) {
  width: 100%;
  max-width: none;
  padding: 0;
}

.service-detail__content:deep(.pt-120) {
  padding-top: 0 !important;
}

.service-detail__content:deep(.pb-90) {
  padding-bottom: 0 !important;
}

.service-detail__content:deep(.sidebar-widget) {
  border-radius: 1.5rem;
  background: rgba(255, 255, 255, 0.88);
  box-shadow: 0 24px 60px rgba(66, 47, 35, 0.08);
  padding: 1.5rem;
}

.service-detail__content:deep(.widget-title) {
  color: var(--retreat-ink);
}

.service-detail__content:deep(.services-categories) {
  margin: 0;
  padding: 0;
  list-style: none;
}

.service-detail__content:deep(.services-categories li + li) {
  margin-top: 0.85rem;
}

.service-detail__content:deep(.services-categories a) {
  color: rgba(46, 35, 28, 0.82);
  text-decoration: none;
}

.service-detail__content:deep(.services-categories li.active a) {
  color: var(--retreat-clay);
}

.service-detail__empty {
  padding: 4rem 0;
  text-align: center;
}

@media (max-width: 991px) {
  .service-detail__content:deep(.row) {
    row-gap: 2rem;
  }
}
</style>
