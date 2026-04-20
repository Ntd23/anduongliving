import { toValue, type MaybeRefOrGetter } from "vue";

export const useNewsletterSubscribe = (
  actionUrl: MaybeRefOrGetter<string | null | undefined>,
) => {
  const email = ref("");
  const pending = ref(false);
  const feedback = ref<{ type: "success" | "error"; message: string } | null>(null);
  const xsrfToken = useCookie<string | null>("XSRF-TOKEN");

  const submit = async () => {
    const target = toValue(actionUrl);

    if (!target || pending.value) {
      return;
    }

    pending.value = true;
    feedback.value = null;

    try {
      const formData = new FormData();
      formData.append("email", email.value);

      const response = await fetch(target, {
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
        message: error instanceof Error ? error.message : "Unable to subscribe right now.",
      };
    } finally {
      pending.value = false;
    }
  };

  return {
    email,
    pending,
    feedback,
    submit,
  };
};
