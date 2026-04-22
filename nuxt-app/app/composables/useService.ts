import {
  cmsProxyRoutes,
  resolveCmsLocale,
  resolveCmsProxyRequestUrl,
} from "~~/shared/cms-routing";

export type ServiceDetailData = {
  name: string;
  slug: string;
  content: string;
  seo?: {
    title?: string | null;
    description?: string | null;
    canonical_path?: string | null;
  } | null;
};

export const useService = async (
  slug: string,
  locale?: string,
): Promise<ServiceDetailData> => {
  const config = useRuntimeConfig();
  const response = await $fetch<{ data?: ServiceDetailData }>(cmsProxyRoutes.services.detail(slug), {
    baseURL: resolveCmsProxyRequestUrl("/", {
      cmsProxyBaseUrl: config.public.cmsProxyBaseUrl,
      client: import.meta.client,
    }),
    query: locale ? { lang: resolveCmsLocale(locale) } : undefined,
  });

  if (!response.data) {
    throw createError({
      statusCode: 404,
      statusMessage: "Service detail not found",
    });
  }

  return response.data;
};
