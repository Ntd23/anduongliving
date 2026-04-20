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
    ? { backgroundImage: `linear-gradient(rgba(18, 12, 10, 0.28), rgba(18, 12, 10, 0.52)), url('${backgroundImageUrl.value}')` }
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
        <p class="cms-breadcrumbs__eyebrow">
          Retreat Journal
        </p>

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
  align-items: flex-end;
  min-height: clamp(18rem, 32vw, 27rem);
  background:
    linear-gradient(180deg, rgba(18, 12, 10, 0.18), rgba(18, 12, 10, 0.34)),
    linear-gradient(135deg, #4f3f33 0%, #231813 100%);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  color: #fff9f1;
}

.cms-breadcrumbs__inner {
  width: 100%;
}

.cms-breadcrumbs__content {
  max-width: 44rem;
  padding: 5.25rem 0 3.1rem;
}

.cms-breadcrumbs__eyebrow {
  margin: 0 0 1rem;
  color: rgba(255, 249, 241, 0.76);
  font-size: 0.76rem;
  font-weight: 700;
  letter-spacing: 0.22em;
  text-transform: uppercase;
}

.cms-breadcrumbs__title {
  margin: 0;
  color: #fffdf8;
  font-family: var(--font-display);
  font-size: clamp(3rem, 6vw, 5.8rem);
  font-weight: 600;
  line-height: 0.92;
  letter-spacing: -0.025em;
}

.cms-breadcrumbs__nav {
  margin-top: 1.25rem;
}

.cms-breadcrumbs__list {
  display: inline-flex;
  flex-wrap: wrap;
  gap: 0.7rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.cms-breadcrumbs__item {
  display: inline-flex;
  align-items: center;
  gap: 0.7rem;
  color: rgba(255, 249, 241, 0.7);
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.cms-breadcrumbs__item + .cms-breadcrumbs__item::before {
  content: "/";
  color: rgba(255, 249, 241, 0.4);
}

.cms-breadcrumbs__item--active {
  color: #fff9f1;
}

.cms-breadcrumbs__link {
  color: inherit;
  text-decoration: none;
}

.cms-breadcrumbs__link:hover {
  color: #d6c09b;
}

@media (max-width: 991px) {
  .cms-breadcrumbs {
    min-height: 15rem;
  }

  .cms-breadcrumbs__content {
    padding: 4.25rem 0 2.35rem;
  }

  .cms-breadcrumbs__title {
    line-height: 0.98;
  }
}
</style>
