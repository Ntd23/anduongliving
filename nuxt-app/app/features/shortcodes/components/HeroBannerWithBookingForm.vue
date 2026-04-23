<script setup lang="ts">
import { parseHeroBannerWithBookingFormBlock, type ShortcodeBlock } from "~/features/shortcodes/core";
import { useCheckAvailability } from "~/composables/useCheckAvailability";
import { useResolvedCmsLink } from "~/features/cms/data/useResolvedCmsLink";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseHeroBannerWithBookingFormBlock(props.block.raw));
const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
const resolveLink = useResolvedCmsLink();

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
const action = computed(() => resolveLink(section.value.action?.href));

const sectionStyle = computed(() => {
  const style: Record<string, string> = {};

  if (section.value.backgroundColor) {
    style.background = section.value.backgroundColor;
  }

  if (section.value.backgroundImage?.src) {
    style.backgroundImage = `url('${section.value.backgroundImage.src}')`;
    style.backgroundPosition = "center";
    style.backgroundRepeat = "no-repeat";
    style.backgroundSize = "cover";
  }

  return style;
});
</script>

<template>
  <section
    v-if="section.title || section.description || section.bookingForm"
    class="shortcode-hero-booking-native"
    :style="sectionStyle"
  >
    <div class="hero-booking-overlay" />
    <div class="hero-booking-shell">
      <div class="hero-booking-copy">
        <div class="hero-booking-copy__glass">
          <h1 v-if="section.title" class="hero-booking-copy__title">{{ section.title }}</h1>
          <p v-if="section.description" class="hero-booking-copy__description">{{ section.description }}</p>

          <NuxtLink v-if="action?.isInternal" :to="action.href" class="hero-booking-copy__button">
            {{ section.action?.label }}
          </NuxtLink>
          <a v-else-if="action" :href="action.href" class="hero-booking-copy__button">
            {{ section.action?.label }}
          </a>
        </div>
      </div>

      <div v-if="section.bookingForm" class="hero-booking-card">
        <h2 v-if="section.bookingForm.title" class="hero-booking-card__title">{{ section.bookingForm.title }}</h2>

        <form class="hero-booking-form" @submit.prevent="submit">
          <label class="hero-booking-form__field">
            <span>Check in</span>
            <input v-model="startDate" type="date" required>
          </label>
          <label class="hero-booking-form__field">
            <span>Check out</span>
            <input v-model="endDate" type="date" required>
          </label>
          <label class="hero-booking-form__field">
            <span>Adults</span>
            <input v-model="adults" :min="minAdults" :max="maxAdults" type="number" required>
          </label>
          <label class="hero-booking-form__field">
            <span>Children</span>
            <input v-model="children" min="0" type="number">
          </label>
          <label class="hero-booking-form__field">
            <span>Rooms</span>
            <input v-model="rooms" min="1" type="number" required>
          </label>

          <button type="submit" class="hero-booking-form__button">
            {{ section.bookingForm.submitLabel || "Check Availability" }}
          </button>
        </form>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-hero-booking-native">
    <div v-html="sanitizedHtml" />
  </section>
</template>

<style scoped>
.shortcode-hero-booking-native {
  position: relative;
  padding: clamp(5rem, 10vw, 8rem) 0;
  background-color: #1a120e;
  overflow: hidden;
}

.hero-booking-overlay {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 20% 80%, rgba(185, 130, 90, 0.12), transparent 30%),
    linear-gradient(0deg, rgba(18, 12, 9, 0.72) 0%, rgba(18, 12, 9, 0.5) 50%, rgba(18, 12, 9, 0.36) 100%);
}

.hero-booking-shell {
  position: relative;
  z-index: 1;
  width: min(calc(100% - 2rem), 78rem);
  margin: 0 auto;
  display: grid;
  gap: 2rem;
  grid-template-columns: minmax(0, 1.2fr) minmax(20rem, 0.92fr);
  align-items: center;
}

/* â”€â”€ Copy glass panel â”€â”€ */
.hero-booking-copy__glass {
  padding: clamp(1.5rem, 3vw, 2.5rem);
  border: 1px solid rgba(248, 243, 234, 0.14);
  border-radius: 1.75rem;
  background: rgba(24, 15, 10, 0.38);
  backdrop-filter: blur(14px);
  box-shadow:
    0 24px 64px rgba(0, 0, 0, 0.28),
    inset 0 1px 0 rgba(255, 248, 237, 0.06);
}

.hero-booking-copy__title {
  margin: 0;
  color: #fff6ea;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(3rem, 7vw, 5.8rem);
  line-height: 0.92;
}

.hero-booking-copy__description {
  max-width: 38rem;
  margin: 1.2rem 0 0;
  color: rgba(251, 243, 232, 0.82);
  line-height: 1.85;
}

.hero-booking-copy__button {
  display: inline-flex;
  margin-top: 1.6rem;
  padding: 0.75rem 1.35rem;
  border-radius: 999px;
  background: rgba(255, 250, 241, 0.14);
  border: 1px solid rgba(248, 243, 234, 0.16);
  color: #fff8ef;
  text-decoration: none;
  font-weight: 600;
  backdrop-filter: blur(8px);
  transition: background-color 0.2s ease;
}

.hero-booking-copy__button:hover {
  background: rgba(255, 250, 241, 0.22);
}

/* â”€â”€ Form card â”€â”€ */
.hero-booking-card {
  padding: clamp(1.4rem, 3vw, 1.8rem);
  border-radius: 1.8rem;
  border: 1px solid rgba(255, 252, 246, 0.08);
  background: rgba(255, 251, 245, 0.92);
  box-shadow: 0 28px 72px rgba(18, 12, 9, 0.2);
}

.hero-booking-card__title {
  margin: 0 0 1rem;
  color: #2f241d;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(1.9rem, 3vw, 2.8rem);
  text-align: center;
}

.hero-booking-form {
  display: grid;
  gap: 0.8rem;
}

.hero-booking-form__field {
  display: grid;
  gap: 0.35rem;
}

.hero-booking-form__field span {
  color: #6a5948;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.hero-booking-form__field input {
  min-height: 3.25rem;
  padding: 0 0.95rem;
  border: 1px solid rgba(107, 116, 79, 0.16);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.9);
  transition: border-color 0.2s ease;
}

.hero-booking-form__field input:focus {
  outline: none;
  border-color: rgba(107, 116, 79, 0.4);
}

.hero-booking-form__button {
  min-height: 3.3rem;
  border: 0;
  border-radius: 999px;
  background: linear-gradient(135deg, #6c744f, #5e6746);
  color: #fffaf1;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 12px 28px rgba(108, 116, 79, 0.18);
  transition: box-shadow 0.2s ease;
}

.hero-booking-form__button:hover {
  box-shadow: 0 16px 36px rgba(108, 116, 79, 0.24);
}

@media (max-width: 991px) {
  .hero-booking-shell {
    grid-template-columns: 1fr;
  }
}
</style>




