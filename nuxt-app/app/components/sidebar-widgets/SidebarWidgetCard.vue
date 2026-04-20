<template>
  <article class="sidebar-widget-card">
    <header v-if="title || description" class="sidebar-widget-card__header">
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
  border: 1px solid rgba(63, 53, 45, 0.08);
  background: rgba(255, 251, 246, 0.96);
  padding: 1.5rem;
  box-shadow: 0 16px 40px rgba(63, 53, 45, 0.06);
}

.sidebar-widget-card__header {
  margin-bottom: 1rem;
}

.sidebar-widget-card__title {
  margin: 0;
  color: #2f241d;
  font-family: "Iowan Old Style", "Palatino Linotype", "Book Antiqua", Georgia, serif;
  font-size: 1.35rem;
  line-height: 1.2;
}

.sidebar-widget-card__description {
  margin: 0.65rem 0 0;
  color: #6b5d51;
  line-height: 1.65;
}
</style>
