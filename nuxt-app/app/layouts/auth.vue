<template>
  <div class="auth-layout">
    <slot />
  </div>
</template>

<style scoped>
.auth-layout {
  min-height: 100vh;
  background: linear-gradient(180deg, #fffdf8 0%, #f7efe4 100%);
}

.auth-layout :deep(main),
.auth-layout :deep(section) {
  min-width: 0;
}
</style>
