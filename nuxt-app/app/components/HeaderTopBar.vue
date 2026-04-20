<script setup lang="ts">
import { useThemeOptions, type ThemeSocialLink } from "~/composables/useThemeOptions";

const props = withDefaults(defineProps<{
  fullWidth?: boolean;
}>(), {
  fullWidth: false,
});

const { locale, locales } = useI18n();

const activeLocale = computed(() => locale.value);

const { data: themeOptions } = await useAsyncData(
  () => `header-top-theme-options-${props.fullWidth ? "fluid" : "default"}-${activeLocale.value}`,
  () => useThemeOptions(activeLocale.value),
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
const hasLanguageSwitcher = computed(() =>
  (locales.value || []).length > 1,
);
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
  Boolean(openingHours.value || hotline.value || socialLinks.value.length || hasLanguageSwitcher.value),
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
          <Icon name="ph:device-mobile" class="header-top-bar__item-icon" />
          <strong>{{ hotline }}</strong>
        </a>
      </div>

      <div class="header-top-bar__right">
        <LanguageSwitcher mode="inline" />

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
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  background: #121212;
  color: rgba(255, 255, 255, 0.82);
}

.header-top-bar__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  min-height: 3.5rem;
}

.header-top-bar__inner--fluid {
  width: 100%;
  padding-inline: 2rem;
}

.header-top-bar__left,
.header-top-bar__right {
  display: flex;
  align-items: center;
  gap: 1.1rem;
}

.header-top-bar__item,
.header-top-bar__item--link {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  color: rgba(255, 255, 255, 0.82);
  font-size: 0.9rem;
  text-decoration: none;
}

.header-top-bar__item--link:hover,
.header-top-bar__social-link:hover {
  color: #d8b56b;
}

.header-top-bar__item-icon {
  font-size: 1rem;
}

.header-top-bar__socials {
  display: inline-flex;
  align-items: center;
  gap: 0.7rem;
}

.header-top-bar__social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: rgba(255, 255, 255, 0.82);
  text-decoration: none;
}

.header-top-bar__social-icon,
.header-top-bar__social-fallback {
  font-size: 0.95rem;
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
