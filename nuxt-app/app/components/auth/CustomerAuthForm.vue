<script setup lang="ts">
import type { CustomerAuthField, CustomerAuthPageData } from "~~/shared/customer-auth";
import { cmsProxyRoutes, resolveCmsProxyRequestUrl } from "~~/shared/cms-routing";

type AuthSubmitResponse = {
  error?: boolean;
  message?: string | null;
  data?: {
    nextUrl?: string | null;
  };
};

const props = defineProps<{
  page: CustomerAuthPageData;
}>();

const config = useRuntimeConfig();
const localePath = useLocalePath();
const submitting = ref(false);
const formError = ref("");
const fieldErrors = ref<Record<string, string | string[]>>({});
const fieldValues = reactive<Record<string, string>>({});
const agreementValues = reactive<Record<string, boolean>>({});
const passwordVisibility = reactive<Record<string, boolean>>({});

const iconMap: Record<string, string> = {
  lock: "ph:lock-key",
  mail: "ph:envelope-simple",
  user: "ph:user",
  "user-plus": "ph:user-plus",
  phone: "ph:phone",
};

const resolveIcon = (icon?: string | null) => iconMap[icon || ""] || "ph:dot-outline";

const resolveFieldType = (field: CustomerAuthField) => {
  if (field.type !== "password") {
    return field.type;
  }

  return passwordVisibility[field.name] ? "text" : "password";
};

const resolveEndpoint = () =>
  resolveCmsProxyRequestUrl(
    props.page.mode === "register" ? cmsProxyRoutes.customer.register() : cmsProxyRoutes.customer.login(),
    {
      cmsProxyBaseUrl: config.public.cmsProxyBaseUrl,
      client: import.meta.client,
    },
  );

const alternateInternalTo = computed(() => {
  const target = props.page.actions.alternateUrl;

  if (!target || !target.startsWith("/")) {
    return null;
  }

  return localePath(target);
});

const fieldError = (name: string) => {
  const value = fieldErrors.value[name];

  return Array.isArray(value) ? value[0] || "" : value || "";
};

watchEffect(() => {
  Object.keys(fieldValues).forEach((key) => delete fieldValues[key]);
  Object.keys(agreementValues).forEach((key) => delete agreementValues[key]);
  Object.keys(passwordVisibility).forEach((key) => delete passwordVisibility[key]);

  props.page.fields.forEach((field) => {
    fieldValues[field.name] = "";

    if (field.type === "password") {
      passwordVisibility[field.name] = false;
    }
  });

  props.page.agreements.forEach((agreement) => {
    agreementValues[agreement.name] = Boolean(agreement.checked);
  });

  fieldErrors.value = {};
  formError.value = "";
});

const onSubmit = async () => {
  submitting.value = true;
  formError.value = "";
  fieldErrors.value = {};

  try {
    const body: Record<string, string> = {
      _token: props.page.csrfToken,
    };

    props.page.fields.forEach((field) => {
      body[field.name] = fieldValues[field.name] || "";
    });

    props.page.agreements.forEach((agreement) => {
      body[agreement.name] = agreementValues[agreement.name] ? "1" : "0";
    });

    const response = await $fetch<AuthSubmitResponse>(resolveEndpoint(), {
      method: "POST",
      body,
    });

    const nextUrl = response?.data?.nextUrl || "/customer/overview";

    if (import.meta.client) {
      window.location.assign(nextUrl);
      return;
    }

    await navigateTo(nextUrl, { external: /^https?:\/\//i.test(nextUrl) });
  } catch (error: any) {
    formError.value = error?.statusMessage || "Authentication failed";
    fieldErrors.value = (error?.data as Record<string, string | string[]>) || {};
  } finally {
    submitting.value = false;
  }
};
</script>

<template>
  <section class="customer-auth-form">
    <div class="customer-auth-form__card">
      <div class="customer-auth-form__header">
        <div class="customer-auth-form__icon-badge">
          <Icon :name="resolveIcon(page.card.icon)" class="customer-auth-form__icon" />
        </div>

        <div class="customer-auth-form__copy">
          <h2 class="customer-auth-form__title">
            {{ page.card.heading }}
          </h2>
          <p v-if="page.card.description" class="customer-auth-form__description">
            {{ page.card.description }}
          </p>
        </div>
      </div>

      <div v-if="formError" class="customer-auth-form__alert">
        {{ formError }}
      </div>

      <form class="customer-auth-form__body" @submit.prevent="onSubmit">
        <div
          v-for="field in page.fields"
          :key="field.name"
          v-show="field.visible"
          class="customer-auth-form__field"
        >
          <label :for="field.name" class="customer-auth-form__label">
            {{ field.label }}
            <span v-if="field.required" class="customer-auth-form__required">*</span>
          </label>

          <div class="customer-auth-form__input-shell" :class="{ 'customer-auth-form__input-shell--invalid': fieldError(field.name) }">
            <Icon :name="resolveIcon(field.icon)" class="customer-auth-form__field-icon" />

            <input
              :id="field.name"
              v-model="fieldValues[field.name]"
              :type="resolveFieldType(field)"
              :name="field.name"
              :placeholder="field.placeholder || undefined"
              :autocomplete="field.autocomplete || undefined"
              :required="field.required"
              class="customer-auth-form__input"
            >

            <button
              v-if="field.type === 'password'"
              type="button"
              class="customer-auth-form__visibility"
              :aria-label="passwordVisibility[field.name] ? 'Hide password' : 'Show password'"
              @click="passwordVisibility[field.name] = !passwordVisibility[field.name]"
            >
              <Icon :name="passwordVisibility[field.name] ? 'ph:eye-slash' : 'ph:eye'" />
            </button>
          </div>

          <p v-if="fieldError(field.name)" class="customer-auth-form__error">
            {{ fieldError(field.name) }}
          </p>
        </div>

        <div v-if="page.agreements.length" class="customer-auth-form__agreements">
          <label
            v-for="agreement in page.agreements"
            :key="agreement.name"
            class="customer-auth-form__agreement"
          >
            <input
              v-model="agreementValues[agreement.name]"
              type="checkbox"
              :name="agreement.name"
              class="customer-auth-form__checkbox"
            >
            <span>{{ agreement.label }}</span>
          </label>
          <p
            v-for="agreement in page.agreements"
            v-show="fieldError(agreement.name)"
            :key="`${agreement.name}-error`"
            class="customer-auth-form__error"
          >
            {{ fieldError(agreement.name) }}
          </p>
        </div>

        <div class="customer-auth-form__actions">
          <a
            v-if="page.actions.forgotPasswordUrl"
            :href="page.actions.forgotPasswordUrl"
            class="customer-auth-form__aux-link"
          >
            {{ page.actions.forgotPasswordLabel || "Quên mật khẩu?" }}
          </a>

          <button type="submit" class="customer-auth-form__submit" :disabled="submitting">
            <span>{{ page.actions.submitLabel }}</span>
            <Icon :name="submitting ? 'ph:spinner-gap' : 'ph:arrow-right'" :class="{ 'customer-auth-form__submit-icon--spinning': submitting }" />
          </button>
        </div>

        <p v-if="page.actions.alternateLabel" class="customer-auth-form__alternate">
          <span v-if="page.actions.alternatePrompt">{{ page.actions.alternatePrompt }}</span>
          <NuxtLink
            v-if="alternateInternalTo"
            :to="alternateInternalTo"
            class="customer-auth-form__alternate-link"
          >
            {{ page.actions.alternateLabel }}
          </NuxtLink>
          <a
            v-else-if="page.actions.alternateUrl"
            :href="page.actions.alternateUrl"
            class="customer-auth-form__alternate-link"
          >
            {{ page.actions.alternateLabel }}
          </a>
        </p>
      </form>
    </div>
  </section>
</template>

<style scoped>
.customer-auth-form__card {
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(68, 51, 40, 0.08);
  border-radius: 2rem;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(251, 247, 240, 0.98));
  box-shadow: 0 28px 70px rgba(55, 39, 28, 0.08);
}

.customer-auth-form__header {
  display: grid;
  gap: 1.25rem;
  padding: clamp(1.75rem, 4vw, 2.4rem);
  border-bottom: 1px solid rgba(68, 51, 40, 0.08);
  background: linear-gradient(180deg, rgba(251, 246, 239, 0.95), rgba(255, 255, 255, 0.65));
}

.customer-auth-form__icon-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 3.75rem;
  height: 3.75rem;
  border-radius: 1.2rem;
  background: rgba(215, 179, 116, 0.14);
  color: #8d623b;
}

.customer-auth-form__icon {
  font-size: 1.55rem;
}

.customer-auth-form__title {
  margin: 0;
  color: #231913;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: clamp(2rem, 4vw, 2.8rem);
  font-weight: 600;
  line-height: 0.95;
}

.customer-auth-form__description {
  max-width: 34rem;
  margin: 0.9rem 0 0;
  color: rgba(61, 45, 35, 0.72);
  font-size: 0.98rem;
  line-height: 1.75;
}

.customer-auth-form__alert {
  margin: 1.25rem 1.5rem 0;
  border: 1px solid rgba(178, 59, 42, 0.18);
  border-radius: 1rem;
  padding: 0.85rem 1rem;
  background: rgba(178, 59, 42, 0.08);
  color: #b23b2a;
}

.customer-auth-form__body {
  display: grid;
  gap: 1.2rem;
  padding: clamp(1.5rem, 4vw, 2.35rem);
}

.customer-auth-form__field {
  display: grid;
  gap: 0.55rem;
}

.customer-auth-form__label {
  color: #34261d;
  font-size: 0.84rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.customer-auth-form__required {
  color: #b23b2a;
}

.customer-auth-form__input-shell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-height: 3.6rem;
  padding-inline: 1rem;
  border: 1px solid rgba(68, 51, 40, 0.14);
  border-radius: 1.15rem;
  background: rgba(255, 255, 255, 0.9);
  transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

.customer-auth-form__input-shell:focus-within {
  border-color: rgba(167, 122, 84, 0.42);
  box-shadow: 0 0 0 0.22rem rgba(167, 122, 84, 0.11);
  transform: translateY(-1px);
}

.customer-auth-form__input-shell--invalid {
  border-color: rgba(178, 59, 42, 0.36);
  box-shadow: 0 0 0 0.18rem rgba(178, 59, 42, 0.08);
}

.customer-auth-form__field-icon,
.customer-auth-form__visibility {
  flex: 0 0 auto;
  color: rgba(90, 67, 50, 0.7);
  font-size: 1.1rem;
}

.customer-auth-form__input {
  flex: 1 1 auto;
  min-width: 0;
  padding: 0;
  border: 0;
  background: transparent;
  color: #231913;
  font-size: 1rem;
  outline: none;
}

.customer-auth-form__input::placeholder {
  color: rgba(101, 81, 65, 0.52);
}

.customer-auth-form__visibility {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 0;
  background: transparent;
  cursor: pointer;
}

.customer-auth-form__error {
  margin: 0;
  color: #b23b2a;
  font-size: 0.88rem;
  line-height: 1.45;
}

.customer-auth-form__agreements {
  display: grid;
  gap: 0.75rem;
  padding-top: 0.25rem;
}

.customer-auth-form__agreement {
  display: inline-flex;
  align-items: flex-start;
  gap: 0.7rem;
  color: rgba(53, 39, 29, 0.78);
  line-height: 1.55;
}

.customer-auth-form__checkbox {
  width: 1rem;
  height: 1rem;
  margin-top: 0.2rem;
}

.customer-auth-form__actions {
  display: grid;
  gap: 1rem;
  padding-top: 0.35rem;
}

.customer-auth-form__aux-link {
  justify-self: start;
  color: rgba(101, 74, 53, 0.9);
  font-size: 0.92rem;
  font-weight: 600;
  text-decoration: none;
}

.customer-auth-form__submit {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.7rem;
  min-height: 3.65rem;
  border: 0;
  border-radius: 999px;
  background: linear-gradient(135deg, #b98655, #91613a);
  color: #fff;
  font-size: 0.92rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
}

.customer-auth-form__submit:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 14px 28px rgba(145, 97, 58, 0.22);
}

.customer-auth-form__submit:disabled {
  opacity: 0.76;
  cursor: wait;
}

.customer-auth-form__submit-icon--spinning {
  animation: customer-auth-form-spin 0.8s linear infinite;
}

.customer-auth-form__alternate {
  margin: 0;
  color: rgba(53, 39, 29, 0.72);
  text-align: center;
}

.customer-auth-form__alternate-link {
  margin-left: 0.35rem;
  color: #85542c;
  font-weight: 700;
  text-decoration: none;
}

@keyframes customer-auth-form-spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 767px) {
  .customer-auth-form__header,
  .customer-auth-form__body {
    padding-inline: 1.2rem;
  }

  .customer-auth-form__submit {
    width: 100%;
  }
}
</style>
