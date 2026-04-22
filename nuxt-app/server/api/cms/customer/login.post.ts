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

  const response = await fetch(buildAbsoluteUrl(backendSiteUrl, "/login"), {
    method: "POST",
    headers: {
      ...createForwardHeaders(event, "application/json"),
      "content-type": "application/x-www-form-urlencoded; charset=UTF-8",
      "x-requested-with": "XMLHttpRequest",
    },
    body: form.toString(),
  });

  forwardUpstreamCookies(event, response);

  if (response.status === 204) {
    return {
      error: false,
      data: {
        nextUrl: cmsAppRoutes.customer.overview(),
      },
    };
  }

  const contentType = response.headers.get("content-type") || "";
  const payload = contentType.includes("application/json")
    ? await response.json().catch(() => null)
    : await response.text().catch(() => "");

  throw createError({
    statusCode: response.status || 500,
    statusMessage:
      (typeof payload === "object" && payload?.message) ||
      response.statusText ||
      "Login failed",
    data: extractErrors(payload),
  });
});
