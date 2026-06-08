<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from "vue";
import { parseBookingFormBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";
import { useScrollAnimation } from "~/composables/useScrollAnimation";
import { useResolvedCmsAsset } from "~/composables/useResolvedCmsAsset";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseBookingFormBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveAsset = useResolvedCmsAsset();

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

const { setupScrollAnimations } = useScrollAnimation()

onMounted(async () => {
  await nextTick()
  setupScrollAnimations()
})

onUnmounted(() => {
  // Cleanup handled by composable
})

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
  <section
    v-if="section.actionUrl || section.subtitle || section.title || section.description || section.image?.src"
    class="shortcode-booking-form-native"
  >
    <div class="booking-backdrop" />
    <div class="booking-veil" />
    
    <div class="container booking-shell animate-on-scroll">
      <div class="booking-grid">
        <div class="booking-content animate-on-scroll" style="--delay: 100ms">
          <header class="booking-header animate-on-scroll" style="--delay: 200ms">
            <img
              v-if="section.shapeImage?.src"
              :src="resolveAsset(section.shapeImage.src) || section.shapeImage.src"
              :alt="section.shapeImage.alt || 'Decorative shape'"
              class="booking-header__shape animate-on-scroll"
              style="--delay: 250ms"
            >
            <h2 v-if="section.title" class="booking-header__title animate-on-scroll" style="--delay: 300ms">
              {{ section.title }}
            </h2>
            <p v-if="section.description" class="booking-header__description animate-on-scroll" style="--delay: 400ms">
              {{ section.description }}
            </p>
          </header>

          <form class="booking-form animate-on-scroll" style="--delay: 500ms" @submit.prevent="submit">
            <div class="booking-form__row">
              <label class="booking-form__field animate-on-scroll" style="--delay: 600ms">
                <span class="booking-form__label">Check in</span>
                <input v-model="startDate" type="date" class="booking-form__input" required>
              </label>
              <label class="booking-form__field animate-on-scroll" style="--delay: 700ms">
                <span class="booking-form__label">Check out</span>
                <input v-model="endDate" type="date" class="booking-form__input" required>
              </label>
            </div>
            <div class="booking-form__row">
              <label class="booking-form__field animate-on-scroll" style="--delay: 800ms">
                <span class="booking-form__label">Guests</span>
                <select v-model="adults" class="booking-form__select" required>
                  <option v-for="option in section.adultOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                  </option>
                </select>
              </label>
              <label class="booking-form__field animate-on-scroll" style="--delay: 900ms">
                <span class="booking-form__label">Room</span>
                <select v-model="roomId" class="booking-form__select" required>
                  <option v-for="option in section.roomOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                  </option>
                </select>
              </label>
            </div>

            <button type="submit" class="booking-form__button animate-on-scroll" style="--delay: 1000ms">
              {{ section.submitLabel || "Book now" }}
            </button>
          </form>
        </div>

        <figure v-if="section.image?.src" class="booking-media animate-on-scroll" style="--delay: 600ms">
          <img
            :src="resolveAsset(section.image.src) || section.image.src"
            :alt="section.image.alt || section.title || 'Booking image'"
            class="booking-media__image"
          >
        </figure>
      </div>

      <footer v-if="section.subtitle" class="booking-footer animate-on-scroll" style="--delay: 1100ms">
        {{ section.subtitle }}
      </footer>
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
  min-height: clamp(44rem, 100vh, 58rem);
  display: flex;
  align-items: center;
  overflow: hidden;
}

.booking-backdrop {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #faf8f5 0%, #f5f2ed 100%);
  z-index: 1;
}

.booking-veil {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse at 12% 18%, rgba(138, 110, 72, 0.12) 0%, transparent 38%),
    radial-gradient(ellipse at 88% 78%, rgba(45, 32, 24, 0.08) 0%, transparent 34%);
  z-index: 2;
}

.booking-shell {
  position: relative;
  z-index: 3;
  width: min(76rem, 92vw);
  max-width: none;
  margin: 0 auto;
}

.booking-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: clamp(2rem, 5vw, 4.5rem);
  align-items: stretch;
}

.booking-content {
  position: relative;
  display: flex;
  min-height: clamp(30rem, 58vw, 40rem);
  flex-direction: column;
  justify-content: center;
  padding: clamp(1.5rem, 3vw, 2.5rem);
  border: 1px solid rgba(45, 32, 24, 0.08);
  border-radius: 2rem;
  background: rgba(255, 252, 248, 0.62);
  backdrop-filter: blur(10px);
  box-shadow: 0 24px 72px rgba(45, 32, 24, 0.08);
}

.booking-header {
  margin-bottom: clamp(1.75rem, 3vw, 2.4rem);
  text-align: start;
}

.booking-header__shape {
  width: 4rem;
  height: auto;
  margin-bottom: 1.5rem;
  opacity: 0;
  transform: translateY(20px);
}

.booking-header__shape.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.booking-header__title {
  margin: 0;
  color: #2d2018;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2.8rem, 4.8vw, 5.2rem);
  font-weight: 600;
  line-height: 0.96;
  letter-spacing: -0.045em;
  opacity: 0;
  transform: translateY(30px);
}

.booking-header__title.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.booking-header__description {
  max-width: 34rem;
  margin: 1.35rem 0 0;
  color: rgba(45, 32, 24, 0.68);
  font-size: 1rem;
  line-height: 1.8;
  opacity: 0;
  transform: translateY(20px);
}

.booking-header__description.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.7s ease-out;
  transition-delay: var(--delay, 0ms);
}

.booking-form {
  display: grid;
  gap: 1.5rem;
  opacity: 0;
  transform: translateY(40px);
}

.booking-form.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.booking-form__row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.booking-form__field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  opacity: 0;
  transform: translateY(20px);
}

.booking-form__field.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.booking-form__label {
  color: rgba(45, 32, 24, 0.7);
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.75rem;
  font-weight: 500;
}

.booking-form__input,
.booking-form__select {
  min-height: 3.5rem;
  padding: 0 1.25rem;
  border: 1px solid rgba(45, 32, 24, 0.15);
  border-radius: 1.5rem;
  background: rgba(255, 252, 248, 0.9);
  backdrop-filter: blur(10px);
  color: #2d2018;
  font-size: 1rem;
  transition: all 0.3s ease;
}

/* Style date input calendar icons */
.booking-form__input[type="date"]::-webkit-calendar-picker-indicator {
  filter: invert(0.5) sepia(1) saturate(2) hue-rotate(15deg);
  opacity: 0.7;
}

.booking-form__input[type="date"]::-webkit-calendar-picker-indicator:hover {
  opacity: 1;
}

.booking-form__input[type="date"]::-moz-calendar-picker-indicator {
  filter: invert(0.5) sepia(1) saturate(2) hue-rotate(15deg);
  opacity: 0.7;
}

.booking-form__input:focus,
.booking-form__select:focus {
  outline: none;
  border-color: rgba(138, 110, 72, 0.4);
  background: rgba(255, 252, 248, 0.95);
  box-shadow: 0 0 0 3px rgba(138, 110, 72, 0.1);
}

.booking-form__button {
  min-height: 3.5rem;
  padding: 0 2.5rem;
  border: 0;
  border-radius: 1.5rem;
  background: #8a6e48;
  color: #ffffff;
  font-size: 1rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  cursor: pointer;
  transition: all 0.3s ease;
  opacity: 0;
  transform: translateY(20px);
}

.booking-form__button.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.6s ease-out;
  transition-delay: var(--delay, 0ms);
}

.booking-form__button:hover {
  background: #6d583a;
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(138, 110, 72, 0.2);
}

.booking-media {
  margin: 0;
  overflow: hidden;
  border-radius: 2rem;
  box-shadow: 0 30px 80px rgba(45, 32, 24, 0.15);
  opacity: 0;
  transform: translateX(30px);
  min-height: clamp(30rem, 58vw, 40rem);
}

.booking-media.animate-in {
  opacity: 1;
  transform: translateX(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.booking-media__image {
  width: 100%;
  height: 100%;
  min-height: inherit;
  display: block;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.booking-footer {
  width: min(62rem, 100%);
  margin: clamp(2rem, 4vw, 3rem) auto 0;
  padding: clamp(1.15rem, 2.4vw, 1.75rem) clamp(1.25rem, 3vw, 2.5rem);
  border-block: 1px solid rgba(138, 110, 72, 0.22);
  color: #6d583a;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.35rem, 2.1vw, 2rem);
  font-weight: 500;
  line-height: 1.35;
  text-align: center;
  opacity: 0;
  transform: translateY(24px);
}

.booking-footer.animate-in {
  opacity: 1;
  transform: translateY(0);
  transition: all 0.8s ease-out;
  transition-delay: var(--delay, 0ms);
}

.booking-media:hover .booking-media__image {
  transform: scale(1.05);
}

/* Scroll animations */
.animate-on-scroll {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.8s ease-out;
}

.animate-on-scroll.animate-in {
  opacity: 1;
  transform: translateY(0);
}

.animate-on-scroll.animate-out {
  opacity: 0;
  transform: translateY(30px);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .booking-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .booking-header {
    margin-bottom: 2rem;
  }
  
  .booking-form {
    gap: 1.25rem;
  }
  
  .booking-form__row {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .booking-media__image {
    min-height: 400px;
  }
}

@media (max-width: 768px) {
  .shortcode-booking-form-native {
    padding: clamp(3rem, 6vw, 5rem) 0;
    min-height: auto;
  }
  
  .booking-shell {
    max-width: min(60rem, 92vw);
  }
  
  .booking-header {
    margin-bottom: 1.5rem;
  }
  
  .booking-header__title {
    font-size: clamp(2.2rem, 4vw, 3.2rem);
  }
  
  .booking-form {
    gap: 1rem;
  }
  
  .booking-form__row {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
  
  .booking-form__input,
  .booking-form__select {
    min-height: 3.25rem;
    padding: 0 1rem;
  }
  
  .booking-form__button {
    min-height: 3.25rem;
    padding: 0 2rem;
  }
  
  .booking-media__image {
    min-height: 300px;
  }
}

@media (max-width: 480px) {
  .booking-form__input,
  .booking-form__select {
    min-height: 3rem;
    padding: 0 0.875rem;
    font-size: 0.95rem;
  }
  
  .booking-form__button {
    min-height: 3rem;
    padding: 0 1.5rem;
    font-size: 0.9rem;
  }
  
  .booking-media__image {
    min-height: 250px;
  }
}
</style>
