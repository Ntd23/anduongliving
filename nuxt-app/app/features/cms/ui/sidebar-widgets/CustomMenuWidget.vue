<script setup lang="ts">
import SidebarWidgetCard from "~/features/cms/ui/sidebar-widgets/SidebarWidgetCard.vue";
import SidebarRawHtmlWidget from "~/features/cms/ui/sidebar-widgets/SidebarRawHtmlWidget.vue";
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
} from "~/features/cms/ui/sidebar-widgets/registry";

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
  border-bottom: 1px solid rgba(56, 41, 30, 0.08);
}

.sidebar-custom-menu__item:last-child {
  border-bottom: 0;
}

.sidebar-custom-menu__link {
  display: block;
  padding: 0.85rem 0;
  color: var(--retreat-ink);
  font-size: 0.96rem;
  font-weight: 600;
  line-height: 1.55;
  text-decoration: none;
}

.sidebar-custom-menu__link:hover {
  color: var(--retreat-clay);
  transform: translateX(3px);
}

.sidebar-custom-menu__item--active > .sidebar-custom-menu__link,
.sidebar-custom-menu__item--branch-active > .sidebar-custom-menu__link {
  color: var(--retreat-clay);
}

.sidebar-custom-menu__item--active > .sidebar-custom-menu__link {
  font-weight: 700;
}

.sidebar-custom-menu__list--nested {
  margin-top: -0.1rem;
  padding: 0 0 0.9rem 1rem;
}

.sidebar-custom-menu__item--child {
  border-bottom: 0;
}

.sidebar-custom-menu__link--child {
  position: relative;
  padding: 0.35rem 0 0.35rem 1rem;
  font-size: 0.86rem;
  font-weight: 500;
}

.sidebar-custom-menu__link--child::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 0;
  width: 0.35rem;
  height: 0.35rem;
  border-radius: 999px;
  background: rgba(167, 122, 84, 0.4);
  transform: translateY(-50%);
}

.footer-custom-menu__title {
  margin: 0 0 1rem;
  color: #fff9f1;
  font-family: var(--font-display);
  font-size: 2rem;
  line-height: 1;
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
  color: rgba(255, 249, 241, 0.82);
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-decoration: none;
  text-transform: uppercase;
}

.footer-custom-menu__link:hover {
  color: #d6c09b;
  transform: translateX(2px);
}

.footer-custom-menu__item--active > .footer-custom-menu__link,
.footer-custom-menu__item--branch-active > .footer-custom-menu__link {
  color: #fff9f1;
}

.footer-custom-menu__item--active > .footer-custom-menu__link {
  font-weight: 700;
}

.footer-custom-menu__list--nested {
  margin-top: 0.35rem;
  padding-left: 1rem;
}

.footer-custom-menu__link--child {
  color: rgba(248, 244, 236, 0.66);
  font-size: 0.74rem;
}
</style>



