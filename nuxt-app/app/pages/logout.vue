<script setup lang="ts">
import { cmsProxyRoutes } from "~~/shared/cms-routing";

definePageMeta({
  layout: "default",
});

const localePath = useLocalePath();

await $fetch<{ data?: { nextUrl?: string | null } }>(cmsProxyRoutes.customer.logout());

await navigateTo(localePath("/"), { replace: true });
</script>

<template>
  <main class="customer-logout">
    <div class="container customer-logout__inner">
      <p>Dang dang xuat...</p>
    </div>
  </main>
</template>

<style scoped>
.customer-logout {
  min-height: 40vh;
  display: flex;
  align-items: center;
}

.customer-logout__inner {
  padding-block: 3rem;
}
</style>
