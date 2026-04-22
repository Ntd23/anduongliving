import type { H3Event } from "h3";

import {
  buildCmsApiUrl,
  resolveCmsApiBaseUrl,
  resolveCmsLocale,
} from "~~/shared/cms-routing";

export const fetchCmsApi = async <T>(
  event: H3Event,
  path: string,
  query?: Record<string, string | number | undefined | null>,
  fallbackMessage: string = "CMS API request failed",
): Promise<T> => {
  const config = useRuntimeConfig(event);
  const targetUrl = new URL(buildCmsApiUrl(resolveCmsApiBaseUrl(config.apiBaseUrl), path));

  Object.entries(query || {}).forEach(([key, value]) => {
    if (value === undefined || value === null || value === "") {
      return;
    }

    targetUrl.searchParams.set(
      key,
      key === "lang" && typeof value === "string" ? resolveCmsLocale(value) : String(value),
    );
  });

  try {
    return await $fetch<T>(targetUrl.toString(), {
      headers: {
        Accept: "application/json",
        "X-API-KEY": String(config.apiKey || ""),
        cookie: getHeader(event, "cookie") || "",
        "user-agent": getHeader(event, "user-agent") || "",
      },
    });
  } catch (error: any) {
    throw createError({
      statusCode: error?.statusCode || error?.status || 500,
      statusMessage: error?.statusMessage || error?.message || fallbackMessage,
      data: error?.data,
    });
  }
};
