import type { ComputedRef, Ref } from "vue";
import {
  buildAbsoluteUrl,
  normalizePath,
} from "~~/shared/cms-routing";

type MaybeRef<T> = Ref<T> | ComputedRef<T>;

type BlogSeoInput = {
  title: MaybeRef<string | undefined>;
  description?: MaybeRef<string | undefined>;
  image?: MaybeRef<string | undefined>;
  robots?: MaybeRef<string | undefined>;
  path?: MaybeRef<string | undefined>;
  type?: MaybeRef<"website" | "article" | undefined>;
};

const readValue = <T>(value: MaybeRef<T> | T | undefined): T | undefined =>
  unref(value as any) as T | undefined;

const toAbsoluteAssetUrl = (siteUrl: string, value?: string) => {
  if (!value) {
    return undefined;
  }

  try {
    return new URL(value).toString();
  } catch {
    return buildAbsoluteUrl(siteUrl, value);
  }
};

export const useBlogSeo = (input: BlogSeoInput) => {
  const config = useRuntimeConfig();
  const route = useRoute();

  const siteUrl = computed(() => String(config.public.siteUrl || ""));
  const currentPath = computed(() => normalizePath(readValue(input.path) || route.path || "/"));
  const canonicalUrl = computed(() => buildAbsoluteUrl(siteUrl.value, currentPath.value));
  const image = computed(() => toAbsoluteAssetUrl(siteUrl.value, readValue(input.image)));
  const robots = computed(() => readValue(input.robots) || "index, follow");
  const ogType = computed(() => readValue(input.type) || "website");
  const title = computed(() => readValue(input.title));
  const description = computed(() => readValue(input.description));

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

  useHead(() => ({
    link: [
      {
        rel: "canonical",
        href: canonicalUrl.value,
      },
    ],
    meta: [
      {
        name: "robots",
        content: robots.value,
      },
    ],
  }));
};
