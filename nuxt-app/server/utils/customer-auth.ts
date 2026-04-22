import type { H3Event } from "h3";

import { buildAbsoluteUrl, cmsAppRoutes, normalizeSiteUrl } from "~~/shared/cms-routing";

const CUSTOMER_TOKEN_COOKIE = "nuxt_customer_token";

type CustomerProfilePayload = {
  id: number | string;
  first_name?: string | null;
  last_name?: string | null;
  name?: string | null;
  email?: string | null;
  avatar_url?: string | null;
  confirmed_at?: string | null;
};

export type CustomerSessionData = {
  authenticated: boolean;
  id?: number | string | null;
  firstName?: string | null;
  lastName?: string | null;
  name?: string | null;
  email?: string | null;
  avatarUrl?: string | null;
  confirmedAt?: string | null;
  overviewUrl?: string | null;
  loginUrl?: string | null;
  registerUrl?: string | null;
  logoutUrl?: string | null;
};

const getApiHeaders = (event: H3Event, token?: string | null) => {
  const config = useRuntimeConfig(event);

  return {
    Accept: "application/json",
    "Content-Type": "application/json",
    "X-API-KEY": String(config.apiKey || ""),
    "user-agent": getHeader(event, "user-agent") || "",
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
  };
};

const buildCustomerApiUrl = (event: H3Event, path: string) => {
  const config = useRuntimeConfig(event);
  const backendSiteUrl = normalizeSiteUrl(config.backendSiteUrl);

  return buildAbsoluteUrl(backendSiteUrl, `/api/v1/customer/${path.replace(/^\/+/, "")}`);
};

export const getCustomerAccessToken = (event: H3Event) => getCookie(event, CUSTOMER_TOKEN_COOKIE) || "";

export const setCustomerAccessToken = (event: H3Event, token: string) => {
  setCookie(event, CUSTOMER_TOKEN_COOKIE, token, {
    httpOnly: true,
    sameSite: "lax",
    secure: false,
    path: "/",
    maxAge: 60 * 60 * 24 * 30,
  });
};

export const clearCustomerAccessToken = (event: H3Event) => {
  deleteCookie(event, CUSTOMER_TOKEN_COOKIE, {
    path: "/",
  });
};

export const fetchCustomerProfile = async (event: H3Event): Promise<CustomerSessionData> => {
  const token = getCustomerAccessToken(event);

  if (!token) {
    return {
      authenticated: false,
      overviewUrl: cmsAppRoutes.customer.overview(),
      loginUrl: cmsAppRoutes.auth.login(),
      registerUrl: cmsAppRoutes.auth.register(),
      logoutUrl: "/logout",
    };
  }

  const response = await fetch(buildCustomerApiUrl(event, "me"), {
    headers: getApiHeaders(event, token),
  });

  if (response.status === 401) {
    clearCustomerAccessToken(event);

    return {
      authenticated: false,
      overviewUrl: cmsAppRoutes.customer.overview(),
      loginUrl: cmsAppRoutes.auth.login(),
      registerUrl: cmsAppRoutes.auth.register(),
      logoutUrl: "/logout",
    };
  }

  if (!response.ok) {
    throw createError({
      statusCode: response.status || 500,
      statusMessage: response.statusText || "Customer profile request failed",
    });
  }

  const payload = await response.json().catch(() => null) as { data?: CustomerProfilePayload } | null;
  const customer = payload?.data;

  if (!customer) {
    clearCustomerAccessToken(event);

    return {
      authenticated: false,
      overviewUrl: cmsAppRoutes.customer.overview(),
      loginUrl: cmsAppRoutes.auth.login(),
      registerUrl: cmsAppRoutes.auth.register(),
      logoutUrl: "/logout",
    };
  }

  return {
    authenticated: true,
    id: customer.id,
    firstName: customer.first_name || null,
    lastName: customer.last_name || null,
    name: customer.name || null,
    email: customer.email || null,
    avatarUrl: customer.avatar_url || null,
    confirmedAt: customer.confirmed_at || null,
    overviewUrl: cmsAppRoutes.customer.overview(),
    loginUrl: cmsAppRoutes.auth.login(),
    registerUrl: cmsAppRoutes.auth.register(),
    logoutUrl: "/logout",
  };
};

export const callCustomerApi = async <T>(
  event: H3Event,
  path: string,
  options?: {
    method?: string;
    body?: Record<string, unknown>;
    token?: string | null;
  },
): Promise<{ response: Response; payload: T | null }> => {
  const response = await fetch(buildCustomerApiUrl(event, path), {
    method: options?.method || "GET",
    headers: getApiHeaders(event, options?.token),
    body: options?.body ? JSON.stringify(options.body) : undefined,
  });

  const payload = await response.json().catch(() => null) as T | null;

  return { response, payload };
};
