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
    <ul class="blog-pagination__list cms-quiet-surface">
      <li
        class="blog-pagination__item"
        :class="{ 'blog-pagination__item--disabled': currentPage <= 1 }"
        :aria-disabled="currentPage <= 1 ? 'true' : undefined"
      >
        <NuxtLink
          v-if="currentPage > 1"
          :to="buildTo(currentPage - 1)"
          class="blog-pagination__link blog-pagination__link--nav"
          aria-label="Previous"
        >
          Prev
        </NuxtLink>
        <span v-else class="blog-pagination__link blog-pagination__link--nav" aria-hidden="true">Prev</span>
      </li>

      <template v-for="item in pageItems" :key="item">
        <li
          v-if="typeof item === 'number'"
          class="blog-pagination__item"
          :class="{ 'blog-pagination__item--active': item === currentPage }"
          :aria-current="item === currentPage ? 'page' : undefined"
        >
          <NuxtLink
            v-if="item !== currentPage"
            :to="buildTo(item)"
            class="blog-pagination__link"
          >
            {{ item }}
          </NuxtLink>
          <span v-else class="blog-pagination__link">
            {{ item }}
          </span>
        </li>

        <li v-else class="blog-pagination__item blog-pagination__item--disabled" aria-disabled="true">
          <span class="blog-pagination__link">...</span>
        </li>
      </template>

      <li
        class="blog-pagination__item"
        :class="{ 'blog-pagination__item--disabled': currentPage >= lastPage }"
        :aria-disabled="currentPage >= lastPage ? 'true' : undefined"
      >
        <NuxtLink
          v-if="currentPage < lastPage"
          :to="buildTo(currentPage + 1)"
          class="blog-pagination__link blog-pagination__link--nav"
          aria-label="Next"
        >
          Next
        </NuxtLink>
        <span v-else class="blog-pagination__link blog-pagination__link--nav" aria-hidden="true">Next</span>
      </li>
    </ul>
  </nav>
</template>

<style scoped>
.blog-pagination {
  display: flex;
  justify-content: center;
  margin-top: 3rem;
}

.blog-pagination__list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.55rem;
  align-items: center;
  justify-content: center;
  margin: 0;
  padding: 0.9rem;
  border-radius: 999px;
  list-style: none;
}

.blog-pagination__link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 2.9rem;
  min-height: 2.9rem;
  padding: 0.65rem 1rem;
  border-radius: 999px;
  color: var(--retreat-ink);
  font-size: 0.84rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-decoration: none;
}

.blog-pagination__link--nav {
  min-width: 5.5rem;
  text-transform: uppercase;
}

.blog-pagination__item--active .blog-pagination__link {
  background: rgba(167, 122, 84, 0.16);
  color: var(--retreat-clay);
}

.blog-pagination__item:not(.blog-pagination__item--active):not(.blog-pagination__item--disabled) .blog-pagination__link:hover {
  background: rgba(107, 113, 83, 0.08);
  color: var(--retreat-olive);
}

.blog-pagination__item--disabled .blog-pagination__link {
  opacity: 0.42;
  pointer-events: none;
}

@media (max-width: 639px) {
  .blog-pagination__list {
    width: 100%;
    border-radius: 1.5rem;
  }

  .blog-pagination__link--nav {
    min-width: 4.7rem;
  }
}
</style>
