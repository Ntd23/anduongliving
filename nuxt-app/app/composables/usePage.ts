import {
  parseShortcodeBlocks,
  type ShortcodeBlock,
} from "~/utils/shortcode";

export const CMS_HOMEPAGE_TOKEN = "homepage";
export const CMS_LOCALE_MAP: Record<string, string> = {
  vi: "vi_VN",
  en: "en_US",
  ja: "ja_JP",
};

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

export const resolveCmsLocale = (locale?: string) =>
  CMS_LOCALE_MAP[locale || ""] || "vi_VN";

export const usePage = async (
  slug: string,
  locale?: string,
): Promise<{ page: PageData; blocks: ShortcodeBlock[] }> => {
  const endpoint =
    slug === CMS_HOMEPAGE_TOKEN
      ? "/api/cms/pages/homepage"
      : `/api/cms/pages/${slug}`;

  const response = await $fetch<{ data?: PageData }>(endpoint, {
    query: locale ? { lang: locale } : undefined,
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
