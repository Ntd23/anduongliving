<script setup lang="ts">
import { cmsAppRoutes, cmsProxyRoutes, resolveCmsProxyRequestUrl } from "~~/shared/cms-routing";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

type AuthMode = "login" | "register";

const props = defineProps<{
  mode: AuthMode;
  html: string;
  csrfToken: string;
}>();

type AuthSubmitResponse = {
  error?: boolean;
  message?: string | null;
  data?: {
    nextUrl?: string | null;
  };
};

const containerRef = ref<HTMLElement | null>(null);
const submitting = ref(false);
const formError = ref("");
const formMessage = ref("");
const config = useRuntimeConfig();
const sanitizedHtml = useSanitizedCmsHtml(() => props.html);

const clearInjectedErrors = () => {
  const root = containerRef.value;

  if (!root) {
    return;
  }

  root.querySelectorAll(".nuxt-auth-error").forEach((node) => node.remove());
  root.querySelectorAll(".nuxt-auth-invalid").forEach((node) => node.classList.remove("nuxt-auth-invalid"));
};

const injectFieldErrors = (errors?: Record<string, string[]>) => {
  const root = containerRef.value;

  if (!root || !errors) {
    return;
  }

  Object.entries(errors).forEach(([name, messages]) => {
    const field = root.querySelector<HTMLElement>(`[name="${CSS.escape(name)}"]`);
    const message = Array.isArray(messages) ? messages[0] : String(messages || "");

    if (!field || !message) {
      return;
    }

    field.classList.add("nuxt-auth-invalid");

    const errorNode = document.createElement("div");
    errorNode.className = "nuxt-auth-error";
    errorNode.textContent = message;

    const wrapper =
      field.closest(".form-group") ||
      field.closest(".form-group-custom") ||
      field.parentElement;

    wrapper?.appendChild(errorNode);
  });
};

const resolveEndpoint = () =>
  resolveCmsProxyRequestUrl(
    props.mode === "register" ? cmsProxyRoutes.customer.register() : cmsProxyRoutes.customer.login(),
    {
      cmsProxyBaseUrl: config.public.cmsProxyBaseUrl,
      client: import.meta.client,
    },
  );

const handleInternalAuthNavigation = async (href: string) => {
  if (href === cmsAppRoutes.auth.login() || href === cmsAppRoutes.auth.register()) {
    await navigateTo(href);
    return true;
  }

  return false;
};

const onClick = async (event: MouseEvent) => {
  const target = event.target as HTMLElement | null;
  const passwordToggle = target?.closest<HTMLElement>("[data-bb-toggle-password]");

  if (passwordToggle) {
    event.preventDefault();

    const inputGroup = passwordToggle.closest(".input-group");
    const passwordField = inputGroup?.querySelector<HTMLInputElement>("[data-bb-password]");

    if (passwordField) {
      passwordField.type = passwordField.type === "password" ? "text" : "password";
    }

    return;
  }

  const anchor = target?.closest("a");

  if (!anchor) {
    return;
  }

  const rawHref = anchor.getAttribute("href");

  if (!rawHref || rawHref.startsWith("#") || anchor.target === "_blank") {
    return;
  }

  try {
    const resolved = new URL(rawHref, window.location.origin);
    const internalPath = `${resolved.pathname}${resolved.search}${resolved.hash}`;

    if (await handleInternalAuthNavigation(internalPath)) {
      event.preventDefault();
    }
  } catch {
    // Ignore invalid URLs and let the browser handle them.
  }
};

const onSubmit = async (event: Event) => {
  const form = event.target as HTMLFormElement | null;

  if (!form || form.tagName !== "FORM") {
    return;
  }

  event.preventDefault();
  submitting.value = true;
  formError.value = "";
  formMessage.value = "";
  clearInjectedErrors();

  try {
    const formData = new FormData(form);

    if (!formData.has("_token")) {
      formData.set("_token", props.csrfToken);
    }

    const body: Record<string, string | string[]> = {};
    formData.forEach((value, key) => {
      const normalized = String(value);

      if (key in body) {
        const current = body[key];
        body[key] = Array.isArray(current) ? [...current, normalized] : [current, normalized];
        return;
      }

      body[key] = normalized;
    });

    const response = await $fetch<AuthSubmitResponse>(resolveEndpoint(), {
      method: "POST",
      body,
    });

    formMessage.value = response?.message || "";
    await navigateTo(response?.data?.nextUrl || "/customer/overview", { external: true });
  } catch (error: any) {
    formError.value = error?.statusMessage || "Authentication failed";
    injectFieldErrors(error?.data);
  } finally {
    submitting.value = false;
  }
};
</script>

<template>
  <section class="auth-passthrough">
    <div class="auth-passthrough__container">
      <div v-if="formError" class="auth-passthrough__notice auth-passthrough__notice--error">
        {{ formError }}
      </div>
      <div v-else-if="formMessage" class="auth-passthrough__notice auth-passthrough__notice--success">
        {{ formMessage }}
      </div>

      <div
        ref="containerRef"
        class="auth-passthrough__content"
        :class="{ 'auth-passthrough__content--submitting': submitting }"
        v-html="sanitizedHtml"
        @click.capture="onClick"
        @submit.capture="onSubmit"
      />
    </div>
  </section>
</template>

<style scoped>
.auth-passthrough {
  padding-bottom: 2rem;
}

.auth-passthrough__container {
  position: relative;
}

.auth-passthrough__notice {
  margin: 1.5rem 0 0;
  border-radius: 1rem;
  padding: 0.9rem 1rem;
}

.auth-passthrough__notice--error {
  background: rgba(178, 59, 42, 0.08);
  border: 1px solid rgba(178, 59, 42, 0.18);
  color: #b23b2a;
}

.auth-passthrough__notice--success {
  background: rgba(87, 132, 88, 0.1);
  border: 1px solid rgba(87, 132, 88, 0.16);
  color: #43673f;
}

.auth-passthrough__content--submitting {
  opacity: 0.72;
  pointer-events: none;
}

.auth-passthrough__content :deep(.nuxt-auth-invalid) {
  border-color: #b23b2a !important;
  box-shadow: 0 0 0 0.18rem rgba(178, 59, 42, 0.1);
}

.auth-passthrough__content :deep(.nuxt-auth-error) {
  margin-top: 0.5rem;
  color: #b23b2a;
  font-size: 0.88rem;
  line-height: 1.45;
}

.auth-passthrough__content :deep(.input-group) {
  position: relative;
}

.auth-passthrough__content :deep(.input-password-toggle) {
  position: absolute;
  top: 0;
  right: 0;
  z-index: 2;
  display: inline-flex;
  height: 100%;
  cursor: pointer;
  align-items: center;
  padding: 0 0.95rem;
}

.auth-passthrough__content :deep([dir="rtl"] .input-password-toggle),
.auth-passthrough__content :deep(body[dir="rtl"] .input-password-toggle) {
  right: auto;
  left: 0;
}
</style>
