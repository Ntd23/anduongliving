<script setup lang="ts">
import SidebarWidgetCard from "~/components/sidebar-widgets/SidebarWidgetCard.vue";
import SidebarRawHtmlWidget from "~/components/sidebar-widgets/SidebarRawHtmlWidget.vue";
import {
  extractInternalPath,
  localizeCmsPath,
  normalizeCmsInternalPath,
  normalizePath,
  normalizeSiteUrl,
} from "~~/shared/cms-routing";
import type {
  SidebarMenuNode,
  SidebarWidgetManifest,
} from "~/utils/sidebar-widgets";

const props = defineProps<{
  widget: SidebarWidgetManifest;
  variant?: "sidebar" | "footer";
}>();

const route = useRoute();
const localePath = useLocalePath();
const { locale, locales } = useI18n();
const runtimeConfig = useRuntimeConfig();

const title = computed(() => {
  const value = props.widget.data?.name || props.widget.menu_data?.name;
  return typeof value === "string" && value.trim() ? value.trim() : null;
});

const menuNodes = computed<SidebarMenuNode[]>(() =>
  Array.isArray(props.widget.menu_data?.nodes) ? props.widget.menu_data.nodes : [],
);
const variant = computed(() => props.variant || "sidebar");

const localeCodes = computed(() =>
  (locales.value || [])
    .map((item) => (typeof item === "string" ? item : item.code))
    .filter((item): item is string => Boolean(item)),
);

const fallbackToHtml = computed(
  () => !menuNodes.value.length,
);

const normalizedSiteUrl = computed(() =>
  normalizeSiteUrl(runtimeConfig.public.siteUrl),
);

const resolveNodeLink = (node: SidebarMenuNode) => {
  const rawUrl = typeof node.url === "string" ? node.url.trim() : "";
  const internalPath = extractInternalPath(rawUrl, {
    siteUrl: normalizedSiteUrl.value,
    currentOrigin: import.meta.client ? window.location.origin : null,
  });

  if (!rawUrl) {
    return {
      href: "#",
      isInternal: false,
      target: undefined,
    };
  }

  if (!internalPath) {
    return {
      href: rawUrl,
      isInternal: false,
      target: node.target || undefined,
    };
  }

  const localizedPath = localizeCmsPath(
    normalizeCmsInternalPath(internalPath),
    {
      locale: locale.value,
      localeCodes: localeCodes.value,
      localePath,
    },
  );

  return {
    href: localizedPath,
    isInternal: true,
    target: undefined,
  };
};

const isNodeActive = (node: SidebarMenuNode): boolean => {
  const link = resolveNodeLink(node);

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

const hasActiveDescendant = (node: SidebarMenuNode): boolean =>
  Array.isArray(node.children) && node.children.some((child) => isNodeActive(child) || hasActiveDescendant(child));

</script>

<template>
  <SidebarRawHtmlWidget v-if="fallbackToHtml" :widget="widget" />

  <SidebarWidgetCard v-else-if="variant === 'sidebar'" :title="title">
    <ul class="sidebar-custom-menu__list">
      <template v-for="node in menuNodes" :key="node.id">
        <li
          class="sidebar-custom-menu__item"
          :class="{
            'sidebar-custom-menu__item--active': isNodeActive(node),
            'sidebar-custom-menu__item--branch-active': hasActiveDescendant(node),
          }"
        >
          <NuxtLink
            v-if="resolveNodeLink(node).isInternal"
            :to="resolveNodeLink(node).href"
            class="sidebar-custom-menu__link"
          >
            {{ node.title }}
          </NuxtLink>

          <a
            v-else
            :href="resolveNodeLink(node).href"
            class="sidebar-custom-menu__link"
            :target="resolveNodeLink(node).target"
            :rel="resolveNodeLink(node).target === '_blank' ? 'noopener noreferrer' : undefined"
          >
            {{ node.title }}
          </a>

          <ul
            v-if="node.children?.length"
            class="sidebar-custom-menu__list sidebar-custom-menu__list--nested"
          >
            <li
              v-for="child in node.children"
              :key="child.id"
              class="sidebar-custom-menu__item sidebar-custom-menu__item--child"
              :class="{
                'sidebar-custom-menu__item--active': isNodeActive(child),
                'sidebar-custom-menu__item--branch-active': hasActiveDescendant(child),
              }"
            >
              <NuxtLink
                v-if="resolveNodeLink(child).isInternal"
                :to="resolveNodeLink(child).href"
                class="sidebar-custom-menu__link sidebar-custom-menu__link--child"
              >
                {{ child.title }}
              </NuxtLink>

              <a
                v-else
                :href="resolveNodeLink(child).href"
                class="sidebar-custom-menu__link sidebar-custom-menu__link--child"
                :target="resolveNodeLink(child).target"
                :rel="resolveNodeLink(child).target === '_blank' ? 'noopener noreferrer' : undefined"
              >
                {{ child.title }}
              </a>
            </li>
          </ul>
        </li>
      </template>
    </ul>
  </SidebarWidgetCard>

  <article v-else class="footer-custom-menu">
    <h2 v-if="title" class="footer-custom-menu__title">
      {{ title }}
    </h2>

    <ul class="footer-custom-menu__list">
      <template v-for="node in menuNodes" :key="node.id">
        <li
          class="footer-custom-menu__item"
          :class="{
            'footer-custom-menu__item--active': isNodeActive(node),
            'footer-custom-menu__item--branch-active': hasActiveDescendant(node),
          }"
        >
          <NuxtLink
            v-if="resolveNodeLink(node).isInternal"
            :to="resolveNodeLink(node).href"
            class="footer-custom-menu__link"
          >
            {{ node.title }}
          </NuxtLink>

          <a
            v-else
            :href="resolveNodeLink(node).href"
            class="footer-custom-menu__link"
            :target="resolveNodeLink(node).target"
            :rel="resolveNodeLink(node).target === '_blank' ? 'noopener noreferrer' : undefined"
          >
            {{ node.title }}
          </a>

          <ul
            v-if="node.children?.length"
            class="footer-custom-menu__list footer-custom-menu__list--nested"
          >
            <li
              v-for="child in node.children"
              :key="child.id"
              class="footer-custom-menu__item footer-custom-menu__item--child"
              :class="{
                'footer-custom-menu__item--active': isNodeActive(child),
                'footer-custom-menu__item--branch-active': hasActiveDescendant(child),
              }"
            >
              <NuxtLink
                v-if="resolveNodeLink(child).isInternal"
                :to="resolveNodeLink(child).href"
                class="footer-custom-menu__link footer-custom-menu__link--child"
              >
                {{ child.title }}
              </NuxtLink>

              <a
                v-else
                :href="resolveNodeLink(child).href"
                class="footer-custom-menu__link footer-custom-menu__link--child"
                :target="resolveNodeLink(child).target"
                :rel="resolveNodeLink(child).target === '_blank' ? 'noopener noreferrer' : undefined"
              >
                {{ child.title }}
              </a>
            </li>
          </ul>
        </li>
      </template>
    </ul>
  </article>
</template>

<style scoped>
.sidebar-custom-menu__list {
  margin: 0;
  padding: 0;
  list-style: none;
}

.sidebar-custom-menu__item {
  margin: 0;
  border-bottom: 1px solid rgba(63, 53, 45, 0.08);
}

.sidebar-custom-menu__item:last-child {
  border-bottom: 0;
}

.sidebar-custom-menu__link {
  display: block;
  padding: 0.85rem 0;
  color: #3f352d;
  font-size: 0.96rem;
  font-weight: 600;
  letter-spacing: 0.03em;
  text-decoration: none;
  transition:
    color 0.2s ease,
    transform 0.2s ease;
}

.sidebar-custom-menu__link:hover {
  color: #8d7355;
  transform: translateX(2px);
}

.sidebar-custom-menu__item--active > .sidebar-custom-menu__link,
.sidebar-custom-menu__item--branch-active > .sidebar-custom-menu__link {
  color: #7c5c3b;
}

.sidebar-custom-menu__item--active > .sidebar-custom-menu__link {
  font-weight: 700;
}

.sidebar-custom-menu__list--nested {
  margin-top: -0.2rem;
  padding: 0 0 0.8rem 0.9rem;
}

.sidebar-custom-menu__item--child {
  border-bottom: 0;
}

.sidebar-custom-menu__link--child {
  padding: 0.4rem 0;
  font-size: 0.9rem;
  font-weight: 500;
}

.footer-custom-menu__title {
  margin: 0 0 1rem;
  color: #fff3dc;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.75rem;
  line-height: 1.1;
}

.footer-custom-menu__list {
  margin: 0;
  padding: 0;
  list-style: none;
}

.footer-custom-menu__item + .footer-custom-menu__item {
  margin-top: 0.65rem;
}

.footer-custom-menu__link {
  color: rgba(255, 243, 220, 0.86);
  text-decoration: none;
  transition:
    color 0.2s ease,
    transform 0.2s ease;
}

.footer-custom-menu__link:hover {
  color: #d8b56b;
  transform: translateX(2px);
}

.footer-custom-menu__item--active > .footer-custom-menu__link,
.footer-custom-menu__item--branch-active > .footer-custom-menu__link {
  color: #fff7ea;
}

.footer-custom-menu__item--active > .footer-custom-menu__link {
  font-weight: 700;
}

.footer-custom-menu__list--nested {
  margin-top: 0.35rem;
  padding-left: 1rem;
}

.footer-custom-menu__link--child {
  color: rgba(245, 234, 214, 0.78);
  font-size: 0.94rem;
}
</style>
