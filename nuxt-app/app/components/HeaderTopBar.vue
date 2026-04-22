<script setup lang="ts">
import { useHeaderExtras } from "~/composables/useHeaderExtras";
import { useThemeOptions, type ThemeSocialLink } from "~/composables/useThemeOptions";

const props = withDefaults(defineProps<{
  fullWidth?: boolean;
}>(), {
  fullWidth: false,
});

const { locale, locales } = useI18n();
const localePath = useLocalePath();

const activeLocale = computed(() => locale.value);

const { data: themeOptions } = await useAsyncData(
  () => `header-top-theme-options-${props.fullWidth ? "fluid" : "default"}-${activeLocale.value}`,
  () => useThemeOptions(activeLocale.value),
  { watch: [activeLocale] },
);

const { data: headerExtras } = await useAsyncData(
  () => `header-top-extras-${props.fullWidth ? "fluid" : "default"}-${activeLocale.value}`,
  () => useHeaderExtras(activeLocale.value),
  { watch: [activeLocale] },
);

const isEnabled = computed(() => {
  const value = themeOptions.value?.header_top_enabled;

  if (typeof value === "boolean") {
    return value;
  }

  if (typeof value === "string") {
    return value !== "0" && value.toLowerCase() !== "false" && value.toLowerCase() !== "no";
  }

  return true;
});

const openingHours = computed(() => themeOptions.value?.opening_hours?.trim() || "");
const hotline = computed(() => themeOptions.value?.hotline?.trim() || "");
const socialLinks = computed<ThemeSocialLink[]>(() =>
  Array.isArray(themeOptions.value?.social_links) ? themeOptions.value.social_links : [],
);
const currencies = computed(() => headerExtras.value?.currencies || []);
const currentCurrency = computed(() => currencies.value.find((item) => item.active) || currencies.value[0] || null);
const otherCurrencies = computed(() => currencies.value.filter((item) => !item.active));
const customer = computed(() => headerExtras.value?.customer || null);
const hasCurrencySwitcher = computed(() => currencies.value.length > 0);
const hasCustomerBlock = computed(() =>
  Boolean(customer.value?.authenticated || customer.value?.loginUrl || customer.value?.overviewUrl),
);
const hasLanguageSwitcher = computed(() => (locales.value || []).length > 1);
const wrapperClass = computed(() =>
  props.fullWidth ? "header-top-bar__inner header-top-bar__inner--fluid" : "container header-top-bar__inner",
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

const hasContent = computed(() =>
  Boolean(
    openingHours.value ||
    hotline.value ||
    socialLinks.value.length ||
    hasLanguageSwitcher.value ||
    hasCurrencySwitcher.value ||
    hasCustomerBlock.value,
  ),
);

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
</script>

<template>
  <div v-if="isEnabled && hasContent" class="header-top-bar">
    <div :class="wrapperClass">
      <div class="header-top-bar__left">
        <span v-if="openingHours" class="header-top-bar__item">
          <Icon name="ph:clock" class="header-top-bar__item-icon" />
          <span>{{ openingHours }}</span>
        </span>

        <a v-if="hotline" :href="`tel:${hotline}`" class="header-top-bar__item header-top-bar__item--link">
          <Icon name="ph:phone" class="header-top-bar__item-icon" />
          <strong>{{ hotline }}</strong>
        </a>
      </div>

      <div class="header-top-bar__right">
        <LanguageSwitcher mode="inline" />

        <div v-if="hasCurrencySwitcher" class="header-top-bar__currency">
          <span class="header-top-bar__currency-current">{{ currentCurrency?.title }}</span>

          <div v-if="otherCurrencies.length" class="header-top-bar__currency-list">
            <a
              v-for="currency in otherCurrencies"
              :key="currency.title"
              :href="currency.href"
              class="header-top-bar__currency-link"
            >
              {{ currency.title }}
            </a>
          </div>
        </div>

        <div v-if="hasCustomerBlock" class="header-top-bar__account">
          <NuxtLink
            v-if="customer?.authenticated && overviewTo"
            :to="overviewTo"
            class="header-top-bar__account-link"
          >
            <img
              v-if="customer.avatarUrl"
              :src="customer.avatarUrl"
              :alt="customer.name || 'Customer avatar'"
              class="header-top-bar__account-avatar"
            >
            <Icon v-else name="ph:user-circle-fill" class="header-top-bar__account-icon" />
            <span>{{ customer.name || "My account" }}</span>
          </NuxtLink>
          <a
            v-else-if="customer?.authenticated && customer.overviewUrl"
            :href="customer.overviewUrl"
            class="header-top-bar__account-link"
          >
            <img
              v-if="customer.avatarUrl"
              :src="customer.avatarUrl"
              :alt="customer.name || 'Customer avatar'"
              class="header-top-bar__account-avatar"
            >
            <Icon v-else name="ph:user-circle-fill" class="header-top-bar__account-icon" />
            <span>{{ customer.name || "My account" }}</span>
          </a>

          <div v-else class="header-top-bar__account-guest">
            <NuxtLink v-if="loginTo" :to="loginTo" class="header-top-bar__account-link">
              <Icon name="ph:sign-in" class="header-top-bar__account-icon" />
              <span>Login</span>
            </NuxtLink>
            <a v-else-if="customer?.loginUrl" :href="customer.loginUrl" class="header-top-bar__account-link">
              <Icon name="ph:sign-in" class="header-top-bar__account-icon" />
              <span>Login</span>
            </a>
            <NuxtLink v-if="registerTo" :to="registerTo" class="header-top-bar__account-secondary">
              Register
            </NuxtLink>
            <a v-else-if="customer?.registerUrl" :href="customer.registerUrl" class="header-top-bar__account-secondary">
              Register
            </a>
          </div>
        </div>

        <div v-if="socialLinks.length" class="header-top-bar__socials">
          <a
            v-for="social in socialLinks"
            :key="`${social.name}-${social.url}`"
            :href="social.url"
            class="header-top-bar__social-link"
            :title="social.name || social.url"
            target="_blank"
            rel="noopener noreferrer"
          >
            <img
              v-if="social.image_url"
              :src="social.image_url"
              :alt="social.name || 'Social link'"
              class="header-top-bar__social-image"
            >
            <Icon
              v-else-if="resolveSocialIcon(social)"
              :name="resolveSocialIcon(social) || ''"
              class="header-top-bar__social-icon"
            />
            <span v-else class="header-top-bar__social-fallback">
              {{ (social.name || social.url || "?").slice(0, 1).toUpperCase() }}
            </span>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.header-top-bar {
  position: relative;
  z-index: 31;
  border-bottom: 1px solid rgba(255, 249, 241, 0.08);
  background:
    linear-gradient(180deg, rgba(19, 13, 10, 0.96), rgba(28, 19, 14, 0.9));
  color: rgba(248, 244, 236, 0.78);
}

.header-top-bar__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  min-height: 2.75rem;
}

.header-top-bar__inner--fluid {
  width: 100%;
  padding-inline: 2rem;
}

.header-top-bar__left,
.header-top-bar__right {
  display: flex;
  align-items: center;
  gap: 1rem;
  min-width: 0;
}

.header-top-bar__item,
.header-top-bar__item--link {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  color: inherit;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.11em;
  text-decoration: none;
  text-transform: uppercase;
  white-space: nowrap;
}

.header-top-bar__item--link:hover,
.header-top-bar__social-link:hover,
.header-top-bar__currency-link:hover,
.header-top-bar__account-link:hover,
.header-top-bar__account-secondary:hover {
  color: #d6c09b;
}

.header-top-bar__item-icon {
  font-size: 0.9rem;
  opacity: 0.85;
}

.header-top-bar__socials {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
}

.header-top-bar__currency,
.header-top-bar__account,
.header-top-bar__account-guest {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
}

.header-top-bar__currency {
  padding-left: 0.4rem;
  margin-left: 0.15rem;
  border-left: 1px solid rgba(255, 249, 241, 0.12);
}

.header-top-bar__currency-current,
.header-top-bar__currency-link,
.header-top-bar__account-link,
.header-top-bar__account-secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  color: inherit;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.11em;
  text-decoration: none;
  text-transform: uppercase;
  white-space: nowrap;
}

.header-top-bar__currency-current {
  color: #e3c8a8;
}

.header-top-bar__currency-list {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
}

.header-top-bar__account-avatar {
  width: 1rem;
  height: 1rem;
  border-radius: 999px;
  object-fit: cover;
}

.header-top-bar__account-icon {
  font-size: 0.95rem;
}

.header-top-bar__social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: inherit;
  text-decoration: none;
}

.header-top-bar__social-icon,
.header-top-bar__social-fallback {
  font-size: 0.9rem;
}

.header-top-bar__social-image {
  width: 0.95rem;
  height: 0.95rem;
  object-fit: contain;
}

@media (max-width: 991px) {
  .header-top-bar {
    display: none;
  }
}
</style>
