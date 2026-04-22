import {
  buildAbsoluteUrl,
  cmsAppRoutes,
  normalizePath,
  resolveUrlOrigin,
  normalizeSiteUrl,
} from "~~/shared/cms-routing";
import { createForwardHeaders, forwardUpstreamCookies } from "~~/server/utils/http-upstream";

type CurrencyItem = {
  title: string;
  href: string;
  active: boolean;
};

type CustomerBlock = {
  authenticated: boolean;
  name?: string | null;
  avatarUrl?: string | null;
  overviewUrl?: string | null;
  loginUrl?: string | null;
  registerUrl?: string | null;
  logoutUrl?: string | null;
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

const extractCustomerBlock = (html: string, siteUrl: string, backendSiteUrl: string): CustomerBlock => {
  const overviewMatches = Array.from(
    html.matchAll(/<a\b[^>]*href=(["'])(.*?)\1[^>]*>([\s\S]*?)<\/a>/gi),
  ).filter((match) => /\/customer\/overview\b/i.test(match[2]));

  const customerLink =
    overviewMatches.find((match) => /customer-name|customer-avatar|Hi,/i.test(match[3])) ||
    overviewMatches[0];

  if (customerLink) {
    const avatarUrl = customerLink[3].match(/<img\b[^>]*src=(["'])(.*?)\1/i)?.[2] || null;
    let name = normalizeText(customerLink[3]);
    name = name.replace(/^Hi,\s*/i, "").trim();

    return {
      authenticated: true,
      name: name || null,
      avatarUrl: avatarUrl ? buildAbsoluteUrl(siteUrl, avatarUrl) : null,
      overviewUrl: cmsAppRoutes.customer.overview(),
      logoutUrl: buildAbsoluteUrl(backendSiteUrl || siteUrl, "/customer/logout"),
      loginUrl: cmsAppRoutes.auth.login(),
      registerUrl: cmsAppRoutes.auth.register(),
    };
  }

  const loginHref =
    html.match(/<a\b[^>]*href=(["'])(.*?\/login)\1[^>]*>/i)?.[2] ||
    "/login";
  const registerHref =
    html.match(/<a\b[^>]*href=(["'])(.*?\/register)\1[^>]*>/i)?.[2] ||
    "/register";

  return {
    authenticated: false,
    overviewUrl: cmsAppRoutes.customer.overview(),
    loginUrl: (loginHref || "").includes("/login") ? cmsAppRoutes.auth.login() : buildAbsoluteUrl(backendSiteUrl || siteUrl, loginHref || "/login"),
    registerUrl: (registerHref || "").includes("/register") ? cmsAppRoutes.auth.register() : buildAbsoluteUrl(backendSiteUrl || siteUrl, registerHref || "/register"),
    logoutUrl: buildAbsoluteUrl(backendSiteUrl || siteUrl, "/customer/logout"),
  };
};

const emptyPayload = (siteUrl: string, backendSiteUrl: string) => ({
  currencies: [] as CurrencyItem[],
  customer: {
    authenticated: false,
    overviewUrl: cmsAppRoutes.customer.overview(),
    loginUrl: cmsAppRoutes.auth.login(),
    registerUrl: cmsAppRoutes.auth.register(),
    logoutUrl: buildAbsoluteUrl(backendSiteUrl || siteUrl, "/customer/logout"),
  } satisfies CustomerBlock,
});

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig(event);
  const siteUrl = normalizeSiteUrl(config.public.siteUrl);
  const backendSiteUrl = normalizeSiteUrl(config.backendSiteUrl);
  const siteOrigin = resolveUrlOrigin(siteUrl);
  const backendOrigin = resolveUrlOrigin(backendSiteUrl);

  if (!siteUrl) {
    return { data: emptyPayload("", backendSiteUrl) };
  }

  if (!backendSiteUrl || (siteOrigin && backendOrigin && siteOrigin === backendOrigin)) {
    return { data: emptyPayload(siteUrl, backendSiteUrl) };
  }

  try {
    const response = await fetch(buildAbsoluteUrl(backendSiteUrl, normalizePath("/")), {
      headers: createForwardHeaders(event, "text/html,application/xhtml+xml"),
    });
    forwardUpstreamCookies(event, response);
    const html = await response.text();

    return {
      data: {
        currencies: extractCurrencyItems(html, siteUrl),
        customer: extractCustomerBlock(html, siteUrl, backendSiteUrl),
      },
    };
  } catch {
    return { data: emptyPayload(siteUrl, backendSiteUrl) };
  }
});
