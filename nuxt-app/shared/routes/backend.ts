import { DEFAULT_API_BASE_URL, DEFAULT_LOCAL_BACKEND_URL } from "../config/defaults";
import { resolveUrlOrigin, normalizeSiteUrl, stripLeadingSlash } from "../utils/url";

export const resolveCmsApiBaseUrl = (value?: string | null) =>
  normalizeSiteUrl(value) || DEFAULT_API_BASE_URL;

export const resolveCmsAssetBaseUrl = (apiBaseUrl?: string | null) =>
  resolveUrlOrigin(resolveCmsApiBaseUrl(apiBaseUrl));

export const resolveBackendSiteUrl = (options?: {
  explicitBaseUrl?: string | null;
  apiBaseUrl?: string | null;
  publicSiteUrl?: string | null;
  isDevelopment?: boolean;
}) => {
  const explicitBaseUrl = normalizeSiteUrl(options?.explicitBaseUrl);

  if (explicitBaseUrl) {
    return explicitBaseUrl;
  }

  const apiOrigin = resolveUrlOrigin(resolveCmsApiBaseUrl(options?.apiBaseUrl));
  const publicOrigin = resolveUrlOrigin(options?.publicSiteUrl);

  if (apiOrigin && apiOrigin !== publicOrigin) {
    return apiOrigin;
  }

  if (options?.isDevelopment) {
    return DEFAULT_LOCAL_BACKEND_URL;
  }

  return "";
};

export const buildCmsApiUrl = (apiBaseUrl: string, path: string) =>
  `${resolveCmsApiBaseUrl(apiBaseUrl)}/v1/${stripLeadingSlash(path)}`;

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
