<script setup lang="ts">
import type { CustomerAuthPageData } from "~~/shared/customer-auth";
import CustomerAuthForm from "~/features/auth/ui/CustomerAuthForm.vue";

const props = defineProps<{
  page: CustomerAuthPageData;
}>();

const localePath = useLocalePath();

const homeHref = computed(() =>
  props.page.hero.homeUrl?.startsWith("/") ? localePath(props.page.hero.homeUrl) : localePath("/"),
);

const heroStyle = computed(() => {
  if (!props.page.hero.backgroundImageUrl) {
    return undefined;
  }

  return {
    backgroundImage: `linear-gradient(180deg, rgba(22, 15, 11, 0.58), rgba(22, 15, 11, 0.42)), url('${props.page.hero.backgroundImageUrl}')`,
  };
});
</script>

<template>
  <main class="customer-auth-page">
    <section class="customer-auth-page__hero" :style="heroStyle">
      <div class="container customer-auth-page__hero-inner">
        <div class="customer-auth-page__hero-copy">
          <h1 class="customer-auth-page__hero-title">
            {{ page.hero.title }}
          </h1>
          <nav class="customer-auth-page__breadcrumbs" aria-label="Breadcrumb">
            <NuxtLink :to="homeHref" class="customer-auth-page__breadcrumb-link">
              {{ page.hero.homeLabel || "Trang chá»§" }}
            </NuxtLink>
            <span class="customer-auth-page__breadcrumb-separator">/</span>
            <span class="customer-auth-page__breadcrumb-current">
              {{ page.hero.breadcrumbLabel }}
            </span>
          </nav>
        </div>
      </div>
    </section>

    <section class="customer-auth-page__section">
      <div class="container customer-auth-page__section-inner">
        <div class="customer-auth-page__panel">
          <CustomerAuthForm :page="page" />
        </div>
      </div>
    </section>

    <CmsFooter />
  </main>
</template>

<style scoped>
.customer-auth-page {
  min-height: 100vh;
}

.customer-auth-page__hero {
  position: relative;
  overflow: hidden;
  background:
    linear-gradient(180deg, rgba(22, 15, 11, 0.52), rgba(22, 15, 11, 0.38)),
    linear-gradient(135deg, #423126, #8e6543);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}

.customer-auth-page__hero::after {
  content: "";
  position: absolute;
  inset: auto 0 0;
  height: 8rem;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(255, 252, 246, 1));
}

.customer-auth-page__hero-inner {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: flex-end;
  min-height: clamp(18rem, 38vw, 27rem);
  padding-top: clamp(4rem, 9vw, 6rem);
  padding-bottom: clamp(3rem, 8vw, 5rem);
}

.customer-auth-page__hero-copy {
  max-width: 42rem;
}

.customer-auth-page__eyebrow {
  display: inline-flex;
  align-items: center;
  min-height: 2rem;
  margin: 0 0 1rem;
  padding: 0 0.8rem;
  border: 1px solid rgba(255, 255, 255, 0.24);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.08);
  color: rgba(255, 249, 241, 0.82);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  backdrop-filter: blur(8px);
}

.customer-auth-page__hero-title {
  margin: 0;
  color: #fffaf2;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(3rem, 7vw, 5.5rem);
  font-weight: 600;
  line-height: 0.95;
}

.customer-auth-page__breadcrumbs {
  display: inline-flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.6rem;
  margin-top: 1.25rem;
  color: rgba(255, 248, 239, 0.84);
  font-size: 0.9rem;
  font-weight: 600;
}

.customer-auth-page__breadcrumb-link {
  color: inherit;
  text-decoration: none;
}

.customer-auth-page__breadcrumb-separator {
  opacity: 0.55;
}

.customer-auth-page__section {
  position: relative;
  padding: clamp(2rem, 5vw, 4rem) 0 clamp(4rem, 7vw, 5.5rem);
  background: linear-gradient(180deg, #fffcf6 0%, #f9f2e8 100%);
}

.customer-auth-page__section-inner {
  display: flex;
  justify-content: center;
}

.customer-auth-page__panel {
  width: min(100%, 44rem);
}

@media (max-width: 767px) {
  .customer-auth-page__hero-inner {
    min-height: 15rem;
    padding-top: 3rem;
  }

  .customer-auth-page__hero-title {
    font-size: clamp(2.4rem, 13vw, 3.8rem);
  }

  .customer-auth-page__breadcrumbs {
    font-size: 0.82rem;
  }
}
</style>



