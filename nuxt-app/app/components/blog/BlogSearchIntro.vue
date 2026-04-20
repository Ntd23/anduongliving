<script setup lang="ts">
import { cmsAppRoutes } from "~~/shared/cms-routing";

const props = defineProps<{
  query?: string;
  total?: number;
}>();

const localePath = useLocalePath();
const searchInput = ref(props.query || "");

watch(
  () => props.query,
  (value) => {
    searchInput.value = value || "";
  },
);

const hasQuery = computed(() => Boolean(props.query?.trim()));
const heading = computed(() =>
  hasQuery.value ? `Search results for "${props.query}"` : "Search the journal",
);
const resultSummary = computed(() => {
  if (!hasQuery.value) {
    return "Enter a keyword to explore stories, updates, and retreat notes.";
  }

  const total = Number(props.total || 0);

  if (total <= 0) {
    return `No stories matched "${props.query}".`;
  }

  return `${total} story${total > 1 ? "ies" : "y"} matched "${props.query}".`;
});

const submit = async () => {
  const query = searchInput.value.trim();

  await navigateTo(
    localePath({
      path: cmsAppRoutes.blog.search(),
      query: query ? { q: query } : {},
    }),
  );
};
</script>

<template>
  <section class="blog-search-intro cms-quiet-surface">
    <div class="blog-search-intro__copy">
      <p class="cms-shell-eyebrow blog-search-intro__eyebrow">
        Journal Search
      </p>
      <h2 class="blog-search-intro__title">
        {{ heading }}
      </h2>
      <p class="blog-search-intro__summary">
        {{ resultSummary }}
      </p>
    </div>

    <form class="blog-search-intro__form" @submit.prevent="submit">
      <input
        v-model="searchInput"
        type="search"
        class="blog-search-intro__input"
        placeholder="Search stories"
        autocomplete="off"
      >
      <button type="submit" class="blog-search-intro__button">
        Search
      </button>
    </form>
  </section>
</template>

<style scoped>
.blog-search-intro {
  display: grid;
  gap: 1.4rem;
  margin-bottom: 2.2rem;
  padding: clamp(1.7rem, 3vw, 2.4rem);
  border-radius: var(--retreat-radius-card);
}

.blog-search-intro__eyebrow {
  margin-bottom: 1rem;
}

.blog-search-intro__title {
  margin: 0;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: clamp(2.4rem, 5vw, 4rem);
  font-weight: 600;
  line-height: 0.96;
  letter-spacing: -0.025em;
}

.blog-search-intro__summary {
  max-width: 34rem;
  margin: 1rem 0 0;
  color: var(--retreat-ink-soft);
  line-height: 1.9;
}

.blog-search-intro__form {
  display: grid;
  gap: 0.85rem;
}

.blog-search-intro__input {
  width: 100%;
  min-height: 3.6rem;
  border: 1px solid rgba(56, 41, 30, 0.12);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.82);
  padding: 0.95rem 1.2rem;
  color: var(--retreat-ink);
}

.blog-search-intro__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3.45rem;
  padding: 0.85rem 1.4rem;
  border: 0;
  border-radius: 999px;
  background: linear-gradient(180deg, rgba(107, 113, 83, 0.98), rgba(83, 91, 60, 0.98));
  color: #fffdf8;
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

@media (min-width: 768px) {
  .blog-search-intro {
    grid-template-columns: minmax(0, 1fr) minmax(280px, 24rem);
    align-items: end;
    gap: 1.5rem 2rem;
  }

  .blog-search-intro__form {
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
  }

  .blog-search-intro__button {
    min-width: 8rem;
  }
}
</style>
