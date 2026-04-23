<script setup lang="ts">
import { usePage } from "~/features/cms/data/usePage";
import { resolvePageLayoutName } from "~/features/cms/ui/page-template";
import { CMS_HOMEPAGE_TOKEN } from "~~/shared/cms/constants";
import { resolveCmsLocale } from "~~/shared/cms/locale";

const route = useRoute();
const { locale } = useI18n();

const activeLocale = computed(() => resolveCmsLocale(locale.value));
const pageIdentifier = computed(() => {
  const slug = route.params.slug;

  if (typeof slug === "string" && slug.trim()) {
    return slug;
  }

  return CMS_HOMEPAGE_TOKEN;
});

const { data: layoutPageData } = await useAsyncData(
  () => `cms-layout-${pageIdentifier.value}-${activeLocale.value}`,
  async () => {
    try {
      return await usePage(pageIdentifier.value, activeLocale.value);
    } catch {
      return null;
    }
  },
  { watch: [pageIdentifier, activeLocale] },
);

const layoutName = computed(() => resolvePageLayoutName(layoutPageData.value?.page?.template));
</script>

<template>
  <CmsPageShell :layout-name="layoutName">
    <slot />
  </CmsPageShell>
</template>
