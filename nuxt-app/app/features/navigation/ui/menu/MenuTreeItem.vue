<script setup lang="ts">
import type { MenuItemData } from "~/features/cms/data/useMenu";
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
const isDrawer = computed(() => variant.value === "drawer");
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
      :class="[item.css_class, { 'menu-tree-item__link--drawer-parent': isDrawer && hasChildren }]"
    >
      {{ item.title }}
    </NuxtLink>

    <a
      v-else
      :href="resolvedLink.href"
      class="menu-tree-item__link"
      :class="[item.css_class, { 'menu-tree-item__link--drawer-parent': isDrawer && hasChildren }]"
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

.menu-tree-item--with-children::before {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  top: 100%;
  height: 0.85rem;
}

.menu-tree-item__link {
  position: relative;
  display: inline-flex;
  align-items: center;
  min-height: 2.8rem;
  padding-inline: 0.95rem;
  border-radius: 999px;
  font-size: 0.82rem;
  font-weight: bold;
  letter-spacing: 0.18em;
  text-decoration: none;
  text-transform: uppercase;
}

.menu-tree-item__link:hover,
.menu-tree-item--active > .menu-tree-item__link {
  color: var(--retreat-clay);

}

.menu-tree-item__children {
  margin: 0;
  padding: 0.7rem 0 0;
  list-style: none;
}

.menu-tree-item--side > .menu-tree-item__link {
  min-height: 2.55rem;
  padding-inline: 0;
  border: 0;
  border-radius: 0;
  background: transparent;
  box-shadow: none;
  color: var(--retreat-ink);
  font-size: 1rem;
  font-weight: bold;
  letter-spacing: 0.14em;
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
  min-height: 2.85rem;
  padding-inline: 0;
  border: 0;
  border-radius: 0;
  background: transparent;
  box-shadow: none;
  color: rgba(255, 249, 241, 0.92);
  font-size: 0.95rem;
  font-weight: bold;
  letter-spacing: 0.16em;
}

.menu-tree-item--drawer > .menu-tree-item__link:hover,
.menu-tree-item--drawer.menu-tree-item--active > .menu-tree-item__link {
  color: #d6c09b;
}

.menu-tree-item__link--drawer-parent {
  color: rgba(214, 192, 155, 0.9);
}

.menu-tree-item--drawer > .menu-tree-item__children {
  padding-top: 0.1rem;
  padding-left: 0.8rem;
}

.menu-tree-item--drawer :deep(.menu-tree-item__link) {
  min-height: 2.15rem;
  font-size: 0.86rem;
}

@media (min-width: 992px) {
  /* â”€â”€ Chevron indicator on parent items (header variant) â”€â”€ */
  .menu-tree-item--with-children:not(.menu-tree-item--side):not(.menu-tree-item--drawer) > .menu-tree-item__link::after {
    content: "";
    display: inline-block;
    width: 0.3em;
    height: 0.3em;
    margin-left: 0.4em;
    border-right: 1.5px solid currentColor;
    border-bottom: 1.5px solid currentColor;
    transform: rotate(45deg) translateY(-2px);
    transition:
      transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
      opacity 0.3s ease;
    opacity: 0.4;
  }

  .menu-tree-item--with-children:not(.menu-tree-item--side):not(.menu-tree-item--drawer):hover > .menu-tree-item__link::after {
    transform: rotate(-135deg) translateY(0);
    opacity: 0.85;
  }

  /* â”€â”€ Dropdown panel â”€â”€ */
  .menu-tree-item__children {
    position: absolute;
    top: calc(100%);
    left: 50%;
    display: grid;
    gap: 0.1rem;
    min-width: 16rem;
    padding: 0.55rem 0.3rem 0.45rem;
    border: 1px solid rgba(120, 105, 87, 0.09);
    border-radius: 0.75rem;
    background:
      linear-gradient(
        180deg,
        rgba(255, 255, 255, 0.94),
        rgba(255, 255, 255, 0.84)
      );
    box-shadow:
      0 4px 12px rgba(35, 22, 13, 0.04),
      0 24px 56px rgba(35, 22, 13, 0.08);
    opacity: 0;
    pointer-events: none;
    transform: translate(-50%, 8px);
    transition:
      opacity 0.28s cubic-bezier(0.4, 0, 0.2, 1),
      transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(18px) saturate(130%);
  }

  /* Gold accent line at top of dropdown */
  .menu-tree-item__children::before {
    content: "";
    display: block;
    width: 2rem;
    height: 1.5px;
    margin: 0 auto 0.3rem;
    background: linear-gradient(90deg, transparent, var(--retreat-clay), transparent);
    border-radius: 1px;
    opacity: 0.5;
  }

  .menu-tree-item--with-children:hover > .menu-tree-item__children,
  .menu-tree-item--with-children:focus-within > .menu-tree-item__children {
    opacity: 1;
    pointer-events: auto;
    transform: translate(-50%, 0);
  }

  /* â”€â”€ Stagger reveal for dropdown items â”€â”€ */
  .menu-tree-item__children :deep(.menu-tree-item) {
    opacity: 0;
    transform: translateY(6px);
    transition:
      opacity 0.28s cubic-bezier(0.4, 0, 0.2, 1),
      transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .menu-tree-item--with-children:hover > .menu-tree-item__children :deep(.menu-tree-item),
  .menu-tree-item--with-children:focus-within > .menu-tree-item__children :deep(.menu-tree-item) {
    opacity: 1;
    transform: translateY(0);
  }

  .menu-tree-item--with-children:hover > .menu-tree-item__children :deep(.menu-tree-item:nth-child(2)),
  .menu-tree-item--with-children:focus-within > .menu-tree-item__children :deep(.menu-tree-item:nth-child(2)) {
    transition-delay: 0.04s;
  }

  .menu-tree-item--with-children:hover > .menu-tree-item__children :deep(.menu-tree-item:nth-child(3)),
  .menu-tree-item--with-children:focus-within > .menu-tree-item__children :deep(.menu-tree-item:nth-child(3)) {
    transition-delay: 0.08s;
  }

  .menu-tree-item--with-children:hover > .menu-tree-item__children :deep(.menu-tree-item:nth-child(4)),
  .menu-tree-item--with-children:focus-within > .menu-tree-item__children :deep(.menu-tree-item:nth-child(4)) {
    transition-delay: 0.12s;
  }

  .menu-tree-item--with-children:hover > .menu-tree-item__children :deep(.menu-tree-item:nth-child(5)),
  .menu-tree-item--with-children:focus-within > .menu-tree-item__children :deep(.menu-tree-item:nth-child(5)) {
    transition-delay: 0.16s;
  }

  .menu-tree-item--with-children:hover > .menu-tree-item__children :deep(.menu-tree-item:nth-child(6)),
  .menu-tree-item--with-children:focus-within > .menu-tree-item__children :deep(.menu-tree-item:nth-child(6)) {
    transition-delay: 0.20s;
  }

  /* â”€â”€ Dropdown item links â”€â”€ */
  .menu-tree-item__children :deep(.menu-tree-item__link) {
    position: relative;
    width: 100%;
    min-height: 2.55rem;
    justify-content: flex-start;
    padding-inline: 0.9rem;
    border-radius: 0.45rem;
    font-size: 0.78rem;
    font-weight: bold;
    letter-spacing: 0.17em;
    transition:
      background-color 0.25s ease,
      color 0.25s ease;
  }

  /* Subtle background highlight on hover */
  .menu-tree-item__children :deep(.menu-tree-item__link):hover {
    background: rgba(167, 122, 84, 0.06);
  }

  /* Gold underline â€” slides in from left on hover */
  .menu-tree-item__children :deep(.menu-tree-item__link)::before {
    content: "";
    position: absolute;
    bottom: 0.3rem;
    left: 0.9rem;
    right: 0.9rem;
    height: 1px;
    background: var(--retreat-clay);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0.5;
  }

  .menu-tree-item__children :deep(.menu-tree-item__link):hover::before {
    transform: scaleX(1);
  }

  /* â”€â”€ Side / Drawer overrides â€” keep existing behavior â”€â”€ */
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

  .menu-tree-item--side > .menu-tree-item__children::before,
  .menu-tree-item--drawer > .menu-tree-item__children::before {
    display: none;
  }

  .menu-tree-item--side .menu-tree-item__children :deep(.menu-tree-item),
  .menu-tree-item--drawer .menu-tree-item__children :deep(.menu-tree-item) {
    opacity: 1;
    transform: none;
    transition: none;
  }

  .menu-tree-item--side .menu-tree-item__children :deep(.menu-tree-item__link)::before,
  .menu-tree-item--drawer .menu-tree-item__children :deep(.menu-tree-item__link)::before {
    display: none;
  }

  .menu-tree-item--side::before,
  .menu-tree-item--drawer::before {
    display: none;
  }
}
</style>



