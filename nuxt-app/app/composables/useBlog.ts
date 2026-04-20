import {
  cmsProxyRoutes,
  resolveCmsLocale,
  resolveCmsProxyRequestUrl,
} from "~~/shared/cms-routing";

export type BlogCategory = {
  id: number;
  name: string;
  slug: string;
  url?: string | null;
  description?: string | null;
  posts_count?: number | null;
  children?: BlogCategory[];
  parent?: BlogCategory | null;
};

export type BlogTag = {
  id: number;
  name: string;
  slug: string;
  description?: string | null;
  posts_count?: number | null;
};

export type BlogPostSummary = {
  id: number;
  name: string;
  slug: string;
  url?: string | null;
  description?: string | null;
  image?: string | null;
  categories?: BlogCategory[];
  tags?: BlogTag[];
  created_at?: string | null;
  updated_at?: string | null;
};

export type BlogPost = BlogPostSummary & {
  content?: string | null;
  views?: number | null;
  author?: {
    name: string;
    avatar_url?: string | null;
    bio?: string | null;
    socials?: Array<{
      name: string;
      url: string;
    }>;
  } | null;
  navigation?: {
    previous?: BlogPostSummary | null;
    next?: BlogPostSummary | null;
  } | null;
};

export type BlogCollectionMeta = {
  current_page?: number;
  last_page?: number;
  per_page?: number;
  total?: number;
};

export type BlogCollectionResponse<T> = {
  data: T[];
  meta?: BlogCollectionMeta;
  links?: Record<string, string | null>;
};

export type BlogSearchResponse = {
  items: BlogPostSummary[];
  query: string;
  count: number;
};

const fetchCmsBlog = async <T>(
  endpoint: string,
  locale?: string,
  query?: Record<string, string | number | undefined>,
): Promise<T> => {
  const config = useRuntimeConfig();
  const response = await $fetch<{
    data?: T;
    meta?: BlogCollectionMeta;
    links?: Record<string, string | null>;
  }>(endpoint, {
    baseURL: resolveCmsProxyRequestUrl("/", {
      cmsProxyBaseUrl: config.public.cmsProxyBaseUrl,
      client: import.meta.client,
    }),
    query: {
      ...(locale ? { lang: resolveCmsLocale(locale) } : {}),
      ...(query || {}),
    },
  });

  if (response.data === undefined) {
    throw createError({
      statusCode: 404,
      statusMessage: "Blog data not found",
    });
  }

  if (response.meta || response.links) {
    return {
      data: response.data,
      meta: response.meta,
      links: response.links,
    } as T;
  }

  return response.data;
};

export const useBlogPosts = (params?: {
  locale?: string;
  page?: number;
  per_page?: number;
  limit?: number;
  type?: "recent" | "popular";
}) =>
  fetchCmsBlog<BlogCollectionResponse<BlogPostSummary> | BlogPostSummary[]>(
    cmsProxyRoutes.blog.posts(),
    params?.locale,
    {
      page: params?.page,
      per_page: params?.per_page,
      limit: params?.limit,
      type: params?.type,
    },
  );

export const useFilteredBlogPosts = (params?: {
  locale?: string;
  page?: number;
  per_page?: number;
  search?: string;
  categories?: number | string;
  tags?: number | string;
}) =>
  fetchCmsBlog<BlogCollectionResponse<BlogPostSummary>>(
    cmsProxyRoutes.blog.filters(),
    params?.locale,
    {
      page: params?.page,
      per_page: params?.per_page,
      search: params?.search,
      categories: params?.categories,
      tags: params?.tags,
    },
  );

export const useBlogPost = (slug: string, locale?: string) =>
  fetchCmsBlog<BlogPost>(cmsProxyRoutes.blog.post(slug), locale);

export const useBlogSearch = (query: string, locale?: string, limit?: number) =>
  fetchCmsBlog<BlogSearchResponse>(cmsProxyRoutes.blog.search(), locale, {
    q: query,
    limit,
  });

export const useBlogCategories = (params?: {
  locale?: string;
  limit?: number;
  type?: "popular" | "all";
}) =>
  fetchCmsBlog<BlogCategory[]>(cmsProxyRoutes.blog.categories(), params?.locale, {
    limit: params?.limit,
    type: params?.type,
  });

export const useBlogCategory = async (slug: string, locale?: string) => {
  const categories = await useBlogCategories({ locale, type: "all" });
  const matched = categories.find((item) => item.slug === slug);

  if (matched) {
    return matched;
  }

  throw createError({
    statusCode: 404,
    statusMessage: "Blog category not found",
  });
};

export const useBlogTags = (params?: {
  locale?: string;
  limit?: number;
  type?: "popular" | "all";
}) =>
  fetchCmsBlog<BlogTag[]>(cmsProxyRoutes.blog.tags(), params?.locale, {
    limit: params?.limit,
    type: params?.type,
  });

export const useBlogTag = async (slug: string, locale?: string) => {
  const tags = await useBlogTags({ locale, type: "all" });
  const matched = tags.find((item) => item.slug === slug);

  if (matched) {
    return matched;
  }

  throw createError({
    statusCode: 404,
    statusMessage: "Blog tag not found",
  });
};

export const normalizeBlogPostCollection = (
  payload?: BlogCollectionResponse<BlogPostSummary> | BlogPostSummary[] | null,
): { items: BlogPostSummary[]; meta?: BlogCollectionMeta } => {
  if (!payload) {
    return { items: [] };
  }

  if (Array.isArray(payload)) {
    return { items: payload };
  }

  return {
    items: payload.data || [],
    meta: payload.meta,
  };
};
