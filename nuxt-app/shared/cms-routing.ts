export const CMS_HOMEPAGE_TOKEN = "homepage";
export const CMS_MAIN_MENU_TOKEN = "main-menu";
export const CMS_SIDEBAR_MENU_TOKEN = "sidebar-menu";

export const CMS_LOCALE_MAP = {
  vi: "vi_VN",
  en: "en_US",
  ja: "ja_JP",
} as const;

const DEFAULT_DEV_HOST = "127.0.0.1";
const DEFAULT_DEV_PORT = 3000;
const DEFAULT_API_BASE_URL = "http://anduongliving.test/api";

type LocalePathResolver = (path: string, locale?: string) => string;

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

export const buildDevOrigin = (
  host: string = DEFAULT_DEV_HOST,
  port: number | string = DEFAULT_DEV_PORT,
) => `http://${host}:${Number(port || DEFAULT_DEV_PORT)}`;

export const resolveCmsProxyBaseUrl = (options?: {
  explicitBaseUrl?: string | null;
  isDevelopment?: boolean;
  devHost?: string;
  devPort?: number | string;
}) => {
  const explicitBaseUrl = normalizeSiteUrl(options?.explicitBaseUrl);

  if (explicitBaseUrl) {
    return explicitBaseUrl;
  }

  if (options?.isDevelopment) {
    return buildDevOrigin(options?.devHost || DEFAULT_DEV_HOST, options?.devPort || DEFAULT_DEV_PORT);
  }

  return "";
};

export const resolveCmsApiBaseUrl = (value?: string | null) =>
  normalizeSiteUrl(value) || DEFAULT_API_BASE_URL;

export const buildCmsApiUrl = (apiBaseUrl: string, path: string) =>
  `${resolveCmsApiBaseUrl(apiBaseUrl)}/v1/${stripLeadingSlash(path)}`;

export const resolveCmsLocale = (locale?: string) => {
  const normalizedLocale = String(locale || "").trim();

  if (!normalizedLocale) {
    return CMS_LOCALE_MAP.vi;
  }

  if (Object.values(CMS_LOCALE_MAP).includes(normalizedLocale as (typeof CMS_LOCALE_MAP)[keyof typeof CMS_LOCALE_MAP])) {
    return normalizedLocale;
  }

  return CMS_LOCALE_MAP[normalizedLocale as keyof typeof CMS_LOCALE_MAP] || normalizedLocale;
};

export const cmsAppRoutes = {
  home: () => "/",
  page: (slug?: string | null) => {
    const normalizedSlug = stripLeadingSlash(slug);
    return normalizedSlug ? `/${normalizedSlug}` : "/";
  },
  blog: {
    index: () => "/blog",
    post: (slug?: string | null) => `/blog/${stripLeadingSlash(slug)}`,
    category: (slug?: string | null) => `/blog/category/${stripLeadingSlash(slug)}`,
    tag: (slug?: string | null) => `/blog/tag/${stripLeadingSlash(slug)}`,
    search: () => "/blog/search",
  },
} as const;

export const cmsProxyRoutes = {
  pages: {
    homepage: () => "/api/cms/pages/homepage",
    detail: (slug: string) => `/api/cms/pages/${stripLeadingSlash(slug)}`,
  },
  blog: {
    posts: () => "/api/cms/blog/posts",
    post: (slug: string) => `/api/cms/blog/posts/${stripLeadingSlash(slug)}`,
    filters: () => "/api/cms/blog/posts/filters",
    categories: () => "/api/cms/blog/categories",
    category: (slug: string) => `/api/cms/blog/categories/${stripLeadingSlash(slug)}`,
    tags: () => "/api/cms/blog/tags",
    tag: (slug: string) => `/api/cms/blog/tags/${stripLeadingSlash(slug)}`,
    search: () => "/api/cms/blog/search",
  },
  menus: {
    main: () => "/api/cms/menus/main-menu",
    detail: (slugOrId: string | number) => `/api/cms/menus/${stripLeadingSlash(String(slugOrId))}`,
    location: (location: string) => `/api/cms/menus/location/${stripLeadingSlash(location)}`,
  },
  theme: {
    options: () => "/api/cms/theme/options",
  },
  widgets: {
    sidebar: (sidebarId: string) => `/api/cms/widgets/sidebar/${stripLeadingSlash(sidebarId)}`,
  },
} as const;

export const cmsApiRoutes = {
  pages: {
    homepage: () => "pages/homepage",
    detail: (slug: string) => `pages/${stripLeadingSlash(slug)}`,
  },
  blog: {
    posts: () => "posts",
    post: (slug: string) => `posts/${stripLeadingSlash(slug)}`,
    filters: () => "posts/filters",
    categories: () => "categories",
    category: (slug: string) => `categories/${stripLeadingSlash(slug)}`,
    tags: () => "tags",
    tag: (slug: string) => `tags/${stripLeadingSlash(slug)}`,
    search: () => "search",
  },
  menus: {
    main: () => "menus/main-menu",
    detail: (slugOrId: string | number) => `menus/${stripLeadingSlash(String(slugOrId))}`,
    location: (location: string) => `menus/location/${stripLeadingSlash(location)}`,
  },
  theme: {
    options: () => "theme/options/public",
  },
  widgets: {
    sidebar: (sidebarId: string) => `widgets/sidebar/${stripLeadingSlash(sidebarId)}`,
  },
} as const;

export const resolveCmsProxyRequestUrl = (
  path: string,
  options?: {
    cmsProxyBaseUrl?: string | null;
    client?: boolean;
  },
) => {
  const normalizedPath = normalizePath(path);

  if (options?.client && options.cmsProxyBaseUrl) {
    return buildAbsoluteUrl(options.cmsProxyBaseUrl, normalizedPath);
  }

  return normalizedPath;
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

export const normalizeCmsInternalPath = (
  value: string,
  options?: {
    referenceType?: string | null;
    referenceSlug?: string | null;
  },
) => {
  const { pathname, search, hash } = splitUrlParts(value);
  const trimmedPath = pathname.replace(/\/+$/, "") || "/";
  const slug = trimmedPath.startsWith("/news/") ? trimmedPath.slice("/news/".length) : "";
  const referenceType = options?.referenceType || "";

  if (trimmedPath === "/news") {
    return `${cmsAppRoutes.blog.index()}${search}${hash}`;
  }

  if (slug) {
    if (referenceType.includes("Category")) {
      return `${cmsAppRoutes.blog.category(slug)}${search}${hash}`;
    }

    if (referenceType.includes("Tag")) {
      return `${cmsAppRoutes.blog.tag(slug)}${search}${hash}`;
    }

    if (referenceType.includes("Post")) {
      return `${cmsAppRoutes.blog.post(slug)}${search}${hash}`;
    }
  }

  if ((trimmedPath === "/" || trimmedPath === "") && referenceType.includes("Page") && options?.referenceSlug) {
    return `${cmsAppRoutes.page(options.referenceSlug)}${search}${hash}`;
  }

  return `${trimmedPath}${search}${hash}`;
};

export const hasLocalePrefix = (path: string, localeCodes: string[]) => {
  const normalized = normalizePath(path);

  return localeCodes.some((code) => normalized === `/${code}` || normalized.startsWith(`/${code}/`));
};

export const localizeCmsPath = (
  path: string,
  options: {
    locale?: string;
    localeCodes: string[];
    localePath: LocalePathResolver;
  },
) => {
  if (hasLocalePrefix(path, options.localeCodes)) {
    return path;
  }

  return options.localePath(path, options.locale);
};

export const sanitizeCmsHtmlContent = (value?: string | null, siteUrl?: string | null) => {
  if (!value) {
    return "";
  }

  let output = value
    .replace(/<script\b[^>]*>[\s\S]*?<\/script>/gim, "")
    .replace(/<script\b[^>]*\/?>/gim, "")
    .replace(/<style\b[^>]*>[\s\S]*?<\/style>/gim, "")
    .replace(/<(?:style|link|meta|base)\b[^>]*>/gim, "")
    .replace(/\son[a-z]+\s*=\s*(".*?"|'.*?'|[^\s>]+)/gim, "")
    .replace(/\s(href|src|xlink:href)\s*=\s*(['"])\s*javascript:[^'"]*\2/gim, " $1=\"#\"")
    .replace(/\s(href|src|xlink:href)\s*=\s*(javascript:[^\s>]+)/gim, " $1=\"#\"");
  const normalizedSiteUrl = normalizeSiteUrl(siteUrl);

  if (normalizedSiteUrl) {
    const escapedSiteUrl = normalizedSiteUrl.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    output = output.replace(new RegExp(`${escapedSiteUrl}/news`, "g"), cmsAppRoutes.blog.index());
  }

  output = output.replace(/href=(['"])\/news\b/gi, `href=$1${cmsAppRoutes.blog.index()}`);

  return output;
};
