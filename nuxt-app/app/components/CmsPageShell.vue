<script setup lang="ts">
const props = defineProps<{
  layoutName?: string | null;
}>();

const isSideMenu = computed(() => props.layoutName === 'cms-side-menu');
const isFullMenu = computed(() => props.layoutName === 'cms-full-menu');
const isBlogSidebar = computed(() => props.layoutName === 'cms-blog-sidebar');

const layoutClass = computed(() => {
  switch (props.layoutName) {
    case 'cms-side-menu':
      return 'cms-layout--side-menu';
    case 'cms-full-menu':
      return 'cms-layout--full-menu';
    case 'cms-full-width':
      return 'cms-layout--full-width';
    case 'cms-blog-sidebar':
      return 'cms-layout--blog-sidebar';
    default:
      return 'cms-layout--default';
  }
});
</script>

<template>
  <div :class="['cms-layout', layoutClass]">
    <!-- Side-menu sidebar (only for side-menu layout) -->
    <aside v-if="isSideMenu" class="cms-layout__sidebar">
      <MainNavigation variant="side" />
    </aside>

    <!-- Standard header (all non-side-menu layouts) -->
    <template v-if="!isSideMenu">
      <HeaderTopBar :full-width="isFullMenu" />
      <MainNavigation variant="header" :full-width="isFullMenu" />
    </template>

    <!--
      Content body – the <slot /> lives here unconditionally.
      When layoutName changes, only the class and the optional aside toggle;
      the slot mount-point is never destroyed / re-created.
    -->
    <div
      class="cms-layout__body"
      :class="{ 'cms-layout__body--blog': isBlogSidebar }"
    >
      <slot />

      <aside v-if="isBlogSidebar" class="cms-blog-sidebar__aside">
        <BlogSidebarWidgets />
      </aside>
    </div>
  </div>
</template>

<style scoped>
/* ─── Default & Full-Menu ─── */
.cms-layout--default,
.cms-layout--full-menu {
  min-height: 100vh;
  background: linear-gradient(180deg, #fcf9f3 0%, #f6efe5 55%, #f8f4ed 100%);
}

.cms-layout--default :deep(main),
.cms-layout--full-menu :deep(main) {
  min-height: 100vh;
}

/* ─── Full-Width ─── */
.cms-layout--full-width {
  min-height: 100vh;
  background: linear-gradient(180deg, #fdfaf4 0%, #f4ede3 100%);
}

.cms-layout--full-width :deep(.page-hero) {
  padding-top: 4rem;
  padding-bottom: 3rem;
}

.cms-layout--full-width :deep(.cms-content) {
  width: 100%;
}

/* ─── Side-Menu ─── */
.cms-layout--side-menu {
  display: grid;
  min-height: 100vh;
  background: #f5eee4;
}

.cms-layout__sidebar {
  border-right: 1px solid rgba(50, 35, 25, 0.08);
  background: linear-gradient(180deg, rgba(250, 246, 239, 0.98), rgba(242, 233, 220, 0.96));
}

.cms-layout--side-menu .cms-layout__body {
  min-width: 0;
  background: #fcf8f2;
}

.cms-layout--side-menu .cms-layout__body :deep(main) {
  min-height: 100vh;
}

.cms-layout--side-menu .cms-layout__body :deep(.cms-breadcrumbs) {
  border-bottom: 1px solid rgba(63, 53, 45, 0.06);
}

.cms-layout--side-menu .cms-layout__body :deep(.page-shell) {
  padding-inline: clamp(1rem, 3vw, 2.25rem);
}

.cms-layout--side-menu .cms-layout__body :deep(.cms-content),
.cms-layout--side-menu .cms-layout__body :deep(.cms-footer) {
  min-width: 0;
}

/* ─── Blog Sidebar ─── */
.cms-layout--blog-sidebar {
  min-height: 100vh;
  background: linear-gradient(180deg, #fcf9f3 0%, #f3ece2 100%);
}

.cms-layout__body--blog {
  display: grid;
  gap: 3rem;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.25rem 5rem;
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

/* ─── Desktop (≥ 992px) ─── */
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

  .cms-layout--side-menu .cms-layout__body :deep(.page-shell) {
    padding-inline: clamp(1.5rem, 3vw, 3rem);
  }
}

/* ─── Blog Sidebar Desktop (≥ 1100px) ─── */
@media (min-width: 1100px) {
  .cms-layout__body--blog {
    grid-template-columns: minmax(0, 1fr) minmax(280px, 22rem);
    align-items: start;
  }

  .cms-blog-sidebar__aside {
    grid-column: 2;
    position: sticky;
    top: 8.25rem;
  }
}

/* ─── Blog Sidebar Mobile (< 1100px) ─── */
@media (max-width: 1099px) {
  .cms-layout--blog-sidebar :deep(.cms-breadcrumbs),
  .cms-layout--blog-sidebar :deep(.page-shell),
  .cms-layout--blog-sidebar :deep(.cms-content),
  .cms-layout--blog-sidebar :deep(.cms-footer) {
    grid-column: 1;
  }
}

/* ─── Side-Menu Mobile (< 992px) ─── */
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
