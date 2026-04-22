import { buildAbsoluteUrl, cmsAppRoutes, normalizePath, normalizeSiteUrl, resolveUrlOrigin } from "~~/shared/cms-routing";
import { fetchCustomerProfile } from "~~/server/utils/customer-auth";
import { createForwardHeaders } from "~~/server/utils/http-upstream";

type CurrencyItem = {
  title: string;
  href: string;
  active: boolean;
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

const extractCurrencyItems = (html: string, siteUrl: string): CurrencyItem[] => {
  const mobileBlock =
    html.match(/<div class="currency-switcher-mobile d-none">([\s\S]*?)<\/div>/i)?.[1] ||
    "";

  const sources = mobileBlock || html;
  const matches = Array.from(
    sources.matchAll(
      /<a\b[^>]*class=(["'])[^"']*currency-item[^"']*\1[^>]*href=(["'])(.*?)\2([^>]*)>([\s\S]*?)<\/a>/gi,
    ),
  );

  const items = matches
    .map((match) => ({
      title: normalizeText(match[5]),
      href: buildAbsoluteUrl(siteUrl, match[3]),
      active: /\bactive\b/i.test(match[0]),
    }))
    .filter((item) => item.title && item.href);

  if (items.length) {
    return items;
  }

  const currentCurrency = normalizeText(
    html.match(/id=(["'])currency-switcher-dropdown\1[^>]*>([\s\S]*?)<\/a>/i)?.[2],
  );

  return currentCurrency
    ? [{ title: currentCurrency, href: buildAbsoluteUrl(siteUrl, "/currency/switch"), active: true }]
    : [];
};

const emptyPayload = {
  currencies: [] as CurrencyItem[],
  customer: {
    authenticated: false,
    overviewUrl: cmsAppRoutes.customer.overview(),
    loginUrl: cmsAppRoutes.auth.login(),
    registerUrl: cmsAppRoutes.auth.register(),
    logoutUrl: "/logout",
  },
};

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig(event);
  const siteUrl = normalizeSiteUrl(config.public.siteUrl);
  const backendSiteUrl = normalizeSiteUrl(config.backendSiteUrl);
  const siteOrigin = resolveUrlOrigin(siteUrl);
  const backendOrigin = resolveUrlOrigin(backendSiteUrl);

  let currencies: CurrencyItem[] = [];

  if (siteUrl && backendSiteUrl && (!siteOrigin || !backendOrigin || siteOrigin !== backendOrigin)) {
    try {
      const response = await fetch(buildAbsoluteUrl(backendSiteUrl, normalizePath("/")), {
        headers: createForwardHeaders(event, "text/html,application/xhtml+xml"),
      });
      const html = await response.text();
      currencies = extractCurrencyItems(html, siteUrl);
    } catch {
      currencies = [];
    }
  }

  return {
    data: {
      currencies,
      customer: await fetchCustomerProfile(event).catch(() => emptyPayload.customer),
    },
  };
});
