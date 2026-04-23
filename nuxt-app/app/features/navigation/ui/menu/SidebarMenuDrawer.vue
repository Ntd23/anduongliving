<script setup lang="ts">
import { useMenu } from "~/features/cms/data/useMenu";
import type { ThemeCurrencyLink, ThemeCustomerBlock } from "~/features/navigation/data/useHeaderExtras";
import { useThemeOptions, type ThemeSocialLink } from "~/features/cms/data/useThemeOptions";
import { CMS_SIDEBAR_MENU_TOKEN } from "~~/shared/cms/constants";
import { cmsAppRoutes } from "~~/shared/routes/app";

const props = defineProps<{
  open: boolean;
  currencies?: ThemeCurrencyLink[];
  customer?: ThemeCustomerBlock | null;
}>();

const emit = defineEmits<{
  (event: "update:open", value: boolean): void;
}>();

const { locale } = useI18n();
const localePath = useLocalePath();
const route = useRoute();

const activeLocale = computed(() => locale.value);

const { data, error } = await useAsyncData(
  () => `sidebar-menu-${activeLocale.value}`,
  () => useMenu(CMS_SIDEBAR_MENU_TOKEN, activeLocale.value, { location: CMS_SIDEBAR_MENU_TOKEN }),
  { watch: [activeLocale] },
);

const { data: themeOptions } = await useAsyncData(
  () => `sidebar-theme-options-${activeLocale.value}`,
  () => useThemeOptions(activeLocale.value),
  { watch: [activeLocale] },
);

const menu = computed(() => data.value);
const menuError = computed(() => {
  if (!error.value) {
    return null;
  }

  return error.value.statusMessage || error.value.message || "Sidebar menu is not configured for this locale.";
});

const brandLabel = computed(() => {
  const title = themeOptions.value?.site_title;
  return typeof title === "string" && title.trim() ? title.trim() : "Anduong Living";
});
const brandHref = computed(() => localePath(cmsAppRoutes.home()));

const brandLogo = computed(() => {
  const logo = themeOptions.value?.logo_url;
  return typeof logo === "string" && logo ? logo : null;
});

const hotline = computed(() => themeOptions.value?.hotline?.trim() || "");
const email = computed(() => themeOptions.value?.email?.trim() || "");
const socialLinks = computed<ThemeSocialLink[]>(() =>
  Array.isArray(themeOptions.value?.social_links) ? themeOptions.value.social_links : [],
);
const currencies = computed(() => props.currencies || []);
const customer = computed(() => props.customer || null);
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

const closeDrawer = () => emit("update:open", false);

const resolveSocialIcon = (social: ThemeSocialLink) => {
  const source = `${social.icon || ""} ${social.name || ""}`.toLowerCase();

  return Object.entries(socialIconMap).find(([key]) => source.includes(key))?.[1] || null;
};

const socialLabel = (social: ThemeSocialLink) => {
  const value = (social.name || social.url || "").trim();
  return value ? value.slice(0, 1).toUpperCase() : "?";
};

const syncBodyLock = (isOpen: boolean) => {
  if (!import.meta.client) {
    return;
  }

  document.body.style.overflow = isOpen ? "hidden" : "";
};

watch(
  () => props.open,
  (isOpen) => syncBodyLock(isOpen),
  { immediate: true },
);

watch(
  () => route.fullPath,
  () => {
    if (props.open) {
      closeDrawer();
    }
  },
);

const onKeydown = (event: KeyboardEvent) => {
  if (event.key === "Escape" && props.open) {
    closeDrawer();
  }
};

onMounted(() => {
  window.addEventListener("keydown", onKeydown);
});

onBeforeUnmount(() => {
  syncBodyLock(false);
  window.removeEventListener("keydown", onKeydown);
});
</script>

<template>
  <Teleport to="body">
    <Transition name="sidebar-menu-overlay">
      <button
        v-if="open"
        type="button"
        class="sidebar-menu-drawer__overlay"
        aria-label="Close sidebar menu"
        @click="closeDrawer"
      />
    </Transition>

    <Transition name="sidebar-menu-panel">
      <aside
        v-if="open"
        class="sidebar-menu-drawer"
        aria-label="Sidebar menu"
      >
        <div class="sidebar-menu-drawer__header">
          <NuxtLink :to="brandHref" class="sidebar-menu-drawer__brand" @click="closeDrawer">
            <img
              v-if="brandLogo"
              :src="brandLogo"
              :alt="brandLabel"
              class="sidebar-menu-drawer__brand-logo"
            >
            <span
              v-else
              class="sidebar-menu-drawer__brand-text"
            >
              {{ brandLabel }}
            </span>
          </NuxtLink>
          <button
            type="button"
            class="sidebar-menu-drawer__close"
            aria-label="Close sidebar menu"
            @click="closeDrawer"
          >
            <Icon name="ph:x" />
          </button>
        </div>

        <p v-if="menuError" class="sidebar-menu-drawer__message sidebar-menu-drawer__message--error">
          {{ menuError }}
        </p>

        <nav
          v-else-if="menu?.items?.length"
          class="sidebar-menu-drawer__nav"
          aria-label="Sidebar navigation"
        >
          <ul class="sidebar-menu-drawer__list">
            <MenuTreeItem
              v-for="item in menu.items"
              :key="item.id"
              :item="item"
              variant="drawer"
            />
          </ul>
        </nav>

        <p v-else class="sidebar-menu-drawer__message">
          Sidebar menu has no items yet.
        </p>

        <div v-if="customer?.authenticated || customer?.loginUrl || customer?.registerUrl" class="sidebar-menu-drawer__account">
          <NuxtLink
            v-if="customer?.authenticated && overviewTo"
            :to="overviewTo"
            class="sidebar-menu-drawer__account-link"
            @click="closeDrawer"
          >
            <img
              v-if="customer.avatarUrl"
              :src="customer.avatarUrl"
              :alt="customer.name || 'Customer avatar'"
              class="sidebar-menu-drawer__account-avatar"
            >
            <Icon v-else name="ph:user-circle-fill" class="sidebar-menu-drawer__account-icon" />
            <span>{{ customer.name || 'My account' }}</span>
          </NuxtLink>
          <a
            v-else-if="customer?.authenticated && customer.overviewUrl"
            :href="customer.overviewUrl"
            class="sidebar-menu-drawer__account-link"
          >
            <img
              v-if="customer.avatarUrl"
              :src="customer.avatarUrl"
              :alt="customer.name || 'Customer avatar'"
              class="sidebar-menu-drawer__account-avatar"
            >
            <Icon v-else name="ph:user-circle-fill" class="sidebar-menu-drawer__account-icon" />
            <span>{{ customer.name || 'My account' }}</span>
          </a>

          <div v-else class="sidebar-menu-drawer__account-links">
            <NuxtLink v-if="loginTo" :to="loginTo" class="sidebar-menu-drawer__account-link" @click="closeDrawer">
              <Icon name="ph:sign-in" class="sidebar-menu-drawer__account-icon" />
              <span>Login</span>
            </NuxtLink>
            <a v-else-if="customer?.loginUrl" :href="customer.loginUrl" class="sidebar-menu-drawer__account-link">
              <Icon name="ph:sign-in" class="sidebar-menu-drawer__account-icon" />
              <span>Login</span>
            </a>
            <NuxtLink v-if="registerTo" :to="registerTo" class="sidebar-menu-drawer__account-link" @click="closeDrawer">
              <Icon name="ph:user-plus" class="sidebar-menu-drawer__account-icon" />
              <span>Register</span>
            </NuxtLink>
            <a v-else-if="customer?.registerUrl" :href="customer.registerUrl" class="sidebar-menu-drawer__account-link">
              <Icon name="ph:user-plus" class="sidebar-menu-drawer__account-icon" />
              <span>Register</span>
            </a>
          </div>
        </div>

        <div
          v-if="hotline || email || socialLinks.length || currencies.length || customer?.logoutUrl"
          class="sidebar-menu-drawer__footer"
        >
          <div v-if="currencies.length" class="sidebar-menu-drawer__currencies">
            <a
              v-for="currency in currencies"
              :key="currency.title"
              :href="currency.href"
              class="sidebar-menu-drawer__currency-link"
              :class="{ 'sidebar-menu-drawer__currency-link--active': currency.active }"
            >
              {{ currency.title }}
            </a>
          </div>

          <div v-if="hotline || email" class="sidebar-menu-drawer__contact">
            <a v-if="hotline" :href="`tel:${hotline}`" class="sidebar-menu-drawer__contact-link">
              {{ hotline }}
            </a>
            <a v-if="email" :href="`mailto:${email}`" class="sidebar-menu-drawer__contact-link">
              {{ email }}
            </a>
          </div>

          <NuxtLink
            v-if="customer?.authenticated && logoutTo"
            :to="logoutTo"
            class="sidebar-menu-drawer__contact-link"
            @click="closeDrawer"
          >
            Logout
          </NuxtLink>
          <a
            v-else-if="customer?.authenticated && customer.logoutUrl"
            :href="customer.logoutUrl"
            class="sidebar-menu-drawer__contact-link"
          >
            Logout
          </a>

          <div v-if="socialLinks.length" class="sidebar-menu-drawer__socials">
            <a
              v-for="social in socialLinks"
              :key="`${social.name}-${social.url}`"
              :href="social.url"
              class="sidebar-menu-drawer__social-link"
              :title="social.name || social.url"
              target="_blank"
              rel="noopener noreferrer"
            >
              <img
                v-if="social.image_url"
                :src="social.image_url"
                :alt="social.name || 'Social link'"
                class="sidebar-menu-drawer__social-image"
              >
              <Icon
                v-else-if="resolveSocialIcon(social)"
                :name="resolveSocialIcon(social) || ''"
                class="sidebar-menu-drawer__social-icon"
              />
              <span v-else class="sidebar-menu-drawer__social-fallback">
                {{ socialLabel(social) }}
              </span>
            </a>
          </div>
        </div>
      </aside>
    </Transition>
  </Teleport>
</template>

<style scoped>
.sidebar-menu-drawer__overlay {
  position: fixed;
  inset: 0;
  z-index: 79;
  border: 0;
  background: rgba(16, 12, 9, 0.6);
  backdrop-filter: blur(4px);
}

.sidebar-menu-drawer {
  position: fixed;
  top: 0;
  right: 0;
  z-index: 80;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  width: min(24rem, 100vw);
  height: 100vh;
  padding: 1.5rem;
  overflow-y: auto;
  background:
    linear-gradient(180deg, rgba(20, 14, 11, 0.98), rgba(38, 25, 18, 0.98));
  box-shadow: -24px 0 60px rgba(0, 0, 0, 0.28);
}

.sidebar-menu-drawer__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.sidebar-menu-drawer__brand {
  display: inline-flex;
  align-items: center;
  color: #fff6e8;
  text-decoration: none;
}

.sidebar-menu-drawer__brand-logo {
  display: block;
  width: auto;
  max-width: 8rem;
  max-height: 4rem;
  object-fit: contain;
}

.sidebar-menu-drawer__brand-text {
  font-family: "Iowan Old Style", "Palatino Linotype", "Book Antiqua", Georgia, serif;
  font-size: 1.2rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.sidebar-menu-drawer__close {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border: 1px solid rgba(255, 246, 232, 0.16);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.04);
  color: #fff6e8;
  font-size: 1.1rem;
}

.sidebar-menu-drawer__nav {
  flex: 1 1 auto;
}

.sidebar-menu-drawer__list {
  margin: 0;
  padding: 0;
  list-style: none;
}

.sidebar-menu-drawer__message {
  margin: 0;
  color: rgba(255, 246, 232, 0.68);
  font-size: 0.95rem;
}

.sidebar-menu-drawer__message--error {
  color: #efb4a8;
}

.sidebar-menu-drawer__account {
  padding-top: 0.25rem;
}

.sidebar-menu-drawer__account-links {
  display: grid;
  gap: 0.75rem;
}

.sidebar-menu-drawer__account-link {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  color: rgba(255, 246, 232, 0.92);
  font-size: 0.95rem;
  font-weight: 600;
  text-decoration: none;
}

.sidebar-menu-drawer__account-avatar {
  width: 1.45rem;
  height: 1.45rem;
  border-radius: 999px;
  object-fit: cover;
}

.sidebar-menu-drawer__account-icon {
  font-size: 1.1rem;
}

.sidebar-menu-drawer__footer {
  display: grid;
  gap: 1rem;
  padding-top: 1.25rem;
  border-top: 1px solid rgba(255, 246, 232, 0.1);
}

.sidebar-menu-drawer__currencies {
  display: flex;
  flex-wrap: wrap;
  gap: 0.55rem;
}

.sidebar-menu-drawer__currency-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2rem;
  padding: 0 0.8rem;
  border: 1px solid rgba(255, 246, 232, 0.14);
  border-radius: 999px;
  color: rgba(255, 246, 232, 0.84);
  font-size: 0.76rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-decoration: none;
  text-transform: uppercase;
}

.sidebar-menu-drawer__currency-link--active {
  border-color: rgba(210, 175, 113, 0.42);
  color: #e7d3b1;
}

.sidebar-menu-drawer__contact {
  display: grid;
  gap: 0.5rem;
}

.sidebar-menu-drawer__contact-link {
  color: rgba(255, 246, 232, 0.88);
  font-size: 0.95rem;
  text-decoration: none;
}

.sidebar-menu-drawer__contact-link:hover {
  color: #d2af71;
}

.sidebar-menu-drawer__socials {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.sidebar-menu-drawer__social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border: 1px solid rgba(255, 246, 232, 0.14);
  border-radius: 999px;
  color: #fff6e8;
  text-decoration: none;
}

.sidebar-menu-drawer__social-image {
  width: 1rem;
  height: 1rem;
  object-fit: contain;
}

.sidebar-menu-drawer__social-icon,
.sidebar-menu-drawer__social-fallback {
  font-size: 1rem;
}

.sidebar-menu-overlay-enter-active,
.sidebar-menu-overlay-leave-active,
.sidebar-menu-panel-enter-active,
.sidebar-menu-panel-leave-active {
  transition: all 0.25s ease;
}

.sidebar-menu-overlay-enter-from,
.sidebar-menu-overlay-leave-to {
  opacity: 0;
}

.sidebar-menu-panel-enter-from,
.sidebar-menu-panel-leave-to {
  transform: translateX(100%);
}
</style>



