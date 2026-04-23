import { cmsAppRoutes } from "~~/shared/routes/app";
import { buildAbsoluteUrl, normalizeSiteUrl } from "~~/shared/utils/url";
import { fetchCustomerProfile } from "~~/server/features/auth/customer-auth";
import { createForwardHeaders } from "~~/server/features/http/upstream";

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

const HEADER_EXTRAS_TIMEOUT_MS = 3000;

const withTimeoutSignal = () => {
  if (typeof AbortSignal !== "undefined" && typeof AbortSignal.timeout === "function") {
    return AbortSignal.timeout(HEADER_EXTRAS_TIMEOUT_MS);
  }

  return undefined;
};

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig(event);
  const siteUrl = normalizeSiteUrl(config.public.siteUrl);
  const backendSiteUrl = normalizeSiteUrl(config.backendSiteUrl);
  const apiKey = String(config.apiKey || "");

  const currencyPromise = (async () => {
    if (!siteUrl || !backendSiteUrl || !apiKey) {
      return [] as CurrencyItem[];
    }

    try {
      const response = await fetch(buildAbsoluteUrl(backendSiteUrl, "/api/v1/customer/header-extras"), {
        headers: {
          ...createForwardHeaders(event, "application/json"),
          "X-API-KEY": apiKey,
        },
        signal: withTimeoutSignal(),
      });

      if (!response.ok) {
        return [] as CurrencyItem[];
      }

      const payload = await response.json().catch(() => null) as { data?: { currencies?: CurrencyItem[] } } | null;
      const items = Array.isArray(payload?.data?.currencies) ? payload.data.currencies : [];

      return items
        .map((item) => ({
          title: normalizeText(item.title),
          href: buildAbsoluteUrl(siteUrl, item.href),
          active: Boolean(item.active),
        }))
        .filter((item) => item.title && item.href);
    } catch {
      return [] as CurrencyItem[];
    }
  })();

  const customerPromise = fetchCustomerProfile(event).catch(() => emptyPayload.customer);
  const [currenciesResult, customer] = await Promise.all([
    currencyPromise,
    customerPromise,
  ]);

  return {
    data: {
      currencies: currenciesResult,
      customer,
    },
  };
});
