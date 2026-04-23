<script setup lang="ts">
import type { CustomerAuthPageData } from "~~/shared/customer-auth";
import CustomerAuthPage from "~/features/auth/ui/CustomerAuthPage.vue";
import { useCustomerAuthPage } from "~/features/auth/data/useCustomerAuthPage";

definePageMeta({
  layout: "default",
});

const { locale } = useI18n();

const { data, error } = await useAsyncData<CustomerAuthPageData>(
  () => `customer-auth-login-${locale.value}`,
  () => useCustomerAuthPage("login"),
  { watch: [locale] },
);

if (error.value) {
  throw error.value;
}

useSeoMeta({
  title: () => data.value?.hero.title || "Đăng nhập",
});
</script>

<template>
  <CustomerAuthPage v-if="data" :page="data" />
</template>
