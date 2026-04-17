export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig();
  const slug = getRouterParam(event, "slug") || "";
  const query = getQuery(event);
  const rawLang = typeof query.lang === "string" ? query.lang : undefined;
  const lang = rawLang === "vi" ? "vi_VN" : rawLang === "en" ? "en_US" : rawLang;

  if (!slug) {
    throw createError({
      statusCode: 400,
      statusMessage: "Missing page slug",
    });
  }

  const apiBaseUrl = config.apiBaseUrl || "http://anduongliving.test/api";
  const targetUrl = new URL(`${apiBaseUrl}/v1/pages/${slug}`);

  if (lang) {
    targetUrl.searchParams.set("lang", lang);
  }

  try {
    return await $fetch(targetUrl.toString(), {
      headers: {
        Accept: "application/json",
        "X-API-KEY": String(config.apiKey || ""),
      },
    });
  } catch (error: any) {
    throw createError({
      statusCode: error?.statusCode || error?.status || 500,
      statusMessage: error?.statusMessage || error?.message || "Failed to load page",
      data: error?.data,
    });
  }
});
