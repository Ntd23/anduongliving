import { clearCustomerAccessToken, callCustomerApi, getCustomerAccessToken } from "~~/server/features/auth/customer-auth";

export default defineEventHandler(async (event) => {
  const token = getCustomerAccessToken(event);

  if (token) {
    await callCustomerApi(event, "logout", {
      token,
    }).catch(() => null);
  }

  clearCustomerAccessToken(event);

  return {
    error: false,
    data: {
      nextUrl: "/",
    },
  };
});
