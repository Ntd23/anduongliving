<script setup lang="ts">
import type { MenuData } from "~/composables/useMenu";
import type { ThemeSocialLink } from "~/composables/useThemeOptions";

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
}>();

const emit = defineEmits<{
  (event: "update:open", value: boolean): void;
}>();

const { locale, locales } = useI18n();
const switchLocalePath = useSwitchLocalePath();
const route = useRoute();

const languageLabels: Record<string, string> = {
  vi: "Tiếng Việt",
  en: "English",
  ja: "日本語",
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
const hasContact = computed(() => Boolean(props.hotline || props.email));

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
          <section class="mobile-menu-collapse__section">
            <div class="mobile-menu-collapse__title">
              <span>Menu</span>
            </div>

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
            <div class="mobile-menu-collapse__title">
              <span>Languages</span>
            </div>

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

          <section v-if="hasContact || hasSocialLinks" class="mobile-menu-collapse__section">
            <div class="mobile-menu-collapse__title">
              <span>Contact</span>
            </div>

            <div v-if="hasContact" class="mobile-menu-collapse__contact">
              <a
                v-if="hotline"
                :href="`tel:${hotline}`"
                class="mobile-menu-collapse__contact-link"
              >
                {{ hotline }}
              </a>
              <a
                v-if="email"
                :href="`mailto:${email}`"
                class="mobile-menu-collapse__contact-link"
              >
                {{ email }}
              </a>
            </div>

            <div v-if="hasSocialLinks" class="mobile-menu-collapse__socials">
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
  background: rgba(13, 9, 7, 0.48);
  backdrop-filter: blur(2px);
}

.mobile-menu-collapse {
  position: fixed;
  top: 5rem;
  left: 0;
  right: 0;
  z-index: 59;
  max-height: calc(100vh - 5rem);
  overflow-y: auto;
  border-top: 1px solid rgba(63, 53, 45, 0.08);
  background: linear-gradient(180deg, #fffaf2 0%, #f6ede1 100%);
  box-shadow: 0 18px 44px rgba(34, 22, 14, 0.16);
}

.mobile-menu-collapse__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 1px solid rgba(63, 53, 45, 0.08);
}

.mobile-menu-collapse__brand {
  display: inline-flex;
  align-items: center;
  color: #2e241d;
  text-decoration: none;
}

.mobile-menu-collapse__brand-logo {
  display: block;
  width: auto;
  max-width: 7.5rem;
  max-height: 3.5rem;
  object-fit: contain;
}

.mobile-menu-collapse__brand-text {
  font-family: "Iowan Old Style", "Palatino Linotype", "Book Antiqua", Georgia, serif;
  font-size: 1.1rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.mobile-menu-collapse__close {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border: 1px solid rgba(63, 53, 45, 0.12);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.62);
  color: #3f352d;
  font-size: 1.15rem;
}

.mobile-menu-collapse__body {
  display: grid;
  gap: 1.25rem;
  padding: 1rem;
}

.mobile-menu-collapse__section {
  border: 1px solid rgba(63, 53, 45, 0.08);
  background: rgba(255, 252, 247, 0.9);
}

.mobile-menu-collapse__title {
  padding: 0.9rem 1rem;
  border-bottom: 1px solid rgba(63, 53, 45, 0.08);
}

.mobile-menu-collapse__title span {
  color: #8d7355;
  font-size: 0.76rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.mobile-menu-collapse__list,
.mobile-menu-collapse__plain-list {
  margin: 0;
  padding: 0.35rem 1rem 0.75rem;
  list-style: none;
}

.mobile-menu-collapse__plain-list li + li {
  border-top: 1px solid rgba(63, 53, 45, 0.06);
}

.mobile-menu-collapse__plain-link {
  display: block;
  padding: 0.9rem 0;
  color: #3f352d;
  font-size: 0.98rem;
  font-weight: 500;
  text-decoration: none;
}

.mobile-menu-collapse__plain-link--active {
  color: #7c5c3b;
  font-weight: 700;
}

.mobile-menu-collapse__message {
  margin: 0;
  padding: 1rem;
  color: #5d5147;
  line-height: 1.65;
}

.mobile-menu-collapse__message--error {
  color: #8b3a2f;
}

.mobile-menu-collapse__contact {
  display: grid;
  gap: 0.75rem;
  padding: 0.9rem 1rem 1rem;
}

.mobile-menu-collapse__contact-link {
  color: #3f352d;
  font-size: 0.95rem;
  text-decoration: none;
}

.mobile-menu-collapse__socials {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  padding: 0 1rem 1rem;
}

.mobile-menu-collapse__social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border: 1px solid rgba(63, 53, 45, 0.1);
  border-radius: 999px;
  background: #fff;
  color: #6e5843;
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
  transform: translateY(-12px);
}

@media (min-width: 992px) {
  .mobile-menu-collapse,
  .mobile-menu-collapse__overlay {
    display: none;
  }
}
</style>
