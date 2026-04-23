import { CMS_LOCALE_MAP } from "../cms/constants";

export const resolveCmsLocale = (locale?: string) => {
  const normalizedLocale = String(locale || "").trim();

  if (!normalizedLocale) {
    return CMS_LOCALE_MAP.vi;
  }

  if (
    Object.values(CMS_LOCALE_MAP).includes(
      normalizedLocale as (typeof CMS_LOCALE_MAP)[keyof typeof CMS_LOCALE_MAP],
    )
  ) {
    return normalizedLocale;
  }

  return CMS_LOCALE_MAP[normalizedLocale as keyof typeof CMS_LOCALE_MAP] || normalizedLocale;
};
