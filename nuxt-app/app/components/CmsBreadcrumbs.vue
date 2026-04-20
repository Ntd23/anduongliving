<script setup lang="ts">
import type { PageData } from "~/composables/usePage";
import { useThemeOptions } from "~/composables/useThemeOptions";

type BreadcrumbItem = {
  label: string;
  to?: string | null;
};

const props = defineProps<{
  page?: PageData | null;
  title?: string | null;
  crumbs?: BreadcrumbItem[];
  backgroundImageUrl?: string | null;
}>();

const { locale, t } = useI18n();
const localePath = useLocalePath();

const activeLocale = computed(() => locale.value);

const { data: themeOptions } = await useAsyncData(
  () => `breadcrumbs-theme-options-${activeLocale.value}`,
  () => useThemeOptions(activeLocale.value),
  { watch: [activeLocale] },
);

const backgroundImageUrl = computed(
  () =>
    props.backgroundImageUrl
    || props.page?.breadcrumb?.background_image_url
    || themeOptions.value?.breadcrumb_background_image_url
    || "",
);

const sectionStyle = computed(() =>
  backgroundImageUrl.value
    ? { backgroundImage: `linear-gradient(rgba(18, 16, 14, 0.45), rgba(18, 16, 14, 0.58)), url('${backgroundImageUrl.value}')` }
    : undefined,
);

const crumbs = computed(() => [
  ...(props.crumbs?.length
    ? props.crumbs
    : [
        {
          label: t("home"),
          to: localePath("/"),
        },
        {
          label: props.page?.name || props.title || "",
          to: null,
        },
      ]),
]);

const title = computed(() => props.title || props.page?.name || "");
</script>

<template>
  <section class="cms-breadcrumbs" :style="sectionStyle">
    <div class="container cms-breadcrumbs__inner">
      <div class="cms-breadcrumbs__content">
        <h1 class="cms-breadcrumbs__title">
          {{ title }}
        </h1>

        <nav class="cms-breadcrumbs__nav" aria-label="Breadcrumb">
          <ol class="cms-breadcrumbs__list">
            <li
              v-for="(crumb, index) in crumbs"
              :key="`${crumb.label}-${index}`"
              class="cms-breadcrumbs__item"
              :class="{ 'cms-breadcrumbs__item--active': !crumb.to }"
            >
              <NuxtLink v-if="crumb.to" :to="crumb.to" class="cms-breadcrumbs__link">
                {{ crumb.label }}
              </NuxtLink>
              <span v-else>{{ crumb.label }}</span>
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
</template>

<style scoped>
.cms-breadcrumbs {
  display: flex;
  align-items: center;
  min-height: clamp(15rem, 25vw, 21rem);
  background:
    linear-gradient(180deg, rgba(18, 16, 14, 0.16), rgba(18, 16, 14, 0.32)),
    linear-gradient(135deg, #534233 0%, #2f241d 100%);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  color: #fff8ea;
}

.cms-breadcrumbs__inner {
  width: 100%;
}

.cms-breadcrumbs__content {
  padding: 3rem 0;
  text-align: center;
}

.cms-breadcrumbs__title {
  margin: 0;
  color: #fff7e5;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.4rem, 5vw, 4.1rem);
  font-weight: 600;
  line-height: 1.06;
}

.cms-breadcrumbs__nav {
  margin-top: 1rem;
}

.cms-breadcrumbs__list {
  display: inline-flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.8rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.cms-breadcrumbs__item {
  display: inline-flex;
  align-items: center;
  gap: 0.8rem;
  color: rgba(255, 247, 229, 0.82);
  font-size: 0.9rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.cms-breadcrumbs__item + .cms-breadcrumbs__item::before {
  content: "/";
  color: rgba(255, 247, 229, 0.54);
}

.cms-breadcrumbs__item--active {
  color: #fff7e5;
}

.cms-breadcrumbs__link {
  color: inherit;
  text-decoration: none;
  transition: color 0.2s ease;
}

.cms-breadcrumbs__link:hover {
  color: #d8b56b;
}

@media (max-width: 991px) {
  .cms-breadcrumbs__content {
    padding: 2.5rem 0;
  }
}
</style>
