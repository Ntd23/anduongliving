<script setup lang="ts">
import MobileMenuCollapse from "~/components/menu/MobileMenuCollapse.vue";
import SidebarMenuDrawer from "~/components/menu/SidebarMenuDrawer.vue";
import { useHeaderExtras } from "~/composables/useHeaderExtras";
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
const isScrolled = ref(false);

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

const { data: headerExtras } = await useAsyncData(
  () => `theme-header-extras-${props.variant}-${activeLocale.value}`,
  () => useHeaderExtras(activeLocale.value),
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
const currencies = computed(() => headerExtras.value?.currencies || []);
const customer = computed(() => headerExtras.value?.customer || null);
const headerButtonLabel = computed(() => themeOptions.value?.header_button_label?.trim() || "");
const headerButtonUrl = computed(() => themeOptions.value?.header_button_url?.trim() || "");
const showHeaderButton = computed(() =>
  props.variant === "header" && !props.fullWidth && !!headerButtonLabel.value && !!headerButtonUrl.value,
);
const wrapperClass = computed(() => {
  if (props.variant === "header") {
    return "main-navigation__inner main-navigation__inner--fluid";
  }

  return props.fullWidth
    ? "main-navigation__inner main-navigation__inner--fluid"
    : "container main-navigation__inner";
});
const showMobileToggle = computed(() => props.variant === "header" || props.variant === "side");
const showOffcanvasTrigger = computed(() => props.variant === "header" && props.fullWidth);
const loginTo = computed(() =>
  customer.value?.loginUrl?.startsWith("/") ? localePath(customer.value.loginUrl) : null,
);
const registerTo = computed(() =>
  customer.value?.registerUrl?.startsWith("/") ? localePath(customer.value.registerUrl) : null,
);
const overviewTo = computed(() =>
  customer.value?.overviewUrl?.startsWith("/") ? localePath(customer.value.overviewUrl) : null,
);
const logoutTo = computed(() =>
  customer.value?.logoutUrl?.startsWith("/") ? localePath(customer.value.logoutUrl) : null,
);

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

const syncScrolledState = () => {
  if (!import.meta.client || props.variant !== "header") {
    return;
  }

  isScrolled.value = window.scrollY > 24;
};

onMounted(() => {
  syncScrolledState();
  window.addEventListener("scroll", syncScrolledState, { passive: true });
});

onBeforeUnmount(() => {
  if (!import.meta.client || props.variant !== "header") {
    return;
  }

  window.removeEventListener("scroll", syncScrolledState);
});
</script>

<template>
  <header
    class="main-navigation"
    :class="{
      'main-navigation--side': variant === 'side',
      'main-navigation--header': variant === 'header',
      'main-navigation--full-width': fullWidth,
      'main-navigation--scrolled': isScrolled,
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

      <div v-if="variant === 'side' && (customer?.authenticated || customer?.loginUrl || customer?.registerUrl)" class="main-navigation__side-account">
        <NuxtLink
          v-if="customer?.authenticated && overviewTo"
          :to="overviewTo"
          class="main-navigation__side-account-link"
        >
          <img
            v-if="customer.avatarUrl"
            :src="customer.avatarUrl"
            :alt="customer.name || 'Customer avatar'"
            class="main-navigation__side-account-avatar"
          >
          <Icon v-else name="ph:user-circle-fill" class="main-navigation__side-account-icon" />
          <span>{{ customer.name || "My account" }}</span>
        </NuxtLink>
        <a
          v-else-if="customer?.authenticated && customer.overviewUrl"
          :href="customer.overviewUrl"
          class="main-navigation__side-account-link"
        >
          <img
            v-if="customer.avatarUrl"
            :src="customer.avatarUrl"
            :alt="customer.name || 'Customer avatar'"
            class="main-navigation__side-account-avatar"
          >
          <Icon v-else name="ph:user-circle-fill" class="main-navigation__side-account-icon" />
          <span>{{ customer.name || "My account" }}</span>
        </a>

        <div v-else class="main-navigation__side-account-links">
          <NuxtLink v-if="loginTo" :to="loginTo" class="main-navigation__side-account-link">Login</NuxtLink>
          <a v-else-if="customer?.loginUrl" :href="customer.loginUrl" class="main-navigation__side-account-link">Login</a>
          <NuxtLink v-if="registerTo" :to="registerTo" class="main-navigation__side-account-link">Register</NuxtLink>
          <a v-else-if="customer?.registerUrl" :href="customer.registerUrl" class="main-navigation__side-account-link">Register</a>
        </div>
      </div>

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
        <div v-if="currencies.length" class="main-navigation__currencies">
          <a
            v-for="currency in currencies"
            :key="currency.title"
            :href="currency.href"
            class="main-navigation__currency-link"
            :class="{ 'main-navigation__currency-link--active': currency.active }"
          >
            {{ currency.title }}
          </a>
        </div>

        <NuxtLink
          v-if="customer?.authenticated && logoutTo"
          :to="logoutTo"
          class="main-navigation__logout-link"
        >
          Logout
        </NuxtLink>
        <a
          v-else-if="customer?.authenticated && customer.logoutUrl"
          :href="customer.logoutUrl"
          class="main-navigation__logout-link"
        >
          Logout
        </a>

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
      :currencies="currencies"
      :customer="customer"
    />

    <SidebarMenuDrawer
      v-if="showOffcanvasTrigger"
      v-model:open="sidebarMenuOpen"
      :currencies="currencies"
      :customer="customer"
    />
  </header>
</template>

<style scoped>
.main-navigation {
  position: relative;
  z-index: 80;
  background: transparent;
  pointer-events: auto;
  isolation: isolate;
}

.main-navigation--header {
  padding-top: 1rem;
}

.main-navigation--header.main-navigation--scrolled {
  min-height: 6.85rem;
}

.main-navigation--header.main-navigation--scrolled .main-navigation__inner {
  position: fixed;
  top: 0.0rem;
  z-index: 81;
  width: 100%;;
}

.main-navigation__inner {
  display: flex;
  align-items: center;
  gap: 1.75rem;
  min-height: 5.85rem;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.66));
  box-shadow:
    0 18px 40px rgba(56, 41, 30, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.62);
  backdrop-filter: blur(22px) saturate(135%);
  pointer-events: auto;
}

.main-navigation__inner--fluid {

}

.main-navigation__brand-block {
  flex: 0 0 auto;
  padding: 0 50px;
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
  max-width: 9.4rem;
  max-height: 4.4rem;
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
  gap: 0.45rem;
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
  min-height: 2.95rem;
  padding: 0.85rem 1.35rem;
  border: 1px solid rgba(120, 105, 87, 0.16);
  border-radius: 999px;
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.72));
  color: var(--retreat-ink);
  font-size: 0.76rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-decoration: none;
  text-transform: uppercase;
  box-shadow: 0 12px 28px rgba(56, 41, 30, 0.08);
}

.main-navigation__action:hover {
  border-color: rgba(167, 122, 84, 0.24);
  color: var(--retreat-clay);
  transform: translateY(-1px);
}

.main-navigation__mobile-toggle,
.main-navigation__offcanvas-trigger {
  display: none;
  align-items: center;
  justify-content: center;
  width: 3rem;
  height: 3rem;
  border: 1px solid rgba(120, 105, 87, 0.14);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.74);
  color: var(--retreat-ink);
  font-size: 1.15rem;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.66);
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
  padding-top: 0;
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
  border: 0;
  border-radius: 0;
  background: transparent;
  box-shadow: none;
  backdrop-filter: none;
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
  display: grid;
  gap: 1rem;
  padding-top: 1.5rem;
  border-top: 1px solid rgba(50, 35, 25, 0.08);
}

.main-navigation__side-account {
  width: 100%;
}

.main-navigation__side-account-links {
  display: grid;
  gap: 0.6rem;
}

.main-navigation__side-account-link,
.main-navigation__logout-link,
.main-navigation__currency-link {
  color: var(--retreat-ink);
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-decoration: none;
  text-transform: uppercase;
}

.main-navigation__side-account-link {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
}

.main-navigation__side-account-avatar {
  width: 1.55rem;
  height: 1.55rem;
  border-radius: 999px;
  object-fit: cover;
}

.main-navigation__side-account-icon {
  font-size: 1.15rem;
}

.main-navigation__currencies {
  display: flex;
  flex-wrap: wrap;
  gap: 0.55rem;
}

.main-navigation__currency-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2rem;
  padding: 0 0.8rem;
  border: 1px solid rgba(50, 35, 25, 0.1);
  border-radius: 999px;
  background: rgba(255, 252, 247, 0.74);
}

.main-navigation__currency-link--active {
  border-color: rgba(167, 122, 84, 0.28);
  color: var(--retreat-clay);
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

  .main-navigation--header .main-navigation__brand-block {
    flex: 0 0 auto;
    min-width: 10rem;
    padding: 0 50px;
  }

  .main-navigation--header .main-navigation__actions {
    min-width: 10rem;
  }
}

@media (max-width: 991px) {
  .main-navigation--header {
    padding-top: 0.6rem;
  }

  .main-navigation--header.main-navigation--scrolled {
    min-height: 5.2rem;
  }


  .main-navigation {
    z-index: 80;
  }

  .main-navigation__inner {
    justify-content: space-between;
    min-height: 4.6rem;
    gap: 1rem;
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
    width: calc(100% - 1rem);
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
