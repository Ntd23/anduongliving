import {
  buildAbsoluteAssetUrl,
} from "~~/shared/utils/url";
import { resolveCmsAssetBaseUrl } from "~~/shared/routes/backend";

export const useResolvedCmsAsset = () => {
  const config = useRuntimeConfig();

  const assetBaseUrl = computed(() =>
    resolveCmsAssetBaseUrl(String(config.public.apiBaseUrl || "")),
  );

  return (value?: string | null) => {
    const source = String(value || "").trim();

    if (!source) {
      return null;
    }

    return buildAbsoluteAssetUrl(assetBaseUrl.value, source) || source;
  };
};



