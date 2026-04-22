<script setup lang="ts">
import { cmsProxyRoutes, resolveCmsProxyRequestUrl } from "~~/shared/cms-routing";

type CustomerSessionData = {
  authenticated: boolean;
  name?: string | null;
  avatarUrl?: string | null;
  overviewUrl?: string | null;
  logoutUrl?: string | null;
};

definePageMeta({
  layout: "default",
});

const config = useRuntimeConfig();
const localePath = useLocalePath();

const { data, error } = await useAsyncData("customer-session", () =>
  $fetch<{ data?: CustomerSessionData }>(cmsProxyRoutes.customer.session(), {
    baseURL: resolveCmsProxyRequestUrl("/", {
      cmsProxyBaseUrl: config.public.cmsProxyBaseUrl,
      client: import.meta.client,
    }),
  }).then((response) => response.data || { authenticated: false }),
);

if (error.value) {
  throw error.value;
}

const customer = computed(() => data.value || { authenticated: false });
const logoutUrl = computed(() => customer.value.logoutUrl || null);
const displayName = computed(() => customer.value.name || "Khách hàng");
const avatarUrl = computed(() => customer.value.avatarUrl || null);

if (!customer.value.authenticated) {
  await navigateTo(localePath("/login"), { replace: true });
}

useSeoMeta({
  title: () => `${displayName.value} | Tài khoản`,
});
</script>

<template>
  <main class="customer-overview">
    <section class="customer-overview__hero">
      <div class="container customer-overview__hero-inner">
        <p class="customer-overview__eyebrow">
          Guest account
        </p>
        <h1 class="customer-overview__title">
          Xin chào, {{ displayName }}
        </h1>
        <p class="customer-overview__description">
          Đây là khu vực tài khoản do Nuxt hiển thị. Phiên đăng nhập vẫn dùng session backend,
          nhưng UI và điều hướng ở đây do Nuxt kiểm soát.
        </p>
      </div>
    </section>

    <section class="customer-overview__section">
      <div class="container customer-overview__grid">
        <article class="customer-overview__profile">
          <div class="customer-overview__profile-header">
            <img
              v-if="avatarUrl"
              :src="avatarUrl"
              :alt="displayName"
              class="customer-overview__avatar"
            >
            <div v-else class="customer-overview__avatar customer-overview__avatar--fallback">
              {{ displayName.slice(0, 1).toUpperCase() }}
            </div>

            <div>
              <p class="customer-overview__label">
                Tài khoản
              </p>
              <h2 class="customer-overview__name">
                {{ displayName }}
              </h2>
            </div>
          </div>

          <dl class="customer-overview__meta">
            <div class="customer-overview__meta-row">
              <dt>Trạng thái</dt>
              <dd>Đã đăng nhập</dd>
            </div>
            <div class="customer-overview__meta-row">
              <dt>Phiên làm việc</dt>
              <dd>Đồng bộ với backend Botble</dd>
            </div>
          </dl>
        </article>

        <article class="customer-overview__panel">
          <p class="customer-overview__panel-eyebrow">
            Hành động nhanh
          </p>
          <h2 class="customer-overview__panel-title">
            Điều hướng tài khoản
          </h2>
          <div class="customer-overview__actions">
            <NuxtLink :to="localePath('/')" class="customer-overview__action customer-overview__action--ghost">
              Về trang chủ
            </NuxtLink>
            <a v-if="logoutUrl" :href="logoutUrl" class="customer-overview__action">
              Đăng xuất
            </a>
          </div>
        </article>
      </div>
    </section>

    <CmsFooter />
  </main>
</template>

<style scoped>
.customer-overview {
  min-height: 100vh;
  background: linear-gradient(180deg, #fffdf8 0%, #f7efe4 100%);
}

.customer-overview__hero {
  padding: clamp(4rem, 10vw, 6rem) 0 clamp(2rem, 5vw, 3rem);
  background:
    radial-gradient(circle at top right, rgba(183, 141, 101, 0.18), transparent 30%),
    linear-gradient(180deg, rgba(37, 26, 18, 0.95), rgba(56, 40, 29, 0.88));
}

.customer-overview__hero-inner {
  max-width: 56rem;
}

.customer-overview__eyebrow {
  margin: 0 0 1rem;
  color: rgba(255, 247, 237, 0.78);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.customer-overview__title {
  margin: 0;
  color: #fffaf2;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.8rem, 6vw, 4.8rem);
  font-weight: 600;
  line-height: 0.95;
}

.customer-overview__description {
  max-width: 42rem;
  margin: 1.1rem 0 0;
  color: rgba(255, 247, 237, 0.76);
  line-height: 1.8;
}

.customer-overview__section {
  padding: clamp(2.2rem, 5vw, 4rem) 0 5rem;
}

.customer-overview__grid {
  display: grid;
  gap: 1.5rem;
}

.customer-overview__profile,
.customer-overview__panel {
  border: 1px solid rgba(68, 51, 40, 0.08);
  border-radius: 1.8rem;
  background: rgba(255, 255, 255, 0.92);
  box-shadow: 0 24px 60px rgba(55, 39, 28, 0.08);
  padding: clamp(1.4rem, 4vw, 2rem);
}

.customer-overview__profile-header {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.customer-overview__avatar {
  width: 4.5rem;
  height: 4.5rem;
  border-radius: 50%;
  object-fit: cover;
}

.customer-overview__avatar--fallback {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #bf8d5f, #85542c);
  color: #fff;
  font-size: 1.45rem;
  font-weight: 700;
}

.customer-overview__label,
.customer-overview__panel-eyebrow {
  margin: 0 0 0.35rem;
  color: rgba(133, 84, 44, 0.82);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
}

.customer-overview__name,
.customer-overview__panel-title {
  margin: 0;
  color: #231913;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.8rem, 4vw, 2.6rem);
  font-weight: 600;
}

.customer-overview__meta {
  margin: 1.5rem 0 0;
}

.customer-overview__meta-row {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.85rem 0;
  border-top: 1px solid rgba(68, 51, 40, 0.08);
}

.customer-overview__meta-row dt {
  color: rgba(61, 45, 35, 0.65);
}

.customer-overview__meta-row dd {
  margin: 0;
  color: #231913;
  font-weight: 600;
  text-align: right;
}

.customer-overview__actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.9rem;
  margin-top: 1.5rem;
}

.customer-overview__action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3.3rem;
  padding: 0 1.4rem;
  border-radius: 999px;
  background: linear-gradient(135deg, #b98655, #91613a);
  color: #fff;
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-decoration: none;
  text-transform: uppercase;
}

.customer-overview__action--ghost {
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(68, 51, 40, 0.12);
  color: #3f2d23;
}

@media (min-width: 992px) {
  .customer-overview__grid {
    grid-template-columns: minmax(0, 1.15fr) minmax(18rem, 0.85fr);
    align-items: start;
  }
}
</style>
