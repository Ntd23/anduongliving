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
  align-items: center;
  gap: 0.8rem;
}

.language-switcher:not(.language-switcher--inline) {
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: 40;
  padding: 0.6rem 0.8rem;
  border: 1px solid rgba(37, 27, 21, 0.1);
  border-radius: 999px;
  background: rgba(255, 252, 247, 0.88);
  box-shadow: 0 18px 46px rgba(33, 23, 17, 0.1);
  backdrop-filter: blur(14px);
}

.language-switcher__link {
  position: relative;
  color: rgba(37, 27, 21, 0.7);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-decoration: none;
  text-transform: uppercase;
}

.language-switcher__link:hover {
  color: #251b15;
}

.language-switcher__link--active {
  color: #a77a54;
}

.language-switcher--inline .language-switcher__link {
  color: rgba(248, 244, 236, 0.72);
  font-size: 0.72rem;
}

.language-switcher--inline .language-switcher__link:hover {
  color: #fffdf8;
}

.language-switcher--inline .language-switcher__link--active {
  color: #d6c09b;
}

.language-switcher--inline .language-switcher__link + .language-switcher__link::before,
.language-switcher:not(.language-switcher--inline) .language-switcher__link + .language-switcher__link::before {
  content: "";
  position: absolute;
  left: -0.42rem;
  top: 50%;
  width: 1px;
  height: 0.7rem;
  background: currentColor;
  opacity: 0.24;
  transform: translateY(-50%);
}

@media (max-width: 640px) {
  .language-switcher:not(.language-switcher--inline) {
    top: 0.75rem;
    right: 0.75rem;
  }
}
</style>



