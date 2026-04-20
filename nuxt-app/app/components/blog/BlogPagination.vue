<script setup lang="ts">
const props = defineProps<{
  currentPage: number;
  lastPage: number;
}>();

const route = useRoute();

const pageWindow = computed(() => {
  const pages = new Set<number>([1, props.lastPage]);

  for (let page = props.currentPage - 1; page <= props.currentPage + 1; page += 1) {
    if (page >= 1 && page <= props.lastPage) {
      pages.add(page);
    }
  }

  return Array.from(pages).sort((a, b) => a - b);
});

const pageItems = computed<Array<number | string>>(() => {
  const items: Array<number | string> = [];
  let previousPage = 0;

  for (const page of pageWindow.value) {
    if (previousPage && page - previousPage > 1) {
      items.push(`gap-${previousPage}-${page}`);
    }

    items.push(page);
    previousPage = page;
  }

  return items;
});

const buildTo = (page: number) => {
  const query = { ...route.query } as Record<string, string | string[] | undefined>;

  if (page <= 1) {
    delete query.page;
  } else {
    query.page = String(page);
  }

  return {
    path: route.path,
    query,
  };
};

</script>

<template>
  <nav
    v-if="lastPage > 1"
    class="blog-pagination"
    aria-label="Blog pagination"
  >
    <ul class="pagination custom-pagination">
      <li
        class="page-item"
        :class="{ disabled: currentPage <= 1 }"
        :aria-disabled="currentPage <= 1 ? 'true' : undefined"
      >
        <NuxtLink
          v-if="currentPage > 1"
          :to="buildTo(currentPage - 1)"
          class="page-link"
          aria-label="Previous"
        >
          ‹
        </NuxtLink>
        <span v-else class="page-link page-link-navigation" aria-hidden="true">‹</span>
      </li>

      <template v-for="item in pageItems" :key="item">
        <li
          v-if="typeof item === 'number'"
          class="page-item"
          :class="{ active: item === currentPage }"
          :aria-current="item === currentPage ? 'page' : undefined"
        >
          <NuxtLink
            v-if="item !== currentPage"
            :to="buildTo(item)"
            class="page-link"
          >
            {{ item }}
          </NuxtLink>
          <span v-else class="page-link">
            {{ item }}
          </span>
        </li>

        <li v-else class="page-item disabled" aria-disabled="true">
          <span class="page-link">{{ "..." }}</span>
        </li>
      </template>

      <li
        class="page-item"
        :class="{ disabled: currentPage >= lastPage }"
        :aria-disabled="currentPage >= lastPage ? 'true' : undefined"
      >
        <NuxtLink
          v-if="currentPage < lastPage"
          :to="buildTo(currentPage + 1)"
          class="page-link"
          aria-label="Next"
        >
          ›
        </NuxtLink>
        <span v-else class="page-link page-link-navigation" aria-hidden="true">›</span>
      </li>
    </ul>
  </nav>
</template>

<style scoped>
.blog-pagination {
  display: flex;
  justify-content: center;
  margin-top: 1.5rem;
}

.pagination {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  align-items: center;
  justify-content: center;
  margin: 0;
  padding: 0;
  list-style: none;
}

.page-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 2.75rem;
  min-height: 2.75rem;
  padding: 0.6rem 0.95rem;
  border: 1px solid rgba(63, 53, 45, 0.12);
  background: #fff;
  color: #3f352d;
  font-weight: 600;
  text-decoration: none;
  transition:
    color 0.2s ease,
    border-color 0.2s ease,
    background-color 0.2s ease;
}

.page-item.active .page-link {
  background: #7c5c3b;
  border-color: #7c5c3b;
  color: #fff8ef;
}

.page-item.disabled .page-link {
  opacity: 0.45;
  pointer-events: none;
}

.page-item:not(.active):not(.disabled) .page-link:hover {
  color: #7c5c3b;
  border-color: rgba(124, 92, 59, 0.24);
}
</style>
