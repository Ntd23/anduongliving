<script setup lang="ts">
import { useThemeOptions } from "~/composables/useThemeOptions";

const { locale } = useI18n();

const activeLocale = computed(() => locale.value);

const { data: themeOptions } = await useAsyncData(
  () => `footer-theme-options-${activeLocale.value}`,
  () => useThemeOptions(activeLocale.value),
  { watch: [activeLocale] },
);

const backgroundStyle = computed(() => {
  const backgroundUrl = themeOptions.value?.background_footer_url;

  if (!backgroundUrl) {
    return undefined;
  }

  return {
    backgroundImage: `linear-gradient(rgba(41, 29, 22, 0.72), rgba(41, 29, 22, 0.84)), url('${backgroundUrl}')`,
  };
});
</script>

<template>
  <footer class="cms-footer">
    <div class="cms-footer__top" :style="backgroundStyle">
      <div class="container">
        <FooterWidgets
          class="cms-footer__grid"
          :theme-options="themeOptions || null"
        />
      </div>
    </div>
  </footer>
</template>

<style scoped>
.cms-footer {
  margin-top: 0;
  background: #291d16;
}

.cms-footer__top {
  background:
    linear-gradient(rgba(41, 29, 22, 0.72), rgba(41, 29, 22, 0.84)),
    linear-gradient(135deg, #35271f 0%, #1d140f 100%);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  padding: 5.5rem 0 2.5rem;
}

.cms-footer__grid {
  min-width: 0;
}
</style>
