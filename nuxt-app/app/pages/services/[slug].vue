<script setup lang="ts">
import { usePage } from "~/composables/usePage";
import { resolveCmsLocale } from "~~/shared/cms-routing";
import {
  layoutUsesBreadcrumbs,
  layoutUsesFooter,
  resolvePageLayoutName,
} from "~/utils/page-template";

const route = useRoute();
const { locale } = useI18n();

const slug = computed(() => `services/${String(route.params.slug || "")}`);
const activeLocale = computed(() => resolveCmsLocale(locale.value));

const { data, error } = await useAsyncData(
  () => `page-${slug.value}-${activeLocale.value}`,
  () => usePage(slug.value, activeLocale.value),
  { watch: [slug, activeLocale] },
);

if (error.value) {
  throw error.value;
}

const page = computed(() => data.value?.page);
const blocks = computed(() => data.value?.blocks || []);
const layoutName = computed(() => resolvePageLayoutName(page.value?.template));
const hasBlocks = computed(() => blocks.value.length > 0);
const showBreadcrumbs = computed(() =>
  Boolean(page.value && layoutUsesBreadcrumbs(layoutName.value) && page.value.breadcrumb?.enabled !== false),
);
const showPlainPageHero = computed(() => Boolean(page.value && !showBreadcrumbs.value && !hasBlocks.value));
const showFooter = computed(() => Boolean(page.value && layoutUsesFooter(layoutName.value)));

usePageSeo(page);

definePageMeta({
  layout: "cms-page",
});
</script>

<template>
  <main>
    <CmsBreadcrumbs v-if="page && showBreadcrumbs" :page="page" />

    <section v-if="showPlainPageHero" class="page-shell">
      <header class="page-hero py-10">
        <div class="container page-hero__inner">
          <h1 class="page-hero__title">
            {{ page.name }}
          </h1>
          <p v-if="page.description" class="page-hero__description">
            {{ page.description }}
          </p>
        </div>
      </header>
    </section>

    <section
      v-if="page && hasBlocks"
      class="cms-content"
      :class="{ 'cms-content--with-breadcrumbs': showBreadcrumbs }"
    >
      <ShortcodeBlockRenderer
        v-for="(block, index) in blocks"
        :key="`${index}-${block.type}-${block.name || 'block'}`"
        :block="block"
      />
    </section>

    <section v-else-if="!page" class="py-20 text-center">
      <div class="container mx-auto px-4">
        <p>Page not found</p>
      </div>
    </section>

    <CmsFooter v-if="showFooter" />
  </main>
</template>

<style scoped>
.page-shell {
  background: linear-gradient(180deg, #fffdf8 0%, #f8f1e7 100%);
}

.page-hero {
  padding-top: clamp(3.5rem, 8vw, 6rem);
  padding-bottom: clamp(2rem, 5vw, 3.25rem);
}

.page-hero__inner {
  max-width: 56rem;
}

.page-hero__title {
  margin: 0;
  color: #241913;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.6rem, 5vw, 4.4rem);
  font-weight: 600;
  line-height: 0.98;
}

.page-hero__description {
  max-width: 42rem;
  margin: 1rem 0 0;
  color: rgba(61, 45, 35, 0.78);
  font-size: 1rem;
  line-height: 1.8;
}

.cms-content {
  min-width: 0;
}

.cms-content--with-breadcrumbs {
  padding-top: 0;
}
</style>
