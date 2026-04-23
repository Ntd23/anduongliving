<script setup lang="ts">
import SidebarWidgetCard from "~/features/cms/ui/sidebar-widgets/SidebarWidgetCard.vue";
import {
  extractInternalPath,
  localizeCmsPath,
  normalizeCmsInternalPath,
  normalizePath,
  normalizeSiteUrl,
} from "~~/shared/cms-routing";
import type { SidebarWidgetManifest } from "~/features/cms/ui/sidebar-widgets/registry";

const props = defineProps<{
  widget: SidebarWidgetManifest;
  variant?: "sidebar" | "footer";
}>();

type MenuItem = {
  label: string;
  url: string;
  attributes: string | null;
  isOpenNewTab: boolean;
};

const route = useRoute();
const localePath = useLocalePath();
const { locale, locales } = useI18n();
const runtimeConfig = useRuntimeConfig();

const title = computed(() => String(props.widget.data?.name || "").trim() || null);
const variant = computed(() => props.variant || "sidebar");
const localeCodes = computed(() =>
  (locales.value || [])
    .map((item) => (typeof item === "string" ? item : item.code))
    .filter((item): item is string => Boolean(item)),
);
const normalizedSiteUrl = computed(() => normalizeSiteUrl(runtimeConfig.public.siteUrl));

const items = computed<MenuItem[]>(() => {
  const value = props.widget.data?.items;

  if (!Array.isArray(value)) {
    return [];
  }

  return value
    .map((row) => {
      const map = new Map<string, string>();

      if (Array.isArray(row)) {
        for (const item of row) {
          if (item && typeof item.key === "string") {
            map.set(item.key, String(item.value || ""));
          }
        }
      }

      const label = String(map.get("label") || "").trim();
      const url = String(map.get("url") || "").trim();

      if (!label || !url) {
        return null;
      }

      return {
        label,
        url,
        attributes: String(map.get("attributes") || "").trim() || null,
        isOpenNewTab: String(map.get("is_open_new_tab") || "").trim() === "1",
      } satisfies MenuItem;
    })
    .filter(Boolean) as MenuItem[];
});

const resolveItemLink = (item: MenuItem) => {
  const internalPath = extractInternalPath(item.url, {
    siteUrl: normalizedSiteUrl.value,
  });

  if (!internalPath) {
    return {
      href: item.url,
      isInternal: false,
    };
  }

  return {
    href: localizeCmsPath(normalizeCmsInternalPath(internalPath), {
      locale: locale.value,
      localeCodes: localeCodes.value,
      localePath,
    }),
    isInternal: true,
  };
};

const isActive = (item: MenuItem) => {
  const link = resolveItemLink(item);

  if (!link.isInternal) {
    return false;
  }

  const currentPath = normalizePath(route.path);
  const targetPath = normalizePath(link.href);

  if (targetPath === "/") {
    return currentPath === "/";
  }

  return currentPath === targetPath || currentPath.startsWith(`${targetPath}/`);
};
</script>

<template>
  <SidebarWidgetCard v-if="variant === 'sidebar'" :title="title">
    <ul class="core-simple-menu-widget__list">
      <li
        v-for="item in items"
        :key="`${item.label}-${item.url}`"
        class="core-simple-menu-widget__item"
        :class="{ 'core-simple-menu-widget__item--active': isActive(item) }"
      >
        <NuxtLink
          v-if="resolveItemLink(item).isInternal"
          :to="resolveItemLink(item).href"
          class="core-simple-menu-widget__link"
        >
          {{ item.label }}
        </NuxtLink>

        <a
          v-else
          :href="resolveItemLink(item).href"
          class="core-simple-menu-widget__link"
          :target="item.isOpenNewTab ? '_blank' : undefined"
          :rel="item.isOpenNewTab ? 'noopener noreferrer' : undefined"
        >
          {{ item.label }}
        </a>
      </li>
    </ul>
  </SidebarWidgetCard>

  <article v-else class="footer-core-simple-menu-widget">
    <h2 v-if="title" class="footer-core-simple-menu-widget__title">
      {{ title }}
    </h2>

    <ul class="footer-core-simple-menu-widget__list">
      <li
        v-for="item in items"
        :key="`${item.label}-${item.url}`"
        class="footer-core-simple-menu-widget__item"
        :class="{ 'footer-core-simple-menu-widget__item--active': isActive(item) }"
      >
        <NuxtLink
          v-if="resolveItemLink(item).isInternal"
          :to="resolveItemLink(item).href"
          class="footer-core-simple-menu-widget__link"
        >
          {{ item.label }}
        </NuxtLink>

        <a
          v-else
          :href="resolveItemLink(item).href"
          class="footer-core-simple-menu-widget__link"
          :target="item.isOpenNewTab ? '_blank' : undefined"
          :rel="item.isOpenNewTab ? 'noopener noreferrer' : undefined"
        >
          {{ item.label }}
        </a>
      </li>
    </ul>
  </article>
</template>

<style scoped>
.core-simple-menu-widget__list,
.footer-core-simple-menu-widget__list {
  margin: 0;
  padding: 0;
  list-style: none;
}

.core-simple-menu-widget__item + .core-simple-menu-widget__item {
  border-top: 1px solid rgba(56, 41, 30, 0.08);
}

.core-simple-menu-widget__link {
  display: block;
  padding: 0.85rem 0;
  color: var(--retreat-ink);
  text-decoration: none;
  font-weight: 600;
}

.core-simple-menu-widget__item--active .core-simple-menu-widget__link,
.core-simple-menu-widget__link:hover {
  color: var(--retreat-clay);
}

.footer-core-simple-menu-widget__title {
  margin: 0 0 1rem;
  color: #fff9f1;
  font-family: var(--font-display);
  font-size: 2rem;
  line-height: 1;
}

.footer-core-simple-menu-widget__item + .footer-core-simple-menu-widget__item {
  margin-top: 0.65rem;
}

.footer-core-simple-menu-widget__link {
  color: rgba(255, 249, 241, 0.82);
  text-decoration: none;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
}

.footer-core-simple-menu-widget__item--active .footer-core-simple-menu-widget__link,
.footer-core-simple-menu-widget__link:hover {
  color: #fff9f1;
}
</style>



