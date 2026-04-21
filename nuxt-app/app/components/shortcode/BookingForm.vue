<script setup lang="ts">
import { parseBookingFormBlock, type ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseBookingFormBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);

const pad = (value: number) => String(value).padStart(2, "0");

const toIsoDate = (value?: string | null) => {
  const normalized = String(value || "").trim();

  if (!normalized) {
    return "";
  }

  const parts = normalized.split(/[-/]/).map((part) => part.trim());

  if (parts.length !== 3) {
    return "";
  }

  const format = section.value.dateFormat || "d-m-Y";
  let year: string | undefined = "";
  let month: string | undefined = "";
  let day: string | undefined = "";

  switch (format) {
    case "d-m-Y":
    case "d/m/Y":
      [day, month, year] = parts;
      break;
    case "m-d-Y":
    case "m/d/Y":
      [month, day, year] = parts;
      break;
    case "Y-m-d":
    case "Y/m/d":
      [year, month, day] = parts;
      break;
    default:
      return "";
  }

  return `${year}-${month}-${day}`;
};

const formatBotbleDate = (isoDate: string, format: string) => {
  const date = new Date(`${isoDate}T00:00:00`);

  if (Number.isNaN(date.getTime())) {
    return isoDate;
  }

  const day = pad(date.getDate());
  const month = pad(date.getMonth() + 1);
  const year = String(date.getFullYear());

  switch (format) {
    case "d-m-Y":
      return `${day}-${month}-${year}`;
    case "m-d-Y":
      return `${month}-${day}-${year}`;
    case "Y-m-d":
      return `${year}-${month}-${day}`;
    case "d/m/Y":
      return `${day}/${month}/${year}`;
    case "m/d/Y":
      return `${month}/${day}/${year}`;
    case "Y/m/d":
      return `${year}/${month}/${day}`;
    default:
      return `${day}-${month}-${year}`;
  }
};

const selectedAdultOption = computed(
  () => section.value.adultOptions.find((option) => option.selected) || section.value.adultOptions[0] || null,
);
const selectedRoomOption = computed(
  () => section.value.roomOptions.find((option) => option.selected) || section.value.roomOptions[0] || null,
);

const startDate = ref(toIsoDate(section.value.startDate));
const endDate = ref(toIsoDate(section.value.endDate));
const adults = ref(selectedAdultOption.value?.value || "1");
const roomId = ref(selectedRoomOption.value?.value || "");

watch(
  () => section.value,
  (value) => {
    startDate.value = toIsoDate(value.startDate);
    endDate.value = toIsoDate(value.endDate);
    adults.value = (value.adultOptions.find((option) => option.selected) || value.adultOptions[0])?.value || "1";
    roomId.value = (value.roomOptions.find((option) => option.selected) || value.roomOptions[0])?.value || "";
  },
  { deep: true },
);

const submit = () => {
  if (!section.value.actionUrl || !import.meta.client) {
    return;
  }

  const form = document.createElement("form");
  form.method = section.value.method || "POST";
  form.action = section.value.actionUrl;
  form.style.display = "none";

  const addField = (name: string, value: string) => {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = name;
    input.value = value;
    form.appendChild(input);
  };

  if (section.value.csrfToken) {
    addField("_token", section.value.csrfToken);
  }

  addField("start_date", formatBotbleDate(startDate.value, section.value.dateFormat || "d-m-Y"));
  addField("end_date", formatBotbleDate(endDate.value, section.value.dateFormat || "d-m-Y"));
  addField("adults", adults.value);
  addField("room_id", roomId.value);

  document.body.appendChild(form);
  form.submit();
  form.remove();
};
</script>

<template>
  <section v-if="section.actionUrl || section.title || section.image?.src" class="shortcode-booking-form-native">
    <div class="booking-shell">
      <div class="booking-copy">
        <img
          v-if="section.shapeImage?.src"
          :src="section.shapeImage.src"
          :alt="section.shapeImage.alt || 'Decorative shape'"
          class="booking-copy__shape"
        >
        <p v-if="section.subtitle" class="booking-copy__eyebrow">{{ section.subtitle }}</p>
        <h2 v-if="section.title" class="booking-copy__title">{{ section.title }}</h2>

        <form class="booking-form" @submit.prevent="submit">
          <label class="booking-form__field">
            <span>Check in</span>
            <input v-model="startDate" type="date" required>
          </label>
          <label class="booking-form__field">
            <span>Check out</span>
            <input v-model="endDate" type="date" required>
          </label>
          <label class="booking-form__field">
            <span>Guests</span>
            <select v-model="adults" required>
              <option v-for="option in section.adultOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </label>
          <label class="booking-form__field">
            <span>Room</span>
            <select v-model="roomId" required>
              <option v-for="option in section.roomOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </label>

          <button type="submit" class="booking-form__button">
            {{ section.submitLabel || "Book now" }}
          </button>
        </form>
      </div>

      <figure v-if="section.image?.src" class="booking-media">
        <img :src="section.image.src" :alt="section.image.alt || section.title || 'Booking image'">
      </figure>
    </div>
  </section>

  <section v-else class="shortcode-booking-form-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-booking-form-native {
  position: relative;
  padding: clamp(4rem, 8vw, 7rem) 0;
  background: linear-gradient(180deg, #f9f3e9, #efe4d5);
}

.booking-shell {
  width: min(calc(100% - 2rem), 76rem);
  margin: 0 auto;
  display: grid;
  gap: 2rem;
  grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
  align-items: center;
}

.booking-copy {
  position: relative;
  padding: 2rem;
  border-radius: 1.8rem;
  background: rgba(255, 252, 246, 0.82);
  box-shadow: 0 24px 60px rgba(48, 35, 27, 0.08);
}

.booking-copy__shape {
  width: 3.5rem;
  margin-bottom: 1rem;
}

.booking-copy__eyebrow {
  margin: 0 0 0.8rem;
  color: #8a6e48;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  font-size: 0.76rem;
}

.booking-copy__title {
  margin: 0;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.4rem, 4.8vw, 4rem);
  line-height: 0.98;
}

.booking-form {
  display: grid;
  gap: 0.9rem;
  margin-top: 1.5rem;
}

.booking-form__field {
  display: grid;
  gap: 0.35rem;
}

.booking-form__field span {
  color: #6a5948;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.72rem;
}

.booking-form__field input,
.booking-form__field select {
  min-height: 3.25rem;
  padding: 0 0.95rem;
  border: 1px solid rgba(107, 116, 79, 0.18);
  border-radius: 999px;
  background: #fff;
  color: #2f241d;
}

.booking-form__button {
  min-height: 3.25rem;
  border: 0;
  border-radius: 999px;
  background: #6c744f;
  color: #fffaf1;
  font-weight: 600;
}

.booking-media {
  margin: 0;
  overflow: hidden;
  border-radius: 2rem;
  box-shadow: 0 30px 70px rgba(48, 35, 27, 0.12);
}

.booking-media img {
  width: 100%;
  display: block;
  object-fit: cover;
}

@media (max-width: 991px) {
  .booking-shell {
    grid-template-columns: 1fr;
  }
}
</style>
