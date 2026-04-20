<template>
  <article class="sidebar-widget-card cms-quiet-surface">
    <header v-if="title || description" class="sidebar-widget-card__header">
      <span class="sidebar-widget-card__kicker">
        Journal
      </span>
      <h2 v-if="title" class="sidebar-widget-card__title">
        {{ title }}
      </h2>
      <p v-if="description" class="sidebar-widget-card__description">
        {{ description }}
      </p>
    </header>

    <div class="sidebar-widget-card__body">
      <slot />
    </div>
  </article>
</template>

<script setup lang="ts">
defineProps<{
  title?: string | null;
  description?: string | null;
}>();
</script>

<style scoped>
.sidebar-widget-card {
  padding: 1.8rem;
  border-radius: var(--retreat-radius-card);
}

.sidebar-widget-card__header {
  margin-bottom: 1.35rem;
}

.sidebar-widget-card__kicker {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.9rem;
  color: var(--retreat-clay);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.22em;
  text-transform: uppercase;
}

.sidebar-widget-card__kicker::before {
  content: "";
  width: 1.8rem;
  height: 1px;
  background: currentColor;
  opacity: 0.65;
}

.sidebar-widget-card__title {
  margin: 0;
  color: var(--retreat-ink);
  font-family: var(--font-display);
  font-size: clamp(1.75rem, 4vw, 2.45rem);
  font-weight: 600;
  line-height: 0.98;
  letter-spacing: -0.02em;
}

.sidebar-widget-card__description {
  margin: 0.7rem 0 0;
  color: var(--retreat-ink-soft);
  font-size: 0.95rem;
  line-height: 1.85;
}

.sidebar-widget-card__body {
  min-width: 0;
}
</style>
