<script setup lang="ts">
import SidebarWidgetCard from "~/components/sidebar-widgets/SidebarWidgetCard.vue";
import { useNewsletterSubscribe } from "~/composables/useNewsletterSubscribe";
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";

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
        placeholder="Nhập email của bạn"
        autocomplete="email"
        required
      >

      <button
        type="submit"
        class="footer-newsletter__button"
        :disabled="pending"
        :aria-label="pending ? 'Submitting newsletter' : 'Subscribe to newsletter'"
      >
        {{ pending ? "..." : "→" }}
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
  gap: 0.75rem;
}

.sidebar-newsletter__input {
  width: 100%;
  border: 1px solid rgba(63, 53, 45, 0.14);
  background: #fff;
  padding: 0.85rem 1rem;
  color: #2f241d;
}

.sidebar-newsletter__button {
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
  color: #fff3dc;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.75rem;
  line-height: 1.1;
}

.footer-newsletter__description {
  margin: 0 0 1rem;
  color: rgba(245, 234, 214, 0.78);
  line-height: 1.75;
}

.footer-newsletter__form {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  gap: 0.9rem;
  align-items: start;
}

.footer-newsletter__input {
  width: 100%;
  border: 1px solid rgba(255, 243, 220, 0.16);
  background: rgba(255, 255, 255, 0.06);
  padding: 0.95rem 1.15rem;
  color: #fff3dc;
}

.footer-newsletter__input::placeholder {
  color: rgba(255, 243, 220, 0.62);
}

.footer-newsletter__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 3.75rem;
  min-height: 3.75rem;
  border: 0;
  background: #b99247;
  color: #fff;
  font-size: 1.15rem;
  font-weight: 700;
  transition:
    background-color 0.2s ease,
    transform 0.2s ease;
}

.footer-newsletter__button:hover:not(:disabled) {
  background: #d0a552;
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
  color: #9fe7c8;
}

.footer-newsletter__feedback--error {
  color: #f7b2aa;
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
