<template>
  <div class="cms-layout cms-layout--full-menu">
    <HeaderTopBar full-width />
    <MainNavigation variant="header" full-width />
    <slot />
  </div>
</template>

<style scoped>
.cms-layout--full-menu {
  min-height: 100vh;
  background: linear-gradient(180deg, #fffdf8 0%, #f7efe4 100%);
}

.cms-layout--full-menu :deep(main) {
  min-height: 100vh;
}
</style>
