<script setup lang="ts">
const props = withDefaults(defineProps<{
  mode?: "inline" | "floating";
}>(), {
  mode: "floating",
});

const { locale, locales } = useI18n();
const switchLocalePath = useSwitchLocalePath();

const languageLabels: Record<string, string> = {
  vi: "VI",
  en: "EN",
  ja: "JA",
};

const items = computed(() =>
  locales.value.map((localeOption) => {
    const code = typeof localeOption === "string" ? localeOption : localeOption.code;

    return {
      code,
      label: languageLabels[code] || code.toUpperCase(),
      to: switchLocalePath(code) || "/",
      active: locale.value === code,
    };
  }),
);
</script>

<template>
  <nav
    class="language-switcher"
    :class="{ 'language-switcher--inline': mode === 'inline' }"
    aria-label="Language switcher"
  >
    <NuxtLink
      v-for="item in items"
      :key="item.code"
      :to="item.to"
      class="language-switcher__link"
      :class="{ 'language-switcher__link--active': item.active }"
      :aria-current="item.active ? 'page' : undefined"
    >
      {{ item.label }}
    </NuxtLink>
  </nav>
</template>

<style scoped>
.language-switcher {
  display: inline-flex;
  gap: 0.35rem;
  padding: 0.35rem;
  border: 1px solid rgba(15, 23, 42, 0.1);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.88);
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.1);
  backdrop-filter: blur(12px);
}

.language-switcher:not(.language-switcher--inline) {
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: 40;
}

.language-switcher--inline {
  padding: 0;
  border: 0;
  border-radius: 0;
  background: transparent;
  box-shadow: none;
  backdrop-filter: none;
}

.language-switcher__link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 3rem;
  height: 2.25rem;
  padding: 0 0.9rem;
  border-radius: 999px;
  color: #334155;
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-decoration: none;
  transition:
    background-color 0.2s ease,
    color 0.2s ease,
    transform 0.2s ease;
}

.language-switcher__link:hover {
  color: #0f172a;
  background: rgba(15, 23, 42, 0.06);
}

.language-switcher__link--active {
  background: linear-gradient(135deg, #007cc3, #005a8f);
  color: #fff;
  box-shadow: 0 10px 24px rgba(0, 124, 195, 0.28);
}

.language-switcher__link--active:hover {
  color: #fff;
  transform: translateY(-1px);
}

.language-switcher--inline .language-switcher__link {
  min-width: auto;
  height: auto;
  padding: 0;
  border-radius: 0;
  color: rgba(255, 255, 255, 0.82);
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.08em;
}

.language-switcher--inline .language-switcher__link:hover {
  background: transparent;
  color: #ffffff;
}

.language-switcher--inline .language-switcher__link--active {
  background: transparent;
  box-shadow: none;
  color: #d8b56b;
}

@media (max-width: 640px) {
  .language-switcher:not(.language-switcher--inline) {
    top: 0.75rem;
    right: 0.75rem;
  }
}
</style>
