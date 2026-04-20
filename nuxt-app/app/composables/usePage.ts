import {
  CMS_HOMEPAGE_TOKEN,
  cmsProxyRoutes,
  resolveCmsLocale,
  resolveCmsProxyRequestUrl,
} from "~~/shared/cms-routing";
import {
  parseShortcodeBlocks,
  type ShortcodeBlock,
} from "~/utils/shortcode";

export type PageData = {
  id: number;
  name: string;
  slug: string;
  description?: string | null;
  content?: string | null;
  image?: string | null;
  template?: string | null;
  status?: {
    value?: string;
    label?: string;
  };
  breadcrumb?: {
    enabled?: boolean;
    background_image?: string | null;
    background_image_url?: string | null;
  };
  seo?: {
    title?: string | null;
    description?: string | null;
    image?: string | null;
    robots?: string | null;
    canonical_path?: string | null;
    localizations?: Array<{
      locale: string;
      path: string;
    }> | null;
    received?: boolean;
  };
  created_at?: string;
  updated_at?: string;
};

export const usePage = async (
  slug: string,
  locale?: string,
): Promise<{ page: PageData; blocks: ShortcodeBlock[] }> => {
  const config = useRuntimeConfig();
  const endpoint =
    slug === CMS_HOMEPAGE_TOKEN
      ? cmsProxyRoutes.pages.homepage()
      : cmsProxyRoutes.pages.detail(slug);

  const response = await $fetch<{ data?: PageData }>(endpoint, {
    baseURL: resolveCmsProxyRequestUrl("/", {
      cmsProxyBaseUrl: config.public.cmsProxyBaseUrl,
      client: import.meta.client,
    }),
    query: locale ? { lang: resolveCmsLocale(locale) } : undefined,
  });

  const page = response.data;

  if (!page) {
    throw createError({
      statusCode: 404,
      statusMessage: "Page not found",
    });
  }

  return {
    page,
    blocks: parseShortcodeBlocks(page.content || ""),
  };
};
