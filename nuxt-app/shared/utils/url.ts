export type LocalePathResolver = (path: string, locale?: string) => string;

export const stripLeadingSlash = (value?: string | null) =>
  String(value || "").replace(/^\/+/, "");

export const normalizeSiteUrl = (value?: string | null) =>
  String(value || "").trim().replace(/\/+$/, "");

export const normalizePath = (value?: string | null) => {
  const path = String(value || "").trim();

  if (!path || path === "/") {
    return "/";
  }

  if (/^https?:\/\//i.test(path)) {
    return path;
  }

  return path.startsWith("/") ? path : `/${path}`;
};

export const buildAbsoluteUrl = (baseUrl: string, path?: string | null) => {
  const normalizedBaseUrl = normalizeSiteUrl(baseUrl);
  const normalizedPath = normalizePath(path);

  if (!normalizedBaseUrl || /^https?:\/\//i.test(normalizedPath)) {
    return normalizedPath;
  }

  return `${normalizedBaseUrl}${normalizedPath}`;
};

export const resolveUrlOrigin = (value?: string | null) => {
  const normalizedValue = normalizeSiteUrl(value);

  if (!normalizedValue) {
    return "";
  }

  try {
    return new URL(normalizedValue).origin;
  } catch {
    return normalizedValue;
  }
};

export const buildAbsoluteAssetUrl = (baseUrl: string, value?: string | null) => {
  if (!value) {
    return "";
  }

  try {
    return new URL(value).toString();
  } catch {
    return buildAbsoluteUrl(resolveUrlOrigin(baseUrl), value);
  }
};

export const splitUrlParts = (value: string) => {
  const hashIndex = value.indexOf("#");
  const hash = hashIndex >= 0 ? value.slice(hashIndex) : "";
  const withoutHash = hashIndex >= 0 ? value.slice(0, hashIndex) : value;
  const queryIndex = withoutHash.indexOf("?");
  const search = queryIndex >= 0 ? withoutHash.slice(queryIndex) : "";
  const pathname = queryIndex >= 0 ? withoutHash.slice(0, queryIndex) : withoutHash;

  return {
    pathname,
    search,
    hash,
  };
};

export const extractInternalPath = (
  value?: string | null,
  options?: {
    siteUrl?: string | null;
    currentOrigin?: string | null;
  },
) => {
  if (!value) {
    return null;
  }

  if (value.startsWith("/") && !value.startsWith("//")) {
    return value;
  }

  try {
    const url = new URL(value);

    if (options?.currentOrigin && url.origin === options.currentOrigin) {
      return `${url.pathname}${url.search}${url.hash}`;
    }

    const normalizedSiteUrl = normalizeSiteUrl(options?.siteUrl);

    if (normalizedSiteUrl) {
      const siteUrl = new URL(normalizedSiteUrl);

      if (url.origin === siteUrl.origin) {
        return `${url.pathname}${url.search}${url.hash}`;
      }
    }

    return null;
  } catch {
    return null;
  }
};

export const hasLocalePrefix = (path: string, localeCodes: string[]) => {
  const normalized = normalizePath(path);

  return localeCodes.some((code) => normalized === `/${code}` || normalized.startsWith(`/${code}/`));
};
