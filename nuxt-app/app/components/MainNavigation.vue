<script setup lang="ts">
import MobileMenuCollapse from "~/components/menu/MobileMenuCollapse.vue";
import SidebarMenuDrawer from "~/components/menu/SidebarMenuDrawer.vue";
import { useMenu } from "~/composables/useMenu";
import { useThemeOptions, type ThemeSocialLink } from "~/composables/useThemeOptions";
import { CMS_MAIN_MENU_TOKEN, cmsAppRoutes } from "~~/shared/cms-routing";

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
const sidebarMenuOpen = ref(false);

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
const wrapperClass = computed(() =>
  props.fullWidth ? "main-navigation__inner main-navigation__inner--fluid" : "container main-navigation__inner",
);
const showMobileToggle = computed(() => props.variant === "header" || props.variant === "side");
const showOffcanvasTrigger = computed(() => props.variant === "header" && props.fullWidth);

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
    sidebarMenuOpen.value = false;
  },
);
</script>

<template>
  <header
    class="main-navigation"
    :class="{
      'main-navigation--side': variant === 'side',
      'main-navigation--header': variant === 'header',
      'main-navigation--full-width': fullWidth,
    }"
  >
    <div :class="wrapperClass">
      <div class="main-navigation__brand-block">
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
      </div>

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

      <div class="main-navigation__actions">
        <a
          v-if="showHeaderButton"
          :href="headerButtonUrl"
          class="main-navigation__action"
        >
          {{ headerButtonLabel }}
        </a>

        <button
          v-if="showOffcanvasTrigger"
          type="button"
          class="main-navigation__offcanvas-trigger"
          aria-label="Open sidebar menu"
          :aria-expanded="sidebarMenuOpen ? 'true' : 'false'"
          @click="sidebarMenuOpen = true"
        >
          <Icon name="ph:dots-nine-bold" />
        </button>

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
      </div>

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

    <SidebarMenuDrawer
      v-if="showOffcanvasTrigger"
      v-model:open="sidebarMenuOpen"
    />
  </header>
</template>

<style scoped>
.main-navigation {
  position: sticky;
  top: 0;
  z-index: 30;
  border-bottom: 1px solid rgba(50, 35, 25, 0.08);
  background:
    linear-gradient(180deg, rgba(252, 248, 241, 0.96), rgba(250, 244, 235, 0.92));
  backdrop-filter: blur(16px);
}

.main-navigation__inner {
  display: flex;
  align-items: center;
  gap: 1.75rem;
  min-height: 5.85rem;
}

.main-navigation__inner--fluid {
  width: 100%;
  padding-inline: 2rem;
}

.main-navigation__brand-block {
  flex: 0 0 auto;
}

.main-navigation__brand {
  display: inline-flex;
  align-items: center;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: 1.9rem;
  line-height: 0.9;
  text-decoration: none;
}

.main-navigation__brand-logo {
  display: block;
  width: auto;
  max-width: 9.75rem;
  max-height: 5rem;
  object-fit: contain;
}

.main-navigation__brand-text {
  display: inline-block;
}

.main-navigation__nav {
  flex: 1 1 auto;
  min-width: 0;
}

.main-navigation__list {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.85rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.main-navigation__actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.9rem;
  flex: 0 0 auto;
}

.main-navigation__error {
  margin: 0;
  color: #8b3a2f;
  font-size: 0.88rem;
  font-weight: 600;
}

.main-navigation__action {
  display: none;
  align-items: center;
  justify-content: center;
  min-height: 3.2rem;
  padding: 0.9rem 1.55rem;
  border: 1px solid rgba(93, 68, 47, 0.14);
  border-radius: 999px;
  background: linear-gradient(180deg, #8f7659 0%, #72634d 100%);
  color: #fffdf8;
  font-size: 0.76rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-decoration: none;
  text-transform: uppercase;
  box-shadow: 0 18px 40px rgba(62, 43, 31, 0.14);
}

.main-navigation__action:hover {
  background: linear-gradient(180deg, #a17f5c 0%, #7d6950 100%);
}

.main-navigation__mobile-toggle,
.main-navigation__offcanvas-trigger {
  display: none;
  align-items: center;
  justify-content: center;
  width: 3rem;
  height: 3rem;
  border: 1px solid rgba(50, 35, 25, 0.1);
  border-radius: 999px;
  background: rgba(255, 252, 247, 0.86);
  color: var(--retreat-ink);
  font-size: 1.15rem;
}

.main-navigation__mobile-toggle:hover,
.main-navigation__offcanvas-trigger:hover {
  border-color: rgba(167, 122, 84, 0.28);
  color: var(--retreat-clay);
}

.main-navigation--side {
  position: static;
  display: flex;
  height: 100%;
  min-height: 100%;
  border-bottom: 0;
  background:
    linear-gradient(180deg, rgba(250, 246, 239, 0.98), rgba(243, 234, 220, 0.96));
  backdrop-filter: none;
}

.main-navigation--side .main-navigation__inner {
  flex: 1 1 auto;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  gap: 2.5rem;
  min-height: 100%;
  padding: 2.5rem 1.75rem 2rem;
}

.main-navigation--side .main-navigation__brand {
  font-size: 2.25rem;
}

.main-navigation--side .main-navigation__brand-logo {
  max-width: 10.5rem;
  max-height: 5.25rem;
}

.main-navigation--side .main-navigation__nav,
.main-navigation--side .main-navigation__list {
  width: 100%;
}

.main-navigation__list--side {
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  gap: 0.45rem;
}

.main-navigation__meta {
  margin-top: auto;
  width: 100%;
  padding-top: 1.5rem;
  border-top: 1px solid rgba(50, 35, 25, 0.08);
}

.main-navigation__socials {
  display: flex;
  flex-wrap: wrap;
  gap: 0.85rem;
}

.main-navigation__social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.85rem;
  height: 2.85rem;
  border: 1px solid rgba(50, 35, 25, 0.1);
  border-radius: 999px;
  background: rgba(255, 252, 247, 0.74);
  color: #6e5843;
  text-decoration: none;
}

.main-navigation__social-link:hover {
  border-color: rgba(167, 122, 84, 0.26);
  color: var(--retreat-clay);
  transform: translateY(-1px);
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
  .main-navigation--full-width .main-navigation__offcanvas-trigger {
    display: inline-flex;
  }

  .main-navigation__action {
    display: inline-flex;
  }
}

@media (max-width: 991px) {
  .main-navigation__inner {
    justify-content: space-between;
    min-height: 5rem;
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
  }

  .main-navigation--header .main-navigation__nav,
  .main-navigation--header .main-navigation__action {
    display: none;
  }

  .main-navigation--header .main-navigation__offcanvas-trigger {
    display: none;
  }

  .main-navigation--header .main-navigation__mobile-toggle {
    display: inline-flex;
  }

  .main-navigation--side .main-navigation__inner {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    min-height: auto;
    gap: 1rem;
  }

  .main-navigation--side .main-navigation__nav,
  .main-navigation--side .main-navigation__meta {
    display: none;
  }

  .main-navigation--side .main-navigation__mobile-toggle {
    display: inline-flex;
  }

  .main-navigation__inner--fluid {
    padding-inline: 1rem;
  }

  .main-navigation__brand-logo {
    max-width: 7.6rem;
    max-height: 3.9rem;
  }

  .main-navigation--side .main-navigation__brand {
    font-size: 1.8rem;
  }

  .main-navigation--side .main-navigation__inner {
    padding: 1.25rem 1rem;
  }
}
</style>
