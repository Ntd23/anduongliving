<script setup lang="ts">
import SidebarWidgetCard from "~/features/cms/ui/sidebar-widgets/SidebarWidgetCard.vue";
import { useNewsletterSubscribe } from "~/composables/useNewsletterSubscribe";
import type { SidebarWidgetManifest } from "~/features/cms/ui/sidebar-widgets/registry";

const props = defineProps<{
  widget: SidebarWidgetManifest;
  variant?: "sidebar" | "footer";
}>();

const actionUrl = computed(() => {
  const value = props.widget.meta?.action_url;
  return typeof value === "string" && value ? value : null;
});
const title = computed(() => String(props.widget.data?.title || "Subscribe To Our Newsletter"));
const description = computed(() => {
  const value = props.widget.data?.description;
  return typeof value === "string" && value.trim() ? value : null;
});
const variant = computed(() => props.variant || "sidebar");

const { email, pending, feedback, submit } = useNewsletterSubscribe(actionUrl);
</script>

<template>
  <SidebarWidgetCard v-if="variant === 'sidebar'" :title="title" :description="description">
    <form class="sidebar-newsletter" @submit.prevent="submit">
      <input
        v-model="email"
        type="email"
        class="sidebar-newsletter__input"
        placeholder="Your email address"
        autocomplete="email"
        required
      >
      <button type="submit" class="sidebar-newsletter__button" :disabled="pending">
        {{ pending ? "Submitting..." : "Subscribe" }}
      </button>
    </form>

    <p
      v-if="feedback"
      class="sidebar-newsletter__feedback"
      :class="`sidebar-newsletter__feedback--${feedback.type}`"
    >
      {{ feedback.message }}
    </p>
  </SidebarWidgetCard>

  <article v-else class="footer-newsletter">
    <h2 class="footer-newsletter__title">
      {{ title }}
    </h2>

    <p v-if="description" class="footer-newsletter__description">
      {{ description }}
    </p>

    <form class="footer-newsletter__form" @submit.prevent="submit">
      <input
        v-model="email"
        type="email"
        class="footer-newsletter__input"
        placeholder="Enter your email"
        autocomplete="email"
        required
      >

      <button
        type="submit"
        class="footer-newsletter__button"
        :disabled="pending"
        :aria-label="pending ? 'Submitting newsletter' : 'Subscribe to newsletter'"
      >
        {{ pending ? "..." : "->" }}
      </button>
    </form>

    <p
      v-if="feedback"
      class="footer-newsletter__feedback"
      :class="`footer-newsletter__feedback--${feedback.type}`"
    >
      {{ feedback.message }}
    </p>
  </article>
</template>

<style scoped>
.sidebar-newsletter {
  display: grid;
  gap: 0.8rem;
}

.sidebar-newsletter__input {
  width: 100%;
  min-height: 3.55rem;
  border: 1px solid rgba(56, 41, 30, 0.1);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.82);
  padding: 0.9rem 1.15rem;
  color: var(--retreat-ink);
}

.sidebar-newsletter__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3.35rem;
  border: 0;
  border-radius: 999px;
  background: linear-gradient(180deg, rgba(107, 113, 83, 0.98), rgba(83, 91, 60, 0.98));
  color: #fffdf8;
  font-size: 0.76rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.sidebar-newsletter__button:hover:enabled {
  transform: translateY(-1px);
}

.sidebar-newsletter__button:disabled {
  opacity: 0.7;
}

.sidebar-newsletter__feedback {
  margin: 0.9rem 0 0;
  font-weight: 600;
}

.sidebar-newsletter__feedback--success {
  color: #0f766e;
}

.sidebar-newsletter__feedback--error {
  color: #b91c1c;
}

.footer-newsletter__title {
  margin: 0 0 1rem;
  color: #fff9f1;
  font-family: var(--font-display);
  font-size: 2rem;
  line-height: 1;
}

.footer-newsletter__description {
  margin: 0 0 1rem;
  color: rgba(248, 244, 236, 0.7);
  line-height: 1.85;
}

.footer-newsletter__form {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  gap: 0.9rem;
  align-items: start;
}

.footer-newsletter__input {
  width: 100%;
  border: 1px solid rgba(255, 249, 241, 0.14);
  border-radius: 999px;
  background: rgba(255, 249, 241, 0.05);
  padding: 1rem 1.2rem;
  color: #fff9f1;
}

.footer-newsletter__input::placeholder {
  color: rgba(255, 249, 241, 0.52);
}

.footer-newsletter__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 3.6rem;
  min-height: 3.6rem;
  border: 0;
  border-radius: 999px;
  background: linear-gradient(180deg, #8f7659 0%, #72634d 100%);
  color: #fffdf8;
  font-size: 1rem;
  font-weight: 700;
  transition:
    background-color 0.2s ease,
    transform 0.2s ease;
}

.footer-newsletter__button:hover:not(:disabled) {
  background: linear-gradient(180deg, #a17f5c 0%, #7d6950 100%);
  transform: translateY(-1px);
}

.footer-newsletter__button:disabled {
  opacity: 0.7;
}

.footer-newsletter__feedback {
  margin: 0.9rem 0 0;
  font-weight: 600;
}

.footer-newsletter__feedback--success {
  color: #b8e2cd;
}

.footer-newsletter__feedback--error {
  color: #f0afa5;
}

@media (max-width: 767px) {
  .footer-newsletter__form {
    grid-template-columns: 1fr;
  }

  .footer-newsletter__button {
    width: 100%;
  }
}
</style>



