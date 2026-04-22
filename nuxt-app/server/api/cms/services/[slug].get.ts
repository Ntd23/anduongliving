import {
  buildAbsoluteUrl,
  cmsAppRoutes,
  normalizeSiteUrl,
  resolveUrlOrigin,
} from "~~/shared/cms-routing";

type ServiceDetailPayload = {
  name: string;
  slug: string;
  content: string;
  seo: {
    title?: string | null;
    description?: string | null;
    canonical_path?: string | null;
  };
};

const stripTags = (value: string) => value.replace(/<[^>]+>/g, " ");

const decodeHtml = (value: string) =>
  value
    .replace(/&nbsp;/gi, " ")
    .replace(/&amp;/gi, "&")
    .replace(/&quot;/gi, "\"")
    .replace(/&#039;|&#39;/gi, "'")
    .replace(/&lt;/gi, "<")
    .replace(/&gt;/gi, ">")
    .replace(/\s+/g, " ")
    .trim();

const normalizeText = (value?: string | null) => decodeHtml(stripTags(value || ""));

const extractTitle = (html: string) => {
  const title =
    normalizeText(html.match(/<meta\b[^>]*property=(["'])og:title\1[^>]*content=(["'])(.*?)\2/i)?.[3]) ||
    normalizeText(html.match(/<title>([\s\S]*?)<\/title>/i)?.[1]);

  return title.replace(/\s+[|\-–—]\s+.*$/, "").trim();
};

const replaceOrigin = (html: string, fromOrigin: string, toOrigin: string) => {
  if (!fromOrigin || !toOrigin || fromOrigin === toOrigin) {
    return html;
  }

  const escapedOrigin = fromOrigin.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
  return html.replace(new RegExp(escapedOrigin, "g"), toOrigin);
};

export default defineEventHandler(async (event) => {
  const slug = String(getRouterParam(event, "slug") || "").trim();

  if (!slug) {
    throw createError({
      statusCode: 400,
      statusMessage: "Missing service slug",
    });
  }

  const config = useRuntimeConfig(event);
  const siteUrl = normalizeSiteUrl(config.public.siteUrl);
  const backendSiteUrl = normalizeSiteUrl(config.backendSiteUrl);
  const siteOrigin = resolveUrlOrigin(siteUrl);
  const backendOrigin = resolveUrlOrigin(backendSiteUrl);

  if (!backendSiteUrl || !backendOrigin || (siteOrigin && backendOrigin === siteOrigin)) {
    throw createError({
      statusCode: 503,
      statusMessage: "Service detail backend is not configured",
    });
  }

  const servicePath = cmsAppRoutes.services.detail(slug);
  const targetUrl = buildAbsoluteUrl(backendSiteUrl, servicePath);

  try {
    const html = await $fetch<string>(targetUrl, {
      responseType: "text",
      headers: {
        Accept: "text/html,application/xhtml+xml",
        cookie: getHeader(event, "cookie") || "",
        "user-agent": getHeader(event, "user-agent") || "",
      },
    });

    const sectionHtml = html.match(/<section\b[^>]*>[\s\S]*?<\/section>/i)?.[0] || "";
    const content = replaceOrigin(sectionHtml, backendOrigin, siteOrigin || backendOrigin);

    if (!content) {
      throw createError({
        statusCode: 404,
        statusMessage: "Service detail not found",
      });
    }

    const payload: ServiceDetailPayload = {
      name: extractTitle(html) || normalizeText(slug.replace(/[-_]+/g, " ")),
      slug,
      content,
      seo: {
        title: extractTitle(html) || null,
        description: normalizeText(
          html.match(/<meta\b[^>]*name=(["'])description\1[^>]*content=(["'])(.*?)\2/i)?.[3],
        ) || null,
        canonical_path: servicePath,
      },
    };

    return { data: payload };
  } catch (error: any) {
    if (error?.statusCode || error?.status) {
      throw createError({
        statusCode: error.statusCode || error.status,
        statusMessage: error.statusMessage || error.message || "Failed to load service detail",
      });
    }

    throw createError({
      statusCode: 500,
      statusMessage: error?.message || "Failed to load service detail",
    });
  }
});
