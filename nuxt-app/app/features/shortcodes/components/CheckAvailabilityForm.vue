<script setup lang="ts">
import { parseCheckAvailabilityFormBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useCheckAvailability } from "~/composables/useCheckAvailability";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseCheckAvailabilityFormBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);

const formMeta = computed(() => {
  const form = section.value.bookingForm;

  if (!form) {
    return null;
  }

  return {
    action_url: form.actionUrl,
    date_format: form.dateFormat,
    default_start_date: form.startDate,
    default_end_date: form.endDate,
    default_adults: form.defaultAdults,
    minimum_adults: form.minAdults,
    maximum_adults: form.maxAdults,
    default_children: form.defaultChildren,
    default_rooms: form.defaultRooms,
  };
});

const { startDate, endDate, adults, children, rooms, minAdults, maxAdults, submit } = useCheckAvailability(formMeta);
</script>

<template>
  <section v-if="section.bookingForm" class="shortcode-check-availability-native">
    <div class="availability-shell">
      <div class="availability-card">
        <h2 v-if="section.bookingForm.title" class="availability-card__title">
          {{ section.bookingForm.title }}
        </h2>

        <form class="availability-form" @submit.prevent="submit">
          <label class="availability-form__field">
            <span>Check in</span>
            <input v-model="startDate" type="date" required>
          </label>
          <label class="availability-form__field">
            <span>Check out</span>
            <input v-model="endDate" type="date" required>
          </label>
          <label class="availability-form__field">
            <span>Adults</span>
            <input v-model="adults" :min="minAdults" :max="maxAdults" type="number" required>
          </label>
          <label class="availability-form__field">
            <span>Children</span>
            <input v-model="children" min="0" type="number">
          </label>
          <label class="availability-form__field">
            <span>Rooms</span>
            <input v-model="rooms" min="1" type="number" required>
          </label>

          <button type="submit" class="availability-form__button">
            {{ section.bookingForm.submitLabel || "Check Availability" }}
          </button>
        </form>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-check-availability-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-check-availability-native {
  padding: 3.5rem 0;
  background:
    radial-gradient(circle at top left, rgba(185, 130, 90, 0.1), transparent 28%),
    linear-gradient(180deg, #f8f2e7, #f2e7d8);
}

.availability-shell {
  width: min(calc(100% - 2rem), 76rem);
  margin: 0 auto;
}

.availability-card {
  padding: clamp(1.5rem, 3vw, 2.2rem);
  border-radius: 1.75rem;
  border: 1px solid rgba(111, 117, 83, 0.1);
  background: rgba(255, 252, 246, 0.82);
  box-shadow: 0 24px 60px rgba(48, 35, 27, 0.08);
  backdrop-filter: blur(10px);
}

.availability-card__title {
  margin: 0 0 1.25rem;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2rem, 4vw, 3.1rem);
  text-align: center;
}

.availability-form {
  display: grid;
  gap: 0.9rem;
  grid-template-columns: repeat(6, minmax(0, 1fr));
}

.availability-form__field {
  display: grid;
  gap: 0.35rem;
  color: #5c4d40;
  font-size: 0.92rem;
}

.availability-form__field span {
  text-transform: uppercase;
  letter-spacing: 0.12em;
  font-size: 0.72rem;
  font-weight: 600;
}

.availability-form__field input {
  min-height: 3.25rem;
  padding: 0 0.95rem;
  border: 1px solid rgba(107, 116, 79, 0.16);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.9);
  color: #2f241d;
  transition: border-color 0.2s ease;
}

.availability-form__field input:focus {
  outline: none;
  border-color: rgba(107, 116, 79, 0.4);
}

.availability-form__button {
  min-height: 3.25rem;
  margin-top: auto;
  border: 0;
  border-radius: 999px;
  background: linear-gradient(135deg, #6c744f, #5e6746);
  color: #fffaf1;
  font-weight: 700;
  box-shadow: 0 12px 28px rgba(108, 116, 79, 0.18);
  cursor: pointer;
  transition: box-shadow 0.2s ease;
}

.availability-form__button:hover {
  box-shadow: 0 16px 36px rgba(108, 116, 79, 0.24);
}

@media (max-width: 991px) {
  .availability-form {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .availability-form {
    grid-template-columns: 1fr;
  }
}
</style>




