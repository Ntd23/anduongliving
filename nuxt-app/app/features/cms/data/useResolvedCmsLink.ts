import { normalizeCmsInternalPath } from "~~/shared/cms/internal-path";
import { extractInternalPath } from "~~/shared/utils/url";

export type ResolvedCmsLink = {
  href: string;
  isInternal: boolean;
};

export const useResolvedCmsLink = () => {
  const localePath = useLocalePath();
  const config = useRuntimeConfig();

  return (value?: string | null): ResolvedCmsLink | null => {
    const href = String(value || "").trim();

    if (!href) {
      return null;
    }

    const internalPath = extractInternalPath(href, {
      siteUrl: String(config.public.siteUrl || ""),
    });

    if (!internalPath) {
      return {
        href,
        isInternal: false,
      };
    }

    return {
      href: localePath(normalizeCmsInternalPath(internalPath)),
      isInternal: true,
    };
  };
};



