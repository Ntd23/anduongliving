import type { ComputedRef, Ref } from "vue";
import type { PageData } from "~/composables/usePage";
import {
  buildAbsoluteUrl,
  normalizePath,
  normalizeSiteUrl,
} from "~~/shared/cms-routing";

type PageRef = Ref<PageData | undefined | null> | ComputedRef<PageData | undefined | null>;

const toAbsoluteAssetUrl = (siteUrl: string, value?: string | null) => {
  if (!value) {
    return undefined;
  }

  try {
    return new URL(value).toString();
  } catch {
    return buildAbsoluteUrl(siteUrl, value);
  }
};

export const usePageSeo = (page: PageRef) => {
  const config = useRuntimeConfig();
  const route = useRoute();
  const { locale, locales } = useI18n();

  const siteUrl = computed(() => String(config.public.siteUrl || ""));
  const currentPage = computed(() => page.value || undefined);
  const seo = computed(() => currentPage.value?.seo);
  const localeLanguageMap = computed<Record<string, string>>(() =>
    Object.fromEntries(
      locales.value.map((localeOption) => {
        if (typeof localeOption === "string") {
          return [localeOption, localeOption];
        }

        return [localeOption.code, localeOption.language || localeOption.code];
      }),
    ),
  );

  const currentPath = computed(() => {
    const localizedPath = seo.value?.localizations?.find(
      (item) => item.locale === locale.value,
    )?.path;

    return localizedPath || route.path || seo.value?.canonical_path || "/";
  });

  const title = computed(() => seo.value?.title || currentPage.value?.name || undefined);
  const description = computed(
    () => seo.value?.description || currentPage.value?.description || undefined,
  );
  const image = computed(() =>
    toAbsoluteAssetUrl(siteUrl.value, seo.value?.image || currentPage.value?.image),
  );
  const canonicalUrl = computed(() => buildAbsoluteUrl(siteUrl.value, currentPath.value));
  const robots = computed(() => seo.value?.robots || "index, follow");
  const ogType = computed(() =>
    seo.value?.canonical_path === "/" ? "website" : "article",
  );

  useSeoMeta({
    title,
    description,
    ogTitle: title,
    ogDescription: description,
    ogImage: image,
    ogUrl: canonicalUrl,
    ogType,
    twitterCard: computed(() => (image.value ? "summary_large_image" : "summary")),
    twitterTitle: title,
    twitterDescription: description,
    twitterImage: image,
  });

  useHead(() => {
    if (!currentPage.value) {
      return {};
    }

    const alternateLinks = (seo.value?.localizations || []).map((item) => ({
      rel: "alternate",
      hreflang: localeLanguageMap.value[item.locale] || item.locale,
      href: buildAbsoluteUrl(siteUrl.value, item.path),
    }));

    return {
      link: [
        {
          rel: "canonical",
          href: canonicalUrl.value,
        },
        ...alternateLinks,
      ],
      meta: [
        {
          name: "robots",
          content: robots.value,
        },
      ],
    };
  });
};
