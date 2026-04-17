<script setup lang="ts">
import { parseNewsletterBlock, type ShortcodeBlock } from "~/utils/shortcode";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const section = computed(() => parseNewsletterBlock(props.block.raw));
const email = ref("");
const pending = ref(false);
const feedback = ref<{ type: "success" | "error"; message: string } | null>(null);
const xsrfToken = useCookie<string | null>("XSRF-TOKEN");

const sectionStyle = computed(() =>
  section.value.backgroundColor ? { backgroundColor: section.value.backgroundColor } : undefined,
);

const submitNewsletter = async () => {
  if (!section.value.action || pending.value) {
    return;
  }

  pending.value = true;
  feedback.value = null;

  try {
    const formData = new FormData();
    formData.append("email", email.value);

    const response = await fetch(section.value.action, {
      method: "POST",
      body: formData,
      credentials: "include",
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
        ...(xsrfToken.value
          ? { "X-XSRF-TOKEN": decodeURIComponent(xsrfToken.value) }
          : {}),
      },
    });
    const payload = await response.json().catch(() => null);

    if (!response.ok || payload?.error) {
      throw new Error(payload?.message || "Unable to subscribe right now.");
    }

    email.value = "";
    feedback.value = {
      type: "success",
      message: payload?.message || "Subscribed successfully.",
    };
  } catch (error) {
    feedback.value = {
      type: "error",
      message:
        error instanceof Error ? error.message : "Unable to subscribe right now.",
    };
  } finally {
    pending.value = false;
  }
};
</script>

<template>
  <section
    v-if="section.title || section.subtitle || section.description || section.action"
    class="shortcode-newsletter"
    :style="sectionStyle"
  >
    <div v-if="section.floatingImage?.src" class="newsletter-decor">
      <img
        :src="section.floatingImage.src"
        :alt="section.floatingImage.alt || section.title || 'Newsletter decoration'"
        loading="lazy"
      >
    </div>

    <div class="container">
      <div class="newsletter-panel">
        <div class="newsletter-copy">
          <p v-if="section.subtitle" class="newsletter-eyebrow shortcode-narrative-eyebrow">
            {{ section.subtitle }}
          </p>
          <h2 v-if="section.title" class="newsletter-title shortcode-narrative-title">
            {{ section.title }}
          </h2>
          <p v-if="section.description" class="newsletter-description">
            {{ section.description }}
          </p>
        </div>

        <form class="newsletter-form" @submit.prevent="submitNewsletter">
          <label class="newsletter-field" for="newsletter-email">
            <input
              id="newsletter-email"
              v-model="email"
              type="email"
              name="email"
              :placeholder="section.placeholder"
              autocomplete="email"
              required
            >
          </label>
          <button type="submit" class="newsletter-submit" :disabled="pending">
            {{ pending ? "Submitting..." : section.buttonLabel }}
          </button>
        </form>

        <p
          v-if="feedback"
          class="newsletter-feedback"
          :class="`newsletter-feedback--${feedback.type}`"
        >
          {{ feedback.message }}
        </p>
      </div>
    </div>
  </section>

  <section v-else class="shortcode-newsletter">
    <div v-html="block.raw" />
  </section>
</template>

<style scoped>
.shortcode-newsletter {
  position: relative;
  overflow: hidden;
  padding: 5.5rem 0;
  background:
    linear-gradient(180deg, rgba(248, 243, 234, 0.72), rgba(237, 228, 214, 0.9));
}

.newsletter-decor {
  position: absolute;
  top: 1.25rem;
  left: clamp(-3rem, -1vw, -0.5rem);
  opacity: 0.35;
  pointer-events: none;
}

.newsletter-panel {
  position: relative;
  max-width: 58rem;
  margin: 0 auto;
  padding: clamp(2rem, 5vw, 3.5rem);
  border: 1px solid rgba(111, 117, 83, 0.12);
  border-radius: 2rem;
  background: rgba(255, 252, 246, 0.82);
  box-shadow: 0 30px 80px rgba(47, 36, 29, 0.08);
  backdrop-filter: blur(10px);
  text-align: center;
}

.newsletter-eyebrow {
  margin: 0 0 0.8rem;
  color: var(--retreat-olive);
}

.newsletter-title {
  margin: 0;
  color: var(--retreat-ink);
  font-size: clamp(2rem, 5vw, 3.8rem);
  line-height: 1.05;
}

.newsletter-description {
  max-width: 40rem;
  margin: 1rem auto 0;
  color: rgba(47, 36, 29, 0.76);
  font-size: 1.02rem;
}

.newsletter-form {
  display: flex;
  flex-direction: column;
  gap: 0.9rem;
  max-width: 40rem;
  margin: 2rem auto 0;
}

.newsletter-field {
  display: block;
}

.newsletter-field input {
  width: 100%;
  height: 3.7rem;
  padding: 0 1.15rem;
  border: 1px solid rgba(111, 117, 83, 0.18);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.88);
  color: var(--retreat-ink);
}

.newsletter-field input:focus {
  outline: 2px solid rgba(111, 117, 83, 0.18);
  border-color: var(--retreat-olive);
}

.newsletter-submit {
  height: 3.7rem;
  padding: 0 1.5rem;
  border: none;
  border-radius: 999px;
  background: linear-gradient(135deg, var(--retreat-olive), #5e6746);
  color: #fffdf9;
  font-weight: 700;
  letter-spacing: 0.02em;
  transition:
    transform 0.2s ease,
    opacity 0.2s ease;
}

.newsletter-submit:hover:enabled {
  transform: translateY(-1px);
}

.newsletter-submit:disabled {
  opacity: 0.7;
}

.newsletter-feedback {
  margin: 1rem 0 0;
  font-weight: 600;
}

.newsletter-feedback--success {
  color: #0f766e;
}

.newsletter-feedback--error {
  color: #b91c1c;
}

@media (min-width: 768px) {
  .newsletter-form {
    flex-direction: row;
    align-items: center;
  }

  .newsletter-field {
    flex: 1;
  }
}
</style>
