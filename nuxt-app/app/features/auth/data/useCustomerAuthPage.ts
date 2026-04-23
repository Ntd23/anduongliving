import type { CustomerAuthMode, CustomerAuthPageData } from "~~/shared/customer-auth";
import { cmsProxyRoutes } from "~~/shared/routes/proxy";

export const useCustomerAuthPage = async (
  mode: CustomerAuthMode,
): Promise<CustomerAuthPageData> => {
  const requestFetch = useRequestFetch();
  const response = await requestFetch<{ data?: CustomerAuthPageData }>(cmsProxyRoutes.customer.authPage(), {
    query: { mode },
  });

  if (!response?.data) {
    throw createError({
      statusCode: 500,
      statusMessage: `Missing auth page data for ${mode}`,
    });
  }

  return response.data;
};



