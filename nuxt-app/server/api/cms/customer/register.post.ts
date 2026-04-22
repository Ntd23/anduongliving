import { buildAbsoluteUrl, normalizeSiteUrl } from "~~/shared/cms-routing";
import { cmsAppRoutes } from "~~/shared/cms-routing";
import { createForwardHeaders, forwardUpstreamCookies } from "~~/server/utils/http-upstream";

const extractErrors = (payload: any) => payload?.errors || payload?.data?.errors || payload?.data || null;
const appendFormEntries = (form: URLSearchParams, body: Record<string, any>) => {
  Object.entries(body).forEach(([key, value]) => {
    if (value === undefined || value === null || value === false) {
      return;
    }

    if (Array.isArray(value)) {
      value.forEach((item) => {
        if (item !== undefined && item !== null) {
          form.append(key, String(item));
        }
      });

      return;
    }

    form.append(key, value === true ? "1" : String(value));
  });
};

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig(event);
  const backendSiteUrl = normalizeSiteUrl(config.backendSiteUrl);
  const body = await readBody<Record<string, string | boolean | null | undefined>>(event);

  const form = new URLSearchParams();
  appendFormEntries(form, body);

  const response = await fetch(buildAbsoluteUrl(backendSiteUrl, "/register"), {
    method: "POST",
    headers: {
      ...createForwardHeaders(event, "application/json"),
      "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
      "x-requested-with": "XMLHttpRequest",
    },
    body: form.toString(),
  });

  forwardUpstreamCookies(event, response);

  const payload = await response.json().catch(() => null);

  if (response.ok && payload && !payload.error) {
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
    statusMessage: payload?.message || response.statusText || "Register failed",
    data: extractErrors(payload),
  });
});
