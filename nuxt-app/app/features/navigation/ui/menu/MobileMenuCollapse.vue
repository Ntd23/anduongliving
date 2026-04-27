<script setup lang="ts">
import type { MenuData } from "~/features/cms/data/useMenu";
import type { ThemeCurrencyLink, ThemeCustomerBlock } from "~/features/navigation/data/useHeaderExtras";
import type { ThemeSocialLink } from "~/features/cms/data/useThemeOptions";

const props = defineProps<{
  open: boolean;
  menu?: MenuData | null;
  menuError?: string | null;
  brandHref: string;
  brandLabel: string;
  brandLogo?: string | null;
  hotline?: string | null;
  email?: string | null;
  socialLinks?: ThemeSocialLink[];
  currencies?: ThemeCurrencyLink[];
  customer?: ThemeCustomerBlock | null;
}>();

const emit = defineEmits<{
  (event: "update:open", value: boolean): void;
}>();

const { locale, locales } = useI18n();
const localePath = useLocalePath();
const switchLocalePath = useSwitchLocalePath();
const route = useRoute();

const languageLabels: Record<string, string> = {
  vi: "Vietnam",
  en: "English",
  ja: "Japanese",
};

const languages = computed(() =>
  (locales.value || []).map((localeOption) => {
    const code = typeof localeOption === "string" ? localeOption : localeOption.code;

    return {
      code,
      label: languageLabels[code] || code.toUpperCase(),
      to: switchLocalePath(code) || "/",
      active: locale.value === code,
    };
  }),
);

const hasLanguages = computed(() => languages.value.length > 1);
const hasSocialLinks = computed(() => (props.socialLinks || []).length > 0);
const hasCurrencies = computed(() => (props.currencies || []).length > 0);
const hasCustomerLinks = computed(() =>
  Boolean(props.customer?.authenticated || props.customer?.loginUrl || props.customer?.registerUrl),
);
const loginTo = computed(() =>
  props.customer?.loginUrl?.startsWith("/") ? localePath(props.customer.loginUrl) : null,
);
const registerTo = computed(() =>
  props.customer?.registerUrl?.startsWith("/") ? localePath(props.customer.registerUrl) : null,
);
const overviewTo = computed(() =>
  props.customer?.overviewUrl?.startsWith("/") ? localePath(props.customer.overviewUrl) : null,
);
const logoutTo = computed(() =>
  props.customer?.logoutUrl?.startsWith("/") ? localePath(props.customer.logoutUrl) : null,
);

const closeMenu = () => emit("update:open", false);

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
      closeMenu();
    }
  },
);

const onKeydown = (event: KeyboardEvent) => {
  if (event.key === "Escape" && props.open) {
    closeMenu();
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
    <Transition name="mobile-menu-overlay">
      <button
        v-if="open"
        type="button"
        class="mobile-menu-collapse__overlay"
        aria-label="Close mobile menu"
        @click="closeMenu"
      />
    </Transition>

    <Transition name="mobile-menu-panel">
      <div v-if="open" class="mobile-menu-collapse">
        <div class="mobile-menu-collapse__header">
          <NuxtLink :to="brandHref" class="mobile-menu-collapse__brand" @click="closeMenu">
            <img
              v-if="brandLogo"
              :src="brandLogo"
              :alt="brandLabel"
              class="mobile-menu-collapse__brand-logo"
            >
            <span v-else class="mobile-menu-collapse__brand-text">
              {{ brandLabel }}
            </span>
          </NuxtLink>

          <button
            type="button"
            class="mobile-menu-collapse__close"
            aria-label="Close"
            @click="closeMenu"
          >
            <Icon name="ph:x" />
          </button>
        </div>

        <div class="mobile-menu-collapse__body">
          <section class="mobile-menu-collapse__section mobile-menu-collapse__section--menu">
            <p v-if="menuError" class="mobile-menu-collapse__message mobile-menu-collapse__message--error">
              {{ menuError }}
            </p>

            <ul v-else-if="menu?.items?.length" class="mobile-menu-collapse__list">
              <MenuTreeItem
                v-for="item in menu.items"
                :key="item.id"
                :item="item"
                variant="drawer"
              />
            </ul>

            <p v-else class="mobile-menu-collapse__message">
              Main menu is not configured for this locale.
            </p>
          </section>

          <section v-if="hasLanguages" class="mobile-menu-collapse__section">
            <ul class="mobile-menu-collapse__plain-list">
              <li v-for="item in languages" :key="item.code">
                <NuxtLink
                  :to="item.to"
                  class="mobile-menu-collapse__plain-link"
                  :class="{ 'mobile-menu-collapse__plain-link--active': item.active }"
                  @click="closeMenu"
                >
                  {{ item.label }}
                </NuxtLink>
              </li>
            </ul>
          </section>

          <section v-if="hasCurrencies" class="mobile-menu-collapse__section">
            <ul class="mobile-menu-collapse__plain-list">
              <li v-for="currency in currencies" :key="currency.title">
                <a
                  :href="currency.href"
                  class="mobile-menu-collapse__plain-link"
                  :class="{ 'mobile-menu-collapse__plain-link--active': currency.active }"
                >
                  {{ currency.title }}
                </a>
              </li>
            </ul>
          </section>

          <section v-if="hasCustomerLinks" class="mobile-menu-collapse__section">
            <ul class="mobile-menu-collapse__plain-list">
              <li v-if="customer?.authenticated && overviewTo">
                <NuxtLink :to="overviewTo" class="mobile-menu-collapse__plain-link mobile-menu-collapse__plain-link--account" @click="closeMenu">
                  <img
                    v-if="customer.avatarUrl"
                    :src="customer.avatarUrl"
                    :alt="customer.name || 'Customer avatar'"
                    class="mobile-menu-collapse__account-avatar"
                  >
                  <Icon v-else name="ph:user-circle-fill" class="mobile-menu-collapse__account-icon" />
                  <span>{{ customer.name || 'My account' }}</span>
                </NuxtLink>
              </li>
              <li v-else-if="customer?.authenticated && customer.overviewUrl">
                <a :href="customer.overviewUrl" class="mobile-menu-collapse__plain-link mobile-menu-collapse__plain-link--account">
                  <img
                    v-if="customer.avatarUrl"
                    :src="customer.avatarUrl"
                    :alt="customer.name || 'Customer avatar'"
                    class="mobile-menu-collapse__account-avatar"
                  >
                  <Icon v-else name="ph:user-circle-fill" class="mobile-menu-collapse__account-icon" />
                  <span>{{ customer.name || 'My account' }}</span>
                </a>
              </li>
              <li v-else-if="loginTo">
                <NuxtLink :to="loginTo" class="mobile-menu-collapse__plain-link mobile-menu-collapse__plain-link--account" @click="closeMenu">
                  <Icon name="ph:sign-in" class="mobile-menu-collapse__account-icon" />
                  <span>Login</span>
                </NuxtLink>
              </li>
              <li v-else-if="customer?.loginUrl">
                <a :href="customer.loginUrl" class="mobile-menu-collapse__plain-link mobile-menu-collapse__plain-link--account">
                  <Icon name="ph:sign-in" class="mobile-menu-collapse__account-icon" />
                  <span>Login</span>
                </a>
              </li>
              <li v-if="!customer?.authenticated && registerTo">
                <NuxtLink :to="registerTo" class="mobile-menu-collapse__plain-link" @click="closeMenu">
                  Register
                </NuxtLink>
              </li>
              <li v-else-if="!customer?.authenticated && customer?.registerUrl">
                <a :href="customer.registerUrl" class="mobile-menu-collapse__plain-link">
                  Register
                </a>
              </li>
              <li v-if="customer?.authenticated && logoutTo">
                <NuxtLink :to="logoutTo" class="mobile-menu-collapse__plain-link" @click="closeMenu">
                  Logout
                </NuxtLink>
              </li>
              <li v-else-if="customer?.authenticated && customer?.logoutUrl">
                <a :href="customer.logoutUrl" class="mobile-menu-collapse__plain-link">
                  Logout
                </a>
              </li>
            </ul>
          </section>

          <section v-if="hasSocialLinks" class="mobile-menu-collapse__section">
            <div class="mobile-menu-collapse__socials">
              <a
                v-for="social in socialLinks"
                :key="`${social.name}-${social.url}`"
                :href="social.url"
                class="mobile-menu-collapse__social-link"
                :title="social.name || social.url"
                target="_blank"
                rel="noopener noreferrer"
              >
                <img
                  v-if="social.image_url"
                  :src="social.image_url"
                  :alt="social.name || 'Social link'"
                  class="mobile-menu-collapse__social-image"
                >
                <Icon
                  v-else-if="resolveSocialIcon(social)"
                  :name="resolveSocialIcon(social) || ''"
                  class="mobile-menu-collapse__social-icon"
                />
                <span v-else class="mobile-menu-collapse__social-fallback">
                  {{ socialLabel(social) }}
                </span>
              </a>
            </div>
          </section>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.mobile-menu-collapse__overlay {
  position: fixed;
  inset: 0;
  z-index: 58;
  border: 0;
  background: rgba(16, 11, 8, 0.48);
  backdrop-filter: blur(3px);
  touch-action: none;
}

.mobile-menu-collapse {
  position: fixed;
  top: 0.8rem;
  right: 0.8rem;
  bottom: 0.8rem;
  z-index: 59;
  width: min(26rem, calc(100vw - 1.1rem));
  overflow-y: auto;
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 1.9rem;
  background: linear-gradient(180deg, rgba(39, 28, 21, 0.9), rgba(28, 20, 16, 0.94));
  color: rgba(255, 249, 241, 0.92);
  box-shadow: -24px 0 60px rgba(16, 11, 8, 0.22);
  backdrop-filter: blur(22px) saturate(125%);
  overscroll-behavior: contain;
  -webkit-overflow-scrolling: touch;
  touch-action: pan-y;
}

.mobile-menu-collapse__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.1rem 1.1rem 0.95rem;
  border-bottom: 1px solid rgba(255, 249, 241, 0.08);
}

.mobile-menu-collapse__brand {
  display: inline-flex;
  align-items: center;
  color: #fff9f1;
  text-decoration: none;
}

.mobile-menu-collapse__brand-logo {
  display: block;
  width: auto;
  max-width: 8rem;
  max-height: 4rem;
  object-fit: contain;
}

.mobile-menu-collapse__brand-text {
  font-family: var(--font-display);
  font-size: 1.8rem;
  line-height: 0.95;
}

.mobile-menu-collapse__close {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.75rem;
  height: 2.75rem;
  border: 1px solid rgba(255, 249, 241, 0.12);
  border-radius: 999px;
  background: rgba(255, 249, 241, 0.06);
  color: #fff9f1;
  font-size: 1.1rem;
}

.mobile-menu-collapse__body {
  display: grid;
  gap: 1rem;
  padding: 0.95rem 1.1rem 1.4rem;
}

.mobile-menu-collapse__section {
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(255, 249, 241, 0.08);
}

.mobile-menu-collapse__section--menu {
  padding-top: 0.1rem;
}

.mobile-menu-collapse__section:last-child {
  border-bottom: 0;
  padding-bottom: 0;
}

.mobile-menu-collapse__list,
.mobile-menu-collapse__plain-list {
  margin: 0;
  padding: 0;
  list-style: none;
}

.mobile-menu-collapse__plain-list li + li {
  margin-top: 0.35rem;
}

.mobile-menu-collapse__plain-link {
  display: block;
  min-height: 2.6rem;
  padding: 0.6rem 0;
  color: rgba(255, 249, 241, 0.84);
  font-size: 1rem;
  font-weight: bold;
  letter-spacing: 0.16em;
  text-decoration: none;
  text-transform: uppercase;
}

.mobile-menu-collapse__plain-link--account {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
}

.mobile-menu-collapse__account-avatar {
  width: 1.35rem;
  height: 1.35rem;
  border-radius: 999px;
  object-fit: cover;
}

.mobile-menu-collapse__account-icon {
  font-size: 1.1rem;
}

.mobile-menu-collapse__plain-link--active {
  color: #d6c09b;
}

.mobile-menu-collapse__message {
  margin: 0;
  color: rgba(255, 249, 241, 0.72);
  line-height: 1.7;
}

.mobile-menu-collapse__message--error {
  color: #f0afa5;
}

.mobile-menu-collapse__socials {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.mobile-menu-collapse__social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.6rem;
  height: 2.6rem;
  border: 1px solid rgba(255, 249, 241, 0.12);
  border-radius: 999px;
  background: rgba(255, 249, 241, 0.04);
  color: rgba(255, 249, 241, 0.86);
  text-decoration: none;
}

.mobile-menu-collapse__social-image {
  width: 1rem;
  height: 1rem;
  object-fit: contain;
}

.mobile-menu-collapse__social-icon,
.mobile-menu-collapse__social-fallback {
  font-size: 1rem;
}

.mobile-menu-overlay-enter-active,
.mobile-menu-overlay-leave-active,
.mobile-menu-panel-enter-active,
.mobile-menu-panel-leave-active {
  transition: all 0.22s ease;
}

.mobile-menu-overlay-enter-from,
.mobile-menu-overlay-leave-to {
  opacity: 0;
}

.mobile-menu-panel-enter-from,
.mobile-menu-panel-leave-to {
  opacity: 0;
  transform: translateX(18px);
}

@media (min-width: 992px) {
  .mobile-menu-collapse,
  .mobile-menu-collapse__overlay {
    display: none;
  }
}

@media (max-width: 575px) {
  .mobile-menu-collapse {
    top: 0.55rem;
    right: 0.55rem;
    bottom: 0.55rem;
    left: 0.55rem;
    width: auto;
    border-radius: 1.5rem;
  }

  .mobile-menu-collapse__header,
  .mobile-menu-collapse__body {
    padding-inline: 0.95rem;
  }
}
</style>



