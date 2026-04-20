<template>
  <div>
    <NuxtRouteAnnouncer />
    <NuxtLoadingIndicator
      color="linear-gradient(90deg, #7c5c3b 0%, #c6a05a 45%, #e1c78d 100%)"
      :height="3"
      :throttle="0"
      :duration="2200"
    />
    <NuxtLayout>
      <NuxtPage :page-key="(route) => route.fullPath" />
    </NuxtLayout>
  </div>
</template>

<style>
body {
  margin: 0;
  padding: 0;
  background-color: #f5f5f5;
}
</style>
