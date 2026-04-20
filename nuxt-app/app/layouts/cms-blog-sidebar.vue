<template>
  <div class="cms-layout cms-layout--blog-sidebar">
    <HeaderTopBar />
    <MainNavigation variant="header" />
    <div class="cms-blog-sidebar__shell">
      <slot />

      <aside class="cms-blog-sidebar__aside">
        <BlogSidebarWidgets />
      </aside>
    </div>

    <CmsFooter />
  </div>
</template>

<style scoped>
.cms-layout--blog-sidebar {
  min-height: 100vh;
  background: linear-gradient(180deg, #fffdf8 0%, #f5efe6 100%);
}

.cms-blog-sidebar__shell {
  display: grid;
  gap: 2.5rem;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1rem 4.5rem;
}

.cms-blog-sidebar__aside {
  min-width: 0;
}

.cms-layout--blog-sidebar :deep(main) {
  display: contents;
}

.cms-layout--blog-sidebar :deep(.cms-breadcrumbs) {
  grid-column: 1 / -1;
}

.cms-layout--blog-sidebar :deep(.page-shell),
.cms-layout--blog-sidebar :deep(.cms-content) {
  grid-column: 1;
  min-width: 0;
}

.cms-layout--blog-sidebar :deep(.page-shell) {
  min-width: 0;
}

.cms-layout--blog-sidebar :deep(.page-hero) {
  padding-top: 3.5rem;
  padding-bottom: 2rem;
}

@media (min-width: 1100px) {
  .cms-blog-sidebar__shell {
    grid-template-columns: minmax(0, 1fr) minmax(280px, 22rem);
    align-items: start;
  }

  .cms-blog-sidebar__aside {
    grid-column: 2;
    position: sticky;
    top: 8.25rem;
  }
}

@media (max-width: 1099px) {
  .cms-layout--blog-sidebar :deep(.cms-breadcrumbs),
  .cms-layout--blog-sidebar :deep(.page-shell),
  .cms-layout--blog-sidebar :deep(.cms-content) {
    grid-column: 1;
  }
}
</style>
