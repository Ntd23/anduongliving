import { cmsAppRoutes } from "../routes/app";
import type { LocalePathResolver } from "../utils/url";
import {
  hasLocalePrefix,
  normalizePath,
  normalizeSiteUrl,
  splitUrlParts,
} from "../utils/url";

export const normalizeCmsInternalPath = (
  value: string,
  options?: {
    referenceType?: string | null;
    referenceSlug?: string | null;
  },
) => {
  const { pathname, search, hash } = splitUrlParts(value);
  const trimmedPath = pathname.replace(/\/+$/, "") || "/";
  const slug = trimmedPath.startsWith("/news/") ? trimmedPath.slice("/news/".length) : "";
  const referenceType = options?.referenceType || "";

  if (trimmedPath === "/news") {
    return `${cmsAppRoutes.blog.index()}${search}${hash}`;
  }

  if (slug) {
    if (referenceType.includes("Category")) {
      return `${cmsAppRoutes.blog.category(slug)}${search}${hash}`;
    }

    if (referenceType.includes("Tag")) {
      return `${cmsAppRoutes.blog.tag(slug)}${search}${hash}`;
    }

    if (referenceType.includes("Post")) {
      return `${cmsAppRoutes.blog.post(slug)}${search}${hash}`;
    }
  }

  if ((trimmedPath === "/" || trimmedPath === "") && referenceType.includes("Page") && options?.referenceSlug) {
    return `${cmsAppRoutes.page(options.referenceSlug)}${search}${hash}`;
  }

  return `${trimmedPath}${search}${hash}`;
};

export const localizeCmsPath = (
  path: string,
  options: {
    locale?: string;
    localeCodes: string[];
    localePath: LocalePathResolver;
  },
) => {
  if (hasLocalePrefix(path, options.localeCodes)) {
    return path;
  }

  return options.localePath(path, options.locale);
};

export const sanitizeCmsHtmlContent = (value?: string | null, siteUrl?: string | null) => {
  if (!value) {
    return "";
  }

  let output = value
    .replace(/<script\b[^>]*>[\s\S]*?<\/script>/gim, "")
    .replace(/<script\b[^>]*\/?>/gim, "")
    .replace(/<style\b[^>]*>[\s\S]*?<\/style>/gim, "")
    .replace(/<(?:style|link|meta|base)\b[^>]*>/gim, "")
    .replace(/\son[a-z]+\s*=\s*(".*?"|'.*?'|[^\s>]+)/gim, "")
    .replace(/\s(href|src|xlink:href)\s*=\s*(['"])\s*javascript:[^'"]*\2/gim, " $1=\"#\"")
    .replace(/\s(href|src|xlink:href)\s*=\s*(javascript:[^\s>]+)/gim, " $1=\"#\"");
  const normalizedSiteUrl = normalizeSiteUrl(siteUrl);

  if (normalizedSiteUrl) {
    const escapedSiteUrl = normalizedSiteUrl.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    output = output.replace(new RegExp(`${escapedSiteUrl}/news`, "g"), cmsAppRoutes.blog.index());
  }

  output = output.replace(/href=(['"])\/news\b/gi, `href=$1${cmsAppRoutes.blog.index()}`);

  return output;
};
