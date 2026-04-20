<template>
  <div>
    <NuxtRouteAnnouncer />
    <NuxtLoadingIndicator
      color="linear-gradient(90deg, #7d6b53 0%, #b48c63 45%, #d6c09b 100%)"
      :height="2"
      :throttle="0"
      :duration="2200"
    />
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<style>
body {
  padding: 0;
}
</style>
