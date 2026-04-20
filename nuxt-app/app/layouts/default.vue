<template>
  <div class="cms-layout cms-layout--default">
    <HeaderTopBar />
    <MainNavigation variant="header" />
    <slot />
  </div>
</template>

<style scoped>
.cms-layout--default {
  min-height: 100vh;
  background: linear-gradient(180deg, #fffdf8 0%, #f7efe4 100%);
}

.cms-layout--default :deep(main) {
  min-height: 100vh;
}
</style>
