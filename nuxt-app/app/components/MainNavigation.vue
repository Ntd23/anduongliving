<script setup lang="ts">
import MobileMenuCollapse from "~/components/menu/MobileMenuCollapse.vue";
import { useMenu } from "~/composables/useMenu";
import { useThemeOptions, type ThemeSocialLink } from "~/composables/useThemeOptions";
import { CMS_MAIN_MENU_TOKEN, cmsAppRoutes, normalizePath } from "~~/shared/cms-routing";

const props = withDefaults(
  defineProps<{
    variant?: "header" | "side";
    fullWidth?: boolean;
  }>(),
  {
    variant: "header",
    fullWidth: false,
  },
);

const { locale } = useI18n();
const localePath = useLocalePath();
const route = useRoute();

const activeLocale = computed(() => locale.value);
const mobileMenuOpen = ref(false);

const { data, error } = await useAsyncData(
  () => `main-menu-${activeLocale.value}`,
  () => useMenu(CMS_MAIN_MENU_TOKEN, activeLocale.value, { canonical: true }),
  { watch: [activeLocale] },
);

const { data: themeOptions } = await useAsyncData(
  () => `theme-options-${props.variant}-${activeLocale.value}`,
  () => useThemeOptions(activeLocale.value),
  { watch: [activeLocale] },
);

const menu = computed(() => data.value);
const menuError = computed(() => {
  const statusMessage = error.value?.statusMessage || error.value?.message;

  if (!error.value) {
    return null;
  }

  return statusMessage || "Main menu is not configured for this locale.";
});

const brandHref = computed(() => localePath(cmsAppRoutes.home()));
const brandLabel = computed(() => {
  const title = themeOptions.value?.site_title;
  return typeof title === "string" && title.trim() ? title.trim() : "Anduong Living";
});
const brandLogo = computed(() => {
  const logo = themeOptions.value?.logo_url;
  return typeof logo === "string" && logo ? logo : null;
});
const socialLinks = computed<ThemeSocialLink[]>(() =>
  Array.isArray(themeOptions.value?.social_links) ? themeOptions.value.social_links : [],
);
const headerButtonLabel = computed(() => themeOptions.value?.header_button_label?.trim() || "");
const headerButtonUrl = computed(() => themeOptions.value?.header_button_url?.trim() || "");
const showHeaderButton = computed(() =>
  props.variant === "header" && !props.fullWidth && !!headerButtonLabel.value && !!headerButtonUrl.value,
);
const isHomepageRoute = computed(() => normalizePath(route.path) === normalizePath(localePath("/")));
const headerStickyEnabled = computed(() => {
  const value = themeOptions.value?.header_sticky_enabled;

  if (typeof value === "boolean") {
    return value;
  }

  return value !== "no";
});
const useBottomStickyHomepageHeader = computed(() =>
  props.variant === "header" && isHomepageRoute.value && headerStickyEnabled.value,
);
const wrapperClass = computed(() =>
  props.fullWidth ? "main-navigation__inner main-navigation__inner--fluid" : "container main-navigation__inner",
);
const showMobileToggle = computed(() => props.variant === "header");

const socialIconMap: Record<string, string> = {
  facebook: "ph:facebook-logo-fill",
  instagram: "ph:instagram-logo-fill",
  twitter: "ph:x-logo-fill",
  x: "ph:x-logo-fill",
  youtube: "ph:youtube-logo-fill",
  linkedin: "ph:linkedin-logo-fill",
  tiktok: "ph:tiktok-logo-fill",
  telegram: "ph:telegram-logo-fill",
  pinterest: "ph:pinterest-logo-fill",
  whatsapp: "ph:whatsapp-logo-fill",
};

const resolveSocialIcon = (social: ThemeSocialLink) => {
  const source = `${social.icon || ""} ${social.name || ""}`.toLowerCase();

  return Object.entries(socialIconMap).find(([key]) => source.includes(key))?.[1] || null;
};

const socialLabel = (social: ThemeSocialLink) => {
  const value = (social.name || social.url || "").trim();
  return value ? value.slice(0, 1).toUpperCase() : "?";
};

const socialStyle = (social: ThemeSocialLink) => {
  const style: Record<string, string> = {};

  if (social.color) {
    style.color = social.color;
  }

  if (social.background_color) {
    style.backgroundColor = social.background_color;
  }

  return style;
};

watch(
  () => route.fullPath,
  () => {
    mobileMenuOpen.value = false;
  },
);
</script>

<template>
  <header
    class="main-navigation"
    :class="{
      'main-navigation--side': variant === 'side',
      'main-navigation--header': variant === 'header',
      'main-navigation--bottom-home': useBottomStickyHomepageHeader,
      'main-navigation--full-width': fullWidth,
    }"
  >
    <div :class="wrapperClass">
      <NuxtLink :to="brandHref" class="main-navigation__brand">
        <img
          v-if="brandLogo"
          :src="brandLogo"
          :alt="brandLabel"
          class="main-navigation__brand-logo"
        >
        <span
          v-else
          class="main-navigation__brand-text"
        >
          {{ brandLabel }}
        </span>
      </NuxtLink>

      <p v-if="menuError" class="main-navigation__error">
        {{ menuError }}
      </p>

      <nav
        v-else-if="menu?.items?.length"
        class="main-navigation__nav"
        aria-label="Main menu"
      >
        <ul
          class="main-navigation__list"
          :class="{ 'main-navigation__list--side': variant === 'side' }"
        >
          <MenuTreeItem
            v-for="item in menu.items"
            :key="item.id"
            :item="item"
            :variant="variant"
          />
        </ul>
      </nav>

      <a
        v-if="showHeaderButton"
        :href="headerButtonUrl"
        class="main-navigation__action"
      >
        {{ headerButtonLabel }}
      </a>

      <button
        v-if="showMobileToggle"
        type="button"
        class="main-navigation__mobile-toggle"
        aria-label="Open mobile menu"
        :aria-expanded="mobileMenuOpen ? 'true' : 'false'"
        @click="mobileMenuOpen = !mobileMenuOpen"
      >
        <Icon :name="mobileMenuOpen ? 'ph:x' : 'ph:list'" />
      </button>

      <div
        v-if="variant === 'side'"
        class="main-navigation__meta"
      >
        <div v-if="socialLinks.length" class="main-navigation__socials">
          <a
            v-for="social in socialLinks"
            :key="`${social.name}-${social.url}`"
            :href="social.url"
            class="main-navigation__social-link"
            :title="social.name || social.url"
            target="_blank"
            rel="noopener noreferrer"
            :style="socialStyle(social)"
          >
            <img
              v-if="social.image_url"
              :src="social.image_url"
              :alt="social.name || 'Social link'"
              class="main-navigation__social-image"
            >
            <Icon
              v-else-if="resolveSocialIcon(social)"
              :name="resolveSocialIcon(social) || ''"
              class="main-navigation__social-icon"
            />
            <span v-else class="main-navigation__social-fallback">
              {{ socialLabel(social) }}
            </span>
          </a>
        </div>
      </div>
    </div>

    <MobileMenuCollapse
      v-if="showMobileToggle"
      v-model:open="mobileMenuOpen"
      :menu="menu"
      :menu-error="menuError"
      :brand-href="brandHref"
      :brand-label="brandLabel"
      :brand-logo="brandLogo"
      :hotline="themeOptions?.hotline || null"
      :email="themeOptions?.email || null"
      :social-links="socialLinks"
    />
  </header>
</template>

<style scoped>
.main-navigation {
  position: sticky;
  top: 0;
  z-index: 30;
  border-bottom: 1px solid rgba(63, 53, 45, 0.08);
  background:
    linear-gradient(180deg, rgba(255, 250, 244, 0.96), rgba(252, 245, 236, 0.92));
  backdrop-filter: blur(14px);
}

.main-navigation--bottom-home {
  border-bottom: 0;
}

.main-navigation__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  min-height: 5rem;
}

.main-navigation__inner--fluid {
  width: 100%;
  padding-inline: 2rem;
}

.main-navigation__brand {
  display: inline-flex;
  align-items: center;
  gap: 0.9rem;
  color: #2e241d;
  font-family: "Iowan Old Style", "Palatino Linotype", "Book Antiqua", Georgia, serif;
  font-size: 1.4rem;
  letter-spacing: 0.08em;
  text-decoration: none;
  text-transform: uppercase;
}

.main-navigation__brand-logo {
  display: block;
  width: auto;
  max-width: 8.5rem;
  max-height: 4.5rem;
  object-fit: contain;
}

.main-navigation__brand-text {
  display: inline-block;
}

.main-navigation__nav {
  flex: 1 1 auto;
}

.main-navigation__list {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 1.4rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.main-navigation__error {
  margin: 0;
  color: #8b3a2f;
  font-size: 0.92rem;
  font-weight: 600;
}

.main-navigation__action {
  display: none;
  align-items: center;
  justify-content: center;
  min-height: 3rem;
  padding: 0.85rem 1.5rem;
  background: #b99247;
  color: #fff;
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-decoration: none;
  text-transform: uppercase;
  transition: background-color 0.2s ease;
}

.main-navigation__action:hover {
  background: #8a6526;
}

.main-navigation__mobile-toggle {
  display: none;
  align-items: center;
  justify-content: center;
  width: 2.9rem;
  height: 2.9rem;
  border: 1px solid rgba(63, 53, 45, 0.12);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.72);
  color: #3f352d;
  font-size: 1.3rem;
}

.main-navigation--bottom-home .main-navigation__brand,
.main-navigation--bottom-home .main-navigation__error,
.main-navigation--bottom-home :deep(.menu-tree-item__link),
.main-navigation--bottom-home :deep(.menu-tree-item__toggle) {
  color: #fff7ef;
}

.main-navigation--bottom-home .main-navigation__mobile-toggle {
  border-color: rgba(255, 247, 239, 0.16);
  background: rgba(255, 247, 239, 0.08);
  color: #fff7ef;
}

.main-navigation--side {
  position: static;
  display: flex;
  height: 100%;
  min-height: 100%;
  border-bottom: 0;
  background: linear-gradient(180deg, #faf5ee 0%, #f3ebdf 100%);
  backdrop-filter: none;
}

.main-navigation--side .main-navigation__inner {
  flex: 1 1 auto;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  gap: 2rem;
  min-height: 100%;
  padding: 2rem 1.5rem;
}

.main-navigation--side .main-navigation__brand {
  display: grid;
  gap: 0.9rem;
  font-size: 1.6rem;
  line-height: 1.15;
}

.main-navigation--side .main-navigation__brand-logo {
  max-width: 10rem;
  max-height: 5rem;
}

.main-navigation--side .main-navigation__nav {
  width: 100%;
}

.main-navigation__list--side {
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  gap: 0.5rem;
  width: 100%;
}

.main-navigation__meta {
  margin-top: auto;
  width: 100%;
  padding-top: 1.25rem;
  border-top: 1px solid rgba(63, 53, 45, 0.08);
}

.main-navigation__socials {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.main-navigation__social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.75rem;
  height: 2.75rem;
  border: 1px solid rgba(63, 53, 45, 0.1);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.7);
  color: #6e5843;
  text-decoration: none;
  transition:
    transform 0.2s ease,
    color 0.2s ease,
    border-color 0.2s ease;
}

.main-navigation__social-link:hover {
  transform: translateY(-1px);
  color: #7c5c3b;
  border-color: rgba(124, 92, 59, 0.25);
}

.main-navigation__social-icon,
.main-navigation__social-fallback {
  font-size: 1.05rem;
}

.main-navigation__social-fallback {
  font-weight: 700;
  letter-spacing: 0.04em;
}

.main-navigation__social-image {
  width: 1.15rem;
  height: 1.15rem;
  object-fit: contain;
}

@media (min-width: 992px) {
  .main-navigation__action {
    display: inline-flex;
  }

  .main-navigation--bottom-home {
    position: fixed;
    top: auto;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 40;
    background: rgba(0, 0, 0, 0.9);
    box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.28);
    backdrop-filter: blur(0);
  }

  .main-navigation--bottom-home .main-navigation__inner,
  .main-navigation--bottom-home .main-navigation__inner--fluid {
    background: transparent;
  }

  .main-navigation--bottom-home .main-navigation__inner {
    min-height: auto;
    padding-top: 0.75rem;
    padding-bottom: 1.125rem;
  }

  .main-navigation--bottom-home .main-navigation__list {
    justify-content: center;
  }

  .main-navigation--bottom-home.main-navigation--full-width .main-navigation__list {
    justify-content: flex-end;
  }

  .main-navigation--bottom-home .main-navigation__action {
    background: rgba(185, 146, 71, 0.95);
  }
}

@media (max-width: 991px) {
  .main-navigation__inner {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    padding-top: 0.8rem;
    padding-bottom: 0.8rem;
  }

  .main-navigation--header .main-navigation__nav,
  .main-navigation--header .main-navigation__action {
    display: none;
  }

  .main-navigation--header .main-navigation__mobile-toggle {
    display: inline-flex;
  }

  .main-navigation__inner--fluid {
    padding-inline: 1rem;
  }

  .main-navigation--header .main-navigation__brand-logo {
    max-width: 7.5rem;
    max-height: 3.75rem;
  }

  .main-navigation--side .main-navigation__inner {
    padding: 1.25rem 1rem;
    min-height: auto;
  }

  .main-navigation--side .main-navigation__list {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }
}
</style>
