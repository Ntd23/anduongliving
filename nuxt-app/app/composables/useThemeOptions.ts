import {
  cmsProxyRoutes,
  resolveCmsLocale,
  resolveCmsProxyRequestUrl,
} from "~~/shared/cms-routing";

export type ThemeSocialLink = {
  name?: string | null;
  url: string;
  icon?: string | null;
  image?: string | null;
  image_url?: string | null;
  color?: string | null;
  background_color?: string | null;
};

export type ThemeOptionsData = {
  locale?: string | null;
  site_title?: string | null;
  logo?: string | null;
  logo_url?: string | null;
  breadcrumb_background_image?: string | null;
  breadcrumb_background_image_url?: string | null;
  background_footer?: string | null;
  background_footer_url?: string | null;
  header_top_enabled?: boolean | string | null;
  header_sticky_enabled?: boolean | string | null;
  header_button_label?: string | null;
  header_button_url?: string | null;
  opening_hours?: string | null;
  hotline?: string | null;
  email?: string | null;
  social_links: ThemeSocialLink[];
};

export const useThemeOptions = async (locale?: string): Promise<ThemeOptionsData> => {
  const config = useRuntimeConfig();
  const response = await $fetch<{ data?: ThemeOptionsData }>(cmsProxyRoutes.theme.options(), {
    baseURL: resolveCmsProxyRequestUrl("/", {
      cmsProxyBaseUrl: config.public.cmsProxyBaseUrl,
      client: import.meta.client,
    }),
    query: locale ? { lang: resolveCmsLocale(locale) } : undefined,
  });

  return {
    social_links: response.data?.social_links || [],
    ...response.data,
  };
};
