import { resolveCmsLocale } from "~~/shared/cms/locale";
import { cmsProxyRoutes } from "~~/shared/routes/proxy";

export type ThemeCurrencyLink = {
  title: string;
  href: string;
  active: boolean;
};

export type ThemeCustomerBlock = {
  authenticated: boolean;
  name?: string | null;
  avatarUrl?: string | null;
  overviewUrl?: string | null;
  loginUrl?: string | null;
  registerUrl?: string | null;
  logoutUrl?: string | null;
};

export type ThemeHeaderExtrasData = {
  currencies: ThemeCurrencyLink[];
  customer: ThemeCustomerBlock;
};

const toHeaderExtras = (value?: Partial<ThemeHeaderExtrasData> | null): ThemeHeaderExtrasData => ({
  currencies: Array.isArray(value?.currencies) ? value.currencies : [],
  customer: {
    authenticated: Boolean(value?.customer?.authenticated),
    name: value?.customer?.name || null,
    avatarUrl: value?.customer?.avatarUrl || null,
    overviewUrl: value?.customer?.overviewUrl || null,
    loginUrl: value?.customer?.loginUrl || null,
    registerUrl: value?.customer?.registerUrl || null,
    logoutUrl: value?.customer?.logoutUrl || null,
  },
});

export const useHeaderExtras = async (locale?: string): Promise<ThemeHeaderExtrasData> => {
  const requestFetch = useRequestFetch();
  const response = await requestFetch<{ data?: ThemeHeaderExtrasData }>(cmsProxyRoutes.theme.headerExtras(), {
    query: locale ? { lang: resolveCmsLocale(locale) } : undefined,
  });

  return toHeaderExtras(response.data);
};



