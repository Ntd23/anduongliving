import {
  buildAbsoluteUrl,
  cmsAppRoutes,
  normalizeSiteUrl,
} from "~~/shared/cms-routing";
import { createForwardHeaders, forwardUpstreamCookies } from "~~/server/utils/http-upstream";

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

const extractCustomerProfile = (html: string, siteUrl: string, backendSiteUrl: string) => {
  const overviewMatches = Array.from(
    html.matchAll(/<a\b[^>]*href=(["'])(.*?)\1[^>]*>([\s\S]*?)<\/a>/gi),
  ).filter((match) => /\/customer\/overview\b/i.test(match[2]));

  const customerLink =
    overviewMatches.find((match) => /customer-name|customer-avatar|hi,/i.test(match[3])) ||
    overviewMatches[0];

  const avatarUrl = customerLink?.[3].match(/<img\b[^>]*src=(["'])(.*?)\1/i)?.[2] || null;
  const name = normalizeText(customerLink?.[3]).replace(/^hi,\s*/i, "").trim();

  return {
    authenticated: true,
    name: name || null,
    avatarUrl: avatarUrl ? buildAbsoluteUrl(siteUrl, avatarUrl) : null,
    overviewUrl: cmsAppRoutes.customer.overview(),
    logoutUrl: buildAbsoluteUrl(backendSiteUrl || siteUrl, "/customer/logout"),
  };
};

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig(event);
  const siteUrl = normalizeSiteUrl(config.public.siteUrl);
  const backendSiteUrl = normalizeSiteUrl(config.backendSiteUrl);
  const targetUrl = buildAbsoluteUrl(backendSiteUrl, "/customer/overview");

  if (!targetUrl) {
    return {
      data: {
        authenticated: false,
        overviewUrl: cmsAppRoutes.customer.overview(),
        logoutUrl: buildAbsoluteUrl(backendSiteUrl || siteUrl, "/customer/logout"),
      },
    };
  }

  const response = await fetch(targetUrl, {
    headers: createForwardHeaders(event, "text/html,application/xhtml+xml"),
    redirect: "manual",
  });

  forwardUpstreamCookies(event, response);

  if (response.status >= 300 && response.status < 400) {
    return {
      data: {
        authenticated: false,
        overviewUrl: cmsAppRoutes.customer.overview(),
        logoutUrl: buildAbsoluteUrl(backendSiteUrl || siteUrl, "/customer/logout"),
      },
    };
  }

  const html = await response.text().catch(() => "");

  if (/action=(["']).*?\/login\1/i.test(html) || /botble-hotel-forms-fronts-auth-login-form/i.test(html)) {
    return {
      data: {
        authenticated: false,
        overviewUrl: cmsAppRoutes.customer.overview(),
        logoutUrl: buildAbsoluteUrl(backendSiteUrl || siteUrl, "/customer/logout"),
      },
    };
  }

  return {
    data: extractCustomerProfile(html, siteUrl, backendSiteUrl),
  };
});
