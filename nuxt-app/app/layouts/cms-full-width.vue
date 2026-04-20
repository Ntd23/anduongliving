<template>
  <div class="cms-layout cms-layout--full-width">
    <HeaderTopBar />
    <MainNavigation variant="header" />
    <slot />
  </div>
</template>

<style scoped>
.cms-layout--full-width {
  min-height: 100vh;
  background:
    linear-gradient(180deg, #fffdf9 0%, #f8f3eb 100%);
}

.cms-layout--full-width :deep(.page-hero) {
  padding-top: 4rem;
  padding-bottom: 3rem;
}

.cms-layout--full-width :deep(.cms-content) {
  width: 100%;
}
</style>
