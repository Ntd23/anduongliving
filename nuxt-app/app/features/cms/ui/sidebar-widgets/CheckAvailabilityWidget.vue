<script setup lang="ts">
import SidebarWidgetCard from "~/features/cms/ui/sidebar-widgets/SidebarWidgetCard.vue";
import { useCheckAvailability } from "~/composables/useCheckAvailability";
import type { SidebarWidgetManifest } from "~/features/cms/ui/sidebar-widgets/registry";

const props = defineProps<{
  widget: SidebarWidgetManifest;
}>();

const title = computed(() => String(props.widget.data?.title || "Check Availability"));
const description = computed(() => {
  const value = props.widget.data?.description;
  return typeof value === "string" && value.trim() ? value : null;
});

const { startDate, endDate, adults, children, rooms, minAdults, maxAdults, submit } = useCheckAvailability(
  computed(() => props.widget.meta || null),
);
</script>

<template>
  <SidebarWidgetCard :title="title" :description="description">
    <form class="sidebar-availability" @submit.prevent="submit">
      <label class="sidebar-availability__field">
        <span>Check in</span>
        <input v-model="startDate" type="date" required>
      </label>
      <label class="sidebar-availability__field">
        <span>Check out</span>
        <input v-model="endDate" type="date" required>
      </label>
      <div class="sidebar-availability__grid">
        <label class="sidebar-availability__field">
          <span>Adults</span>
          <input v-model="adults" :min="minAdults" :max="maxAdults" type="number" required>
        </label>
        <label class="sidebar-availability__field">
          <span>Children</span>
          <input v-model="children" min="0" type="number">
        </label>
        <label class="sidebar-availability__field sidebar-availability__field--full">
          <span>Rooms</span>
          <input v-model="rooms" min="1" type="number" required>
        </label>
      </div>
      <button type="submit" class="sidebar-availability__button">
        Check Availability
      </button>
    </form>
  </SidebarWidgetCard>
</template>

<style scoped>
.sidebar-availability {
  display: grid;
  gap: 0.9rem;
}

.sidebar-availability__grid {
  display: grid;
  gap: 0.9rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.sidebar-availability__field {
  display: grid;
  gap: 0.35rem;
  color: #5d5147;
  font-size: 0.95rem;
}

.sidebar-availability__field--full {
  grid-column: 1 / -1;
}

.sidebar-availability__field input {
  width: 100%;
  border: 1px solid rgba(63, 53, 45, 0.14);
  background: #fff;
  padding: 0.85rem 1rem;
  color: #2f241d;
}

.sidebar-availability__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3rem;
  border: 0;
  background: #7c5c3b;
  color: #fff;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}
</style>



