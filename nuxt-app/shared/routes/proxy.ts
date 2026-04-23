import { buildAbsoluteUrl, normalizePath, stripLeadingSlash } from "../utils/url";

export const cmsProxyRoutes = {
  customer: {
    authPage: () => "/cms-proxy/customer/auth-page",
    login: () => "/cms-proxy/customer/login",
    register: () => "/cms-proxy/customer/register",
    session: () => "/cms-proxy/customer/session",
    logout: () => "/cms-proxy/customer/logout",
  },
  pages: {
    homepage: () => "/cms-proxy/pages/homepage",
    detail: (slug: string) => `/cms-proxy/pages/${stripLeadingSlash(slug)}`,
  },
  blog: {
    posts: () => "/cms-proxy/blog/posts",
    post: (slug: string) => `/cms-proxy/blog/posts/${stripLeadingSlash(slug)}`,
    filters: () => "/cms-proxy/blog/posts/filters",
    categories: () => "/cms-proxy/blog/categories",
    category: (slug: string) => `/cms-proxy/blog/categories/${stripLeadingSlash(slug)}`,
    tags: () => "/cms-proxy/blog/tags",
    tag: (slug: string) => `/cms-proxy/blog/tags/${stripLeadingSlash(slug)}`,
    search: () => "/cms-proxy/blog/search",
  },
  menus: {
    main: () => "/cms-proxy/menus/main-menu",
    detail: (slugOrId: string | number) => `/cms-proxy/menus/${stripLeadingSlash(String(slugOrId))}`,
    location: (location: string) => `/cms-proxy/menus/location/${stripLeadingSlash(location)}`,
  },
  theme: {
    options: () => "/cms-proxy/theme/options",
    headerExtras: () => "/cms-proxy/theme/header-extras",
  },
  services: {
    detail: (slug: string) => `/cms-proxy/services/${stripLeadingSlash(slug)}`,
  },
  widgets: {
    sidebar: (sidebarId: string) => `/cms-proxy/widgets/sidebar/${stripLeadingSlash(sidebarId)}`,
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
