import { computed, toValue } from "vue";
import type { MaybeRefOrGetter } from "vue";
import { sanitizeCmsHtmlContent } from "~~/shared/cms-routing";

export const useSanitizedCmsHtml = (value: MaybeRefOrGetter<string | null | undefined>) => {
  const config = useRuntimeConfig();

  return computed(() =>
    sanitizeCmsHtmlContent(toValue(value), String(config.public.siteUrl || "")),
  );
};
