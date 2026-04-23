import { cmsAppRoutes } from "~~/shared/routes/app";
import { callCustomerApi, setCustomerAccessToken } from "~~/server/features/auth/customer-auth";

const extractErrors = (payload: any) => payload?.errors || payload?.data?.errors || payload?.data || null;

export default defineEventHandler(async (event) => {
  const body = await readBody<Record<string, string | boolean | null | undefined>>(event);

  const { response, payload } = await callCustomerApi<{
    error?: boolean;
    data?: { token?: string | null };
    message?: string | null;
  }>(event, "login", {
    method: "POST",
    body: {
      email: body.email || "",
      password: body.password || "",
    },
  });

  if (response.ok && !payload?.error && payload?.data?.token) {
    setCustomerAccessToken(event, payload.data.token);

    return {
      error: false,
      message: payload.message || null,
      data: {
        nextUrl: cmsAppRoutes.customer.overview(),
      },
    };
  }

  throw createError({
    statusCode: response.status || 500,
    statusMessage: payload?.message || response.statusText || "Login failed",
    data: extractErrors(payload),
  });
});
