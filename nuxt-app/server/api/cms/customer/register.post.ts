import { cmsAppRoutes } from "~~/shared/cms-routing";
import { callCustomerApi, setCustomerAccessToken } from "~~/server/utils/customer-auth";

const extractErrors = (payload: any) => payload?.errors || payload?.data?.errors || payload?.data || null;

export default defineEventHandler(async (event) => {
  const body = await readBody<Record<string, string | boolean | null | undefined>>(event);

  const { response, payload } = await callCustomerApi<{
    error?: boolean;
    data?: { token?: string | null };
    message?: string | null;
  }>(event, "register", {
    method: "POST",
    body: {
      first_name: body.first_name || "",
      last_name: body.last_name || "",
      email: body.email || "",
      password: body.password || "",
      password_confirmation: body.password_confirmation || "",
      agree_terms_and_policy: body.agree_terms_and_policy ? "1" : "0",
    },
  });

  if (response.ok && !payload?.error) {
    if (payload?.data?.token) {
      setCustomerAccessToken(event, payload.data.token);
    }

    return {
      error: false,
      message: payload?.message || null,
      data: {
        nextUrl: payload?.data?.token ? cmsAppRoutes.customer.overview() : cmsAppRoutes.auth.login(),
      },
    };
  }

  throw createError({
    statusCode: response.status || 500,
    statusMessage: payload?.message || response.statusText || "Register failed",
    data: extractErrors(payload),
  });
});
