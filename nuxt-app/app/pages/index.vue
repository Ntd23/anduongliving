<script setup lang="ts">
import {
  CMS_HOMEPAGE_TOKEN,
  resolveCmsLocale,
  usePage,
} from "~/composables/usePage";

const { locale } = useI18n();

const activeLocale = computed(() => resolveCmsLocale(locale.value));

const { data, error } = await useAsyncData(
  () => `page-homepage-${activeLocale.value}`,
  () => usePage(CMS_HOMEPAGE_TOKEN, activeLocale.value),
  { watch: [activeLocale] },
);

const homepageError = computed(() => {
  const statusCode = error.value?.statusCode || error.value?.status;
  const statusMessage = error.value?.statusMessage || error.value?.message;

  if (statusCode === 404 || statusCode === 422) {
    return statusMessage || "Homepage is not configured yet.";
  }

  return null;
});

if (error.value && !homepageError.value) {
  throw error.value;
}

const page = computed(() => data.value?.page);
const blocks = computed(() => data.value?.blocks || []);
const layoutName = computed(() => {
  switch (page.value?.template) {
    case "side-menu":
      return "cms-side-menu";
    case "full-menu":
      return "cms-full-menu";
    default:
      return "default";
  }
});
usePageSeo(page);
</script>

<template>
  <NuxtLayout :name="layoutName">
    <main>
      <section v-if="page" class="page-shell">
        <header class="page-hero py-10">
          <div class="container mx-auto px-4">
            <h1 class="text-3xl font-semibold">
              {{ page.name }}
            </h1>
            <p v-if="page.description" class="mt-2 text-gray-600">
              {{ page.description }}
            </p>
          </div>
        </header>

        <div class="cms-content">
          <ShortcodeBlockRenderer
            v-for="(block, index) in blocks"
            :key="`${index}-${block.type}-${block.name || 'block'}`"
            :block="block"
          />
        </div>
      </section>

      <section v-else class="py-20 text-center">
        <div class="container mx-auto px-4">
          <h1 class="text-3xl font-semibold">Homepage not configured</h1>
          <p class="mt-3 text-gray-600">
            {{ homepageError || "Please choose a homepage in admin first." }}
          </p>
        </div>
      </section>
    </main>
  </NuxtLayout>
</template>
