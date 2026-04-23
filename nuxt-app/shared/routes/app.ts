import { stripLeadingSlash } from "../utils/url";

export const cmsAppRoutes = {
  home: () => "/",
  auth: {
    login: () => "/login",
    register: () => "/register",
  },
  customer: {
    overview: () => "/customer/overview",
  },
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
  services: {
    detail: (slug?: string | null) => `/services/${stripLeadingSlash(slug)}`,
  },
} as const;
