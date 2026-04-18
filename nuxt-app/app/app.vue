<template>
  <div>
    <NuxtRouteAnnouncer />
    <LanguageSwitcher />
    <NuxtPage />
    <Footer />
  </div>
</template>
<style>
body {
  margin: 0;
  padding: 0;
  background-color: #f5f5f5;
}
</style>
<script setup>
import Footer from '../components/Footer.vue'
</script>
