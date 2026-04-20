<script setup lang="ts">
defineProps<{
  layoutName?: string | null;
}>();
</script>

<template>
  <div
    v-if="layoutName === 'cms-side-menu'"
    class="cms-layout cms-layout--side-menu"
  >
    <aside class="cms-layout__sidebar">
      <MainNavigation variant="side" />
    </aside>

    <section class="cms-layout__content">
      <slot />
    </section>
  </div>

  <div
    v-else-if="layoutName === 'cms-full-menu'"
    class="cms-layout cms-layout--full-menu"
  >
    <HeaderTopBar full-width />
    <MainNavigation variant="header" full-width />
    <slot />
  </div>

  <div
    v-else-if="layoutName === 'cms-full-width'"
    class="cms-layout cms-layout--full-width"
  >
    <HeaderTopBar />
    <MainNavigation variant="header" />
    <slot />
  </div>

  <div
    v-else-if="layoutName === 'cms-blog-sidebar'"
    class="cms-layout cms-layout--blog-sidebar"
  >
    <HeaderTopBar />
    <MainNavigation variant="header" />
    <div class="cms-blog-sidebar__shell">
      <slot />

      <aside class="cms-blog-sidebar__aside">
        <BlogSidebarWidgets />
      </aside>
    </div>
  </div>

  <div
    v-else
    class="cms-layout cms-layout--default"
  >
    <HeaderTopBar />
    <MainNavigation variant="header" />
    <slot />
  </div>
</template>

<style scoped>
.cms-layout--default,
.cms-layout--full-menu {
  min-height: 100vh;
  background: linear-gradient(180deg, #fffdf8 0%, #f7efe4 100%);
}

.cms-layout--default :deep(main),
.cms-layout--full-menu :deep(main) {
  min-height: 100vh;
}

.cms-layout--full-width {
  min-height: 100vh;
  background: linear-gradient(180deg, #fffdf9 0%, #f8f3eb 100%);
}

.cms-layout--full-width :deep(.page-hero) {
  padding-top: 4rem;
  padding-bottom: 3rem;
}

.cms-layout--full-width :deep(.cms-content) {
  width: 100%;
}

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

.cms-layout--blog-sidebar :deep(.cms-breadcrumbs),
.cms-layout--blog-sidebar :deep(.cms-footer) {
  grid-column: 1 / -1;
}

.cms-layout--blog-sidebar :deep(.page-shell),
.cms-layout--blog-sidebar :deep(.cms-content) {
  grid-column: 1;
  min-width: 0;
}

.cms-layout--blog-sidebar :deep(.page-hero) {
  padding-top: 3.5rem;
  padding-bottom: 2rem;
}

.cms-layout--blog-sidebar :deep(.cms-footer) {
  margin-top: 1rem;
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
  .cms-layout--blog-sidebar :deep(.cms-content),
  .cms-layout--blog-sidebar :deep(.cms-footer) {
    grid-column: 1;
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
