import { forwardUpstreamCookies } from "~~/server/features/http/upstream";

const FORWARDED_HEADERS = [
  "accept",
  "content-type",
  "cookie",
  "user-agent",
  "x-forwarded-for",
  "x-forwarded-host",
  "x-forwarded-proto",
] as const;

export default defineEventHandler(async (event) => {
  const pathParam = getRouterParam(event, "path");
  const segments = Array.isArray(pathParam) ? pathParam : [pathParam].filter(Boolean);
  const targetPath = `/api/cms/${segments.join("/")}`;
  const method = event.method || "GET";

  const headers: Record<string, string> = {};

  for (const headerName of FORWARDED_HEADERS) {
    const value = getHeader(event, headerName);

    if (value) {
      headers[headerName] = value;
    }
  }

  const query = getQuery(event);
  const body =
    method === "GET" || method === "HEAD"
      ? undefined
      : headers["content-type"]?.includes("application/json")
        ? await readBody(event)
        : await readRawBody(event, false);

  try {
    const response = await $fetch.raw(targetPath, {
      method,
      query,
      headers,
      body,
    });

    setResponseStatus(event, response.status, response.statusText);
    forwardUpstreamCookies(event, response);

    for (const [headerName, headerValue] of response.headers.entries()) {
      if (["content-length", "set-cookie"].includes(headerName.toLowerCase())) {
        continue;
      }

      setHeader(event, headerName, headerValue);
    }

    return response._data;
  } catch (error: any) {
    throw createError({
      statusCode: error?.response?.status || error?.statusCode || 500,
      statusMessage: error?.response?.statusText || error?.statusMessage || "CMS proxy request failed",
      data: error?.data ?? error?.response?._data ?? null,
    });
  }
});
