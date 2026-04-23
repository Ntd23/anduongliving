import {
  CMS_MAIN_MENU_TOKEN,
  CMS_SIDEBAR_MENU_TOKEN,
} from "~~/shared/cms/constants";
import { resolveCmsLocale } from "~~/shared/cms/locale";
import { cmsProxyRoutes, resolveCmsProxyRequestUrl } from "~~/shared/routes/proxy";

export type MenuItemData = {
  id: number;
  title: string;
  url: string;
  target?: string | null;
  css_class?: string | null;
  icon_font?: string | null;
  reference_type?: string | null;
  reference_id?: number | null;
  is_external?: boolean;
  reference?: {
    slug?: string | null;
  } | null;
  children: MenuItemData[];
};

export type MenuData = {
  id: number;
  name: string;
  slug: string;
  location?: string | null;
  locale?: string | null;
  items: MenuItemData[];
};

export const useMenu = async (
  identifier: string | number,
  locale?: string,
  options?: { canonical?: boolean; location?: string | null },
): Promise<MenuData> => {
  const config = useRuntimeConfig();
  const endpoint =
    options?.location
      ? cmsProxyRoutes.menus.location(options.location)
      : options?.canonical || identifier === CMS_MAIN_MENU_TOKEN
      ? cmsProxyRoutes.menus.main()
      : identifier === CMS_SIDEBAR_MENU_TOKEN
      ? cmsProxyRoutes.menus.location(CMS_SIDEBAR_MENU_TOKEN)
      : cmsProxyRoutes.menus.detail(identifier);

  const response = await $fetch<{ data?: MenuData }>(endpoint, {
    baseURL: resolveCmsProxyRequestUrl("/", {
      cmsProxyBaseUrl: config.public.cmsProxyBaseUrl,
      client: import.meta.client,
    }),
    query: locale ? { lang: resolveCmsLocale(locale) } : undefined,
  });

  if (!response.data) {
    throw createError({
      statusCode: 404,
      statusMessage: "Menu not found",
    });
  }

  return response.data;
};



