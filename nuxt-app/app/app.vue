<template>
  <div>
    <NuxtRouteAnnouncer />
    <LanguageSwitcher />
    <NuxtPage />
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
import HomePage from '../components/HomePage.vue'
import Footer from '../components/Footer.vue'
</script>
