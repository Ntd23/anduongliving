<template>
  <div class="cms-layout cms-layout--side-menu">
    <aside class="cms-layout__sidebar">
      <MainNavigation variant="side" />
    </aside>

    <section class="cms-layout__content">
      <slot />
    </section>
  </div>
</template>

<style scoped>
.cms-layout--side-menu {
  display: grid;
  min-height: 100vh;
  background: #f7f3ec;
}

.cms-layout__sidebar {
  border-right: 1px solid rgba(63, 53, 45, 0.08);
  background: linear-gradient(180deg, #faf5ee 0%, #f3ebdf 100%);
}

.cms-layout__content {
  min-width: 0;
  background: #fffdf8;
}

.cms-layout__content :deep(main) {
  min-height: 100vh;
}

.cms-layout__content :deep(.cms-breadcrumbs) {
  border-bottom: 1px solid rgba(63, 53, 45, 0.06);
}

.cms-layout__content :deep(.page-shell) {
  padding-inline: clamp(1rem, 3vw, 2.25rem);
}

.cms-layout__content :deep(.cms-content),
.cms-layout__content :deep(.cms-footer) {
  min-width: 0;
}

@media (min-width: 992px) {
  .cms-layout--side-menu {
    grid-template-columns: minmax(240px, 19rem) minmax(0, 1fr);
  }

  .cms-layout__sidebar {
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
  }

  .cms-layout__content :deep(.page-shell) {
    padding-inline: clamp(1.5rem, 3vw, 3rem);
  }
}

@media (max-width: 991px) {
  .cms-layout--side-menu {
    grid-template-columns: minmax(0, 1fr);
  }

  .cms-layout__sidebar {
    border-right: 0;
    border-bottom: 1px solid rgba(63, 53, 45, 0.08);
  }
}
</style>
