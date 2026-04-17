<script setup lang="ts">
import { resolveCmsLocale, usePage } from "~/composables/usePage";

const route = useRoute();
const { locale } = useI18n();

const slug = computed(() => String(route.params.slug || ""));
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
          <p>Page not found</p>
        </div>
      </section>
    </main>
  </NuxtLayout>
</template>
