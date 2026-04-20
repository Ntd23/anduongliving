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
  position: relative;
  display: inline-flex;
  align-items: center;
  min-height: 2.6rem;
  color: var(--retreat-ink);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-decoration: none;
  text-transform: uppercase;
}

.menu-tree-item__link::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0.55rem;
  height: 1px;
  background: currentColor;
  opacity: 0;
  transform: scaleX(0.6);
  transform-origin: center;
  transition:
    opacity 0.22s ease,
    transform 0.22s ease;
}

.menu-tree-item__link:hover,
.menu-tree-item--active > .menu-tree-item__link {
  color: var(--retreat-clay);
}

.menu-tree-item__link:hover::after,
.menu-tree-item--active > .menu-tree-item__link::after {
  opacity: 0.5;
  transform: scaleX(1);
}

.menu-tree-item__children {
  margin: 0;
  padding: 0.7rem 0 0;
  list-style: none;
}

.menu-tree-item--side > .menu-tree-item__link {
  min-height: 2.55rem;
  color: var(--retreat-ink);
  font-size: 0.9rem;
  letter-spacing: 0.14em;
}

.menu-tree-item--side > .menu-tree-item__children {
  padding-top: 0.15rem;
  padding-left: 1rem;
}

.menu-tree-item--side :deep(.menu-tree-item__link) {
  min-height: 2rem;
  font-size: 0.76rem;
}

.menu-tree-item--drawer > .menu-tree-item__link {
  width: 100%;
  min-height: 2.85rem;
  color: rgba(255, 249, 241, 0.92);
  font-size: 0.84rem;
  letter-spacing: 0.18em;
}

.menu-tree-item--drawer > .menu-tree-item__link:hover,
.menu-tree-item--drawer.menu-tree-item--active > .menu-tree-item__link {
  color: #d6c09b;
}

.menu-tree-item--drawer > .menu-tree-item__children {
  padding-top: 0.1rem;
  padding-left: 0.8rem;
}

.menu-tree-item--drawer :deep(.menu-tree-item__link) {
  min-height: 2rem;
  font-size: 0.78rem;
}

@media (min-width: 992px) {
  .menu-tree-item__children {
    position: absolute;
    top: calc(100% + 0.25rem);
    left: 50%;
    display: grid;
    gap: 0.2rem;
    min-width: 15rem;
    padding: 1rem 1.1rem;
    border: 1px solid rgba(46, 32, 22, 0.08);
    border-radius: 1.5rem;
    background: rgba(255, 252, 247, 0.94);
    box-shadow: 0 24px 60px rgba(35, 22, 13, 0.08);
    opacity: 0;
    pointer-events: none;
    transform: translate(-50%, 10px);
    transition:
      opacity 0.22s ease,
      transform 0.22s ease;
    backdrop-filter: blur(14px);
  }

  .menu-tree-item--with-children:hover > .menu-tree-item__children,
  .menu-tree-item--with-children:focus-within > .menu-tree-item__children {
    opacity: 1;
    pointer-events: auto;
    transform: translate(-50%, 0);
  }

  .menu-tree-item__children :deep(.menu-tree-item__link) {
    min-height: 2.1rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.16em;
  }

  .menu-tree-item--side > .menu-tree-item__children,
  .menu-tree-item--drawer > .menu-tree-item__children {
    position: static;
    display: grid;
    min-width: 0;
    padding: 0.2rem 0 0.35rem 1rem;
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
