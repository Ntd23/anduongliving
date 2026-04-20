import {
  cmsProxyRoutes,
  resolveCmsLocale,
  resolveCmsProxyRequestUrl,
} from "~~/shared/cms-routing";
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";

export type SidebarWidgetsData = {
  sidebar_id: string;
  lang?: string | null;
  locale?: string | null;
  items: SidebarWidgetManifest[];
};

export const useSidebarWidgets = async (
  sidebarId: string,
  locale?: string,
): Promise<SidebarWidgetsData> => {
  const config = useRuntimeConfig();
  const response = await $fetch<{ data?: SidebarWidgetsData | SidebarWidgetManifest[] }>(
    cmsProxyRoutes.widgets.sidebar(sidebarId),
    {
      baseURL: resolveCmsProxyRequestUrl("/", {
        cmsProxyBaseUrl: config.public.cmsProxyBaseUrl,
        client: import.meta.client,
      }),
      query: locale ? { lang: resolveCmsLocale(locale) } : undefined,
    },
  );

  if (!response.data) {
    throw createError({
      statusCode: 404,
      statusMessage: "Sidebar widgets not found",
    });
  }

  if (Array.isArray(response.data)) {
    return {
      sidebar_id: sidebarId,
      items: response.data,
    };
  }

  return {
    ...response.data,
    items: response.data.items || [],
  };
};
