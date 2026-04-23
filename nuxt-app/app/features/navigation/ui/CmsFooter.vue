<script setup lang="ts">
import { useThemeOptions } from "~/features/cms/data/useThemeOptions";

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
    backgroundImage: `linear-gradient(rgba(18, 12, 10, 0.88), rgba(18, 12, 10, 0.94)), url('${backgroundUrl}')`,
  };
});
</script>

<template>
  <footer class="cms-footer">
    <div class="cms-footer__top" :style="backgroundStyle">
      <div class="container cms-footer__container">
        <p class="cms-footer__eyebrow">
          Quiet retreat living
        </p>

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
  background: #140e0b;
}

.cms-footer__top {
  background:
    linear-gradient(rgba(18, 12, 10, 0.88), rgba(18, 12, 10, 0.94)),
    radial-gradient(circle at top, rgba(92, 71, 53, 0.26), transparent 38%),
    linear-gradient(135deg, #241914 0%, #100b08 100%);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  padding: 6rem 0 3rem;
}

.cms-footer__container {
  display: grid;
  gap: 2rem;
}

.cms-footer__eyebrow {
  margin: 0;
  color: rgba(214, 192, 155, 0.8);
  font-size: 0.76rem;
  font-weight: 700;
  letter-spacing: 0.22em;
  text-transform: uppercase;
}

.cms-footer__grid {
  min-width: 0;
}

@media (max-width: 991px) {
  .cms-footer__top {
    padding: 4.5rem 0 2.5rem;
  }
}
</style>



