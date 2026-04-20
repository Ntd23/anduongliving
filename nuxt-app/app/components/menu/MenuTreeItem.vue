<script setup lang="ts">
import type { MenuItemData } from "~/composables/useMenu";
import {
  extractInternalPath,
  localizeCmsPath,
  normalizeCmsInternalPath,
  normalizePath,
  normalizeSiteUrl,
} from "~~/shared/cms-routing";

const props = defineProps<{
  item: MenuItemData;
  depth?: number;
  variant?: "header" | "side" | "drawer";
}>();

const route = useRoute();
const localePath = useLocalePath();
const { locale, locales } = useI18n();
const runtimeConfig = useRuntimeConfig();

const localeCodes = computed(() =>
  (locales.value || [])
    .map((item) => (typeof item === "string" ? item : item.code))
    .filter((item): item is string => Boolean(item)),
);

const normalizedSiteUrl = computed(() =>
  normalizeSiteUrl(runtimeConfig.public.siteUrl),
);

const resolvedLink = computed(() => {
  const rawUrl = typeof props.item.url === "string" ? props.item.url.trim() : "";
  const internalPath = extractInternalPath(rawUrl, {
    siteUrl: normalizedSiteUrl.value,
    currentOrigin: import.meta.client ? window.location.origin : null,
  });

  if (internalPath) {
    const normalizedInternalPath = normalizeCmsInternalPath(internalPath, {
      referenceType: props.item.reference_type,
      referenceSlug: props.item.reference?.slug,
    });

    return {
      href: localizeCmsPath(normalizedInternalPath, {
        locale: locale.value,
        localeCodes: localeCodes.value,
        localePath,
      }),
      isInternal: true,
      target: undefined,
    };
  }

  return {
    href: rawUrl || "#",
    isInternal: false,
    target: props.item.target || undefined,
  };
});

const isActive = computed(() => {
  if (!resolvedLink.value.isInternal) {
    return false;
  }

  const currentPath = normalizePath(route.path);
  const itemPath = normalizePath(resolvedLink.value.href);

  if (itemPath === "/") {
    return currentPath === "/";
  }

  return currentPath === itemPath || currentPath.startsWith(`${itemPath}/`);
});

const hasChildren = computed(() => props.item.children?.length > 0);
const variant = computed(() => props.variant || "header");
</script>

<template>
  <li
    class="menu-tree-item"
    :class="{
      'menu-tree-item--active': isActive,
      'menu-tree-item--with-children': hasChildren,
      'menu-tree-item--side': variant === 'side',
      'menu-tree-item--drawer': variant === 'drawer',
    }"
  >
    <NuxtLink
      v-if="resolvedLink.isInternal"
      :to="resolvedLink.href"
      class="menu-tree-item__link"
      :class="item.css_class"
    >
      {{ item.title }}
    </NuxtLink>

    <a
      v-else
      :href="resolvedLink.href"
      class="menu-tree-item__link"
      :class="item.css_class"
      :target="resolvedLink.target"
      :rel="resolvedLink.target === '_blank' ? 'noopener noreferrer' : undefined"
    >
      {{ item.title }}
    </a>

    <ul v-if="hasChildren" class="menu-tree-item__children">
      <MenuTreeItem
        v-for="child in item.children"
        :key="child.id"
        :item="child"
        :depth="(depth || 0) + 1"
        :variant="variant"
      />
    </ul>
  </li>
</template>

<style scoped>
.menu-tree-item {
  position: relative;
  list-style: none;
}

.menu-tree-item__link {
  display: inline-flex;
  align-items: center;
  min-height: 2.75rem;
  color: #3f352d;
  font-size: 0.95rem;
  font-weight: 600;
  letter-spacing: 0.03em;
  text-decoration: none;
  transition:
    color 0.2s ease,
    opacity 0.2s ease;
}

.menu-tree-item__link:hover {
  color: #7c5c3b;
}

.menu-tree-item--active > .menu-tree-item__link {
  color: #7c5c3b;
}

.menu-tree-item__children {
  margin: 0;
  padding: 0.8rem 0 0;
  list-style: none;
}

.menu-tree-item--side > .menu-tree-item__link {
  min-height: 2.4rem;
  padding: 0.15rem 0;
  font-size: 0.98rem;
  font-weight: 500;
  letter-spacing: 0.04em;
}

.menu-tree-item--side > .menu-tree-item__children {
  padding-top: 0.15rem;
  padding-left: 1rem;
}

.menu-tree-item--side :deep(.menu-tree-item__link) {
  min-height: 2rem;
  font-size: 0.9rem;
}

.menu-tree-item--drawer > .menu-tree-item__link {
  width: 100%;
  min-height: 2.6rem;
  padding: 0.4rem 0;
  color: rgba(255, 246, 232, 0.92);
  font-size: 1rem;
  font-weight: 500;
  letter-spacing: 0.04em;
}

.menu-tree-item--drawer > .menu-tree-item__link:hover,
.menu-tree-item--drawer.menu-tree-item--active > .menu-tree-item__link {
  color: #d2af71;
}

.menu-tree-item--drawer > .menu-tree-item__children {
  padding-top: 0.1rem;
  padding-left: 1rem;
}

.menu-tree-item--drawer :deep(.menu-tree-item__link) {
  min-height: 2rem;
  font-size: 0.92rem;
}

@media (min-width: 992px) {
  .menu-tree-item__children {
    position: absolute;
    top: 100%;
    left: 0;
    display: grid;
    gap: 0.25rem;
    min-width: 14rem;
    padding: 0.9rem 1rem;
    border: 1px solid rgba(63, 53, 45, 0.08);
    border-radius: 1rem;
    background: rgba(255, 251, 246, 0.98);
    box-shadow: 0 24px 48px rgba(63, 53, 45, 0.12);
    opacity: 0;
    pointer-events: none;
    transform: translateY(10px);
    transition:
      opacity 0.2s ease,
      transform 0.2s ease;
    backdrop-filter: blur(14px);
  }

  .menu-tree-item--with-children:hover > .menu-tree-item__children,
  .menu-tree-item--with-children:focus-within > .menu-tree-item__children {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
  }

  .menu-tree-item__children :deep(.menu-tree-item__link) {
    min-height: 2.2rem;
    font-size: 0.9rem;
    font-weight: 500;
  }

  .menu-tree-item--side > .menu-tree-item__children {
    position: static;
    display: grid;
    min-width: 0;
    padding: 0.2rem 0 0.4rem 1rem;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    opacity: 1;
    pointer-events: auto;
    transform: none;
    backdrop-filter: none;
  }

  .menu-tree-item--drawer > .menu-tree-item__children {
    position: static;
    display: grid;
    min-width: 0;
    padding: 0.2rem 0 0.4rem 1rem;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    opacity: 1;
    pointer-events: auto;
    transform: none;
    backdrop-filter: none;
  }
}
</style>
