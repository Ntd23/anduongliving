<script setup lang="ts">
const { locale, locales } = useI18n();
const switchLocalePath = useSwitchLocalePath();

const languageLabels: Record<string, string> = {
  vi: "VI",
  en: "EN",
  ja: "日本語",
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
  <nav class="language-switcher" aria-label="Language switcher">
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
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: 40;
  display: inline-flex;
  gap: 0.35rem;
  padding: 0.35rem;
  border: 1px solid rgba(15, 23, 42, 0.1);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.88);
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.1);
  backdrop-filter: blur(12px);
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

@media (max-width: 640px) {
  .language-switcher {
    top: 0.75rem;
    right: 0.75rem;
  }
}
</style>
