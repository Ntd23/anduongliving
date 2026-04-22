import type {
  ShortcodeImage,
  TeamSocialPlatform,
} from "./types";

export const detectSocialPlatform = (html: string): TeamSocialPlatform | null => {
  const lowerHtml = html.toLowerCase();

  if (lowerHtml.includes("facebook")) return "facebook";
  if (lowerHtml.includes("twitter")) return "twitter";
  if (lowerHtml.includes("instagram")) return "instagram";
  if (lowerHtml.includes("http")) return "website";

  return null;
};

export const extractBlocksByTag = (
  html: string,
  tagName: string,
  classNeedle?: string,
): string[] => {
  const pattern = new RegExp(`<${tagName}\\b[^>]*>`, "gi");
  const blocks: string[] = [];
  let match: RegExpExecArray | null;

  while ((match = pattern.exec(html))) {
    const openingTag = match[0];

    if (classNeedle && !openingTag.toLowerCase().includes(classNeedle.toLowerCase())) {
      continue;
    }

    const block = extractBalancedTag(html, match.index, tagName);

    if (!block) {
      continue;
    }

    blocks.push(block);
    pattern.lastIndex = match.index + block.length;
  }

  return blocks;
};

export const extractBalancedTag = (html: string, startIndex: number, tagName: string): string | null => {
  const openPattern = new RegExp(`<${tagName}\\b[^>]*>`, "gi");
  const closePattern = new RegExp(`</${tagName}>`, "gi");
  let cursor = startIndex;
  let depth = 0;

  while (cursor < html.length) {
    openPattern.lastIndex = cursor;
    closePattern.lastIndex = cursor;

    const nextOpen = openPattern.exec(html);
    const nextClose = closePattern.exec(html);
    const openIndex = nextOpen?.index ?? Number.POSITIVE_INFINITY;
    const closeIndex = nextClose?.index ?? Number.POSITIVE_INFINITY;

    if (openIndex < closeIndex) {
      depth += 1;
      cursor = openPattern.lastIndex;
      continue;
    }

    if (!Number.isFinite(closeIndex)) {
      return null;
    }

    depth -= 1;
    cursor = closePattern.lastIndex;

    if (depth === 0) {
      return html.slice(startIndex, cursor);
    }
  }

  return null;
};

export const extractFirstBlockByClass = (html: string, tagName: string, classNeedle: string): string | null =>
  extractBlocksByTag(html, tagName, classNeedle)[0] || null;

export const extractFirstNthBlockByClass = (
  html: string,
  tagName: string,
  classNeedle: string,
  index: number,
): string | null => extractBlocksByTag(html, tagName, classNeedle)[index] || null;

export const extractFirstTag = (
  html: string,
  tagName: string,
  classNeedle?: string,
  extraFilter?: RegExp,
): string | null => {
  const pattern = new RegExp(`<${tagName}\\b[^>]*>`, "gi");
  let match: RegExpExecArray | null;

  while ((match = pattern.exec(html))) {
    const openingTag = match[0];

    if (classNeedle && !openingTag.toLowerCase().includes(classNeedle.toLowerCase())) {
      continue;
    }

    if (extraFilter && !extraFilter.test(openingTag)) {
      continue;
    }

    const balanced = extractBalancedTag(html, match.index, tagName);

    if (balanced) {
      return balanced;
    }

    return openingTag;
  }

  return null;
};

export const extractLinks = (html: string): Array<{ raw: string; url: string }> => {
  const pattern = /<a\b[^>]*href=(["'])(.*?)\1[^>]*>([\s\S]*?)<\/a>/gi;
  const links: Array<{ raw: string; url: string }> = [];
  let match: RegExpExecArray | null;

  while ((match = pattern.exec(html))) {
    links.push({
      raw: match[0],
      url: decodeHtmlEntities(match[2] || ""),
    });
  }

  return links;
};

export const extractListItems = (html: string): string[] => {
  const pattern = /<li\b[^>]*>([\s\S]*?)<\/li>/gi;
  const items: string[] = [];
  let match: RegExpExecArray | null;

  while ((match = pattern.exec(html))) {
    const value = normalizeText(match[1] || "");

    if (value) {
      items.push(value);
    }
  }

  return items;
};

export const extractFirstImage = (html: string): ShortcodeImage | null => {
  const match = /<img\b[^>]*src=(["'])(.*?)\1[^>]*>/i.exec(html);

  if (!match) {
    return null;
  }

  return {
    src: decodeHtmlEntities(match[2] || ""),
    alt: decodeHtmlEntities(extractAttribute(match[0], "alt") || ""),
  };
};

export const extractAllImages = (html: string): ShortcodeImage[] => {
  const pattern = /<img\b[^>]*src=(["'])(.*?)\1[^>]*>/gi;
  const images: ShortcodeImage[] = [];
  let match: RegExpExecArray | null;

  while ((match = pattern.exec(html))) {
    images.push({
      src: decodeHtmlEntities(match[2] || ""),
      alt: decodeHtmlEntities(extractAttribute(match[0], "alt") || ""),
    });
  }

  return images;
};

export const extractFirstDirectImage = (html: string): ShortcodeImage | null => {
  const cleanedHtml = html.replace(/<div\b[^>]*class=(["'])[^"']*about-icon[^"']*\1[^>]*>[\s\S]*?<\/div>/i, "");

  return extractFirstImage(cleanedHtml);
};

export const extractTextFromTag = (html: string, tagName: string, classNeedle?: string): string | null => {
  const classPattern = classNeedle
    ? `[^>]*class=(["'])[^"']*${escapeRegExp(classNeedle)}[^"']*\\1[^>]*`
    : "[^>]*";
  const pattern = new RegExp(
    `<${tagName}\\b${classPattern}>([\\s\\S]*?)</${tagName}>`,
    "i",
  );
  const match = pattern.exec(html);

  if (!match) {
    return null;
  }

  return normalizeText(match[1]);
};

export const extractInnerHtmlFromTag = (html: string, tagName: string, classNeedle?: string): string | null => {
  const classPattern = classNeedle
    ? `[^>]*class=(["'])[^"']*${escapeRegExp(classNeedle)}[^"']*\\1[^>]*`
    : "[^>]*";
  const pattern = new RegExp(
    `<${tagName}\\b${classPattern}>([\\s\\S]*?)</${tagName}>`,
    "i",
  );
  const match = pattern.exec(html);

  if (!match) {
    return null;
  }

  return decodeHtmlEntities((match[1] || "").trim());
};

export const extractAttribute = (html: string, attribute: string): string | null => {
  const pattern = new RegExp(`${attribute}\\s*=\\s*(['"])(.*?)\\1`, "i");
  const match = pattern.exec(html);

  if (!match) {
    return null;
  }

  return decodeHtmlEntities(match[2] || "");
};

export const extractStyleValue = (html: string, property: string): string | null => {
  const style = extractAttribute(html, "style");

  if (!style) {
    return null;
  }

  const pattern = new RegExp(`${escapeRegExp(property)}\\s*:\\s*([^;]+)`, "i");
  const match = pattern.exec(style);

  return match ? match[1].trim() : null;
};

export const extractStyleImage = (html: string): ShortcodeImage | null => {
  const backgroundImage = extractStyleValue(html, "background-image");

  if (!backgroundImage) {
    return null;
  }

  const match = /url\((["']?)(.*?)\1\)/i.exec(backgroundImage);

  if (!match?.[2]) {
    return null;
  }

  return {
    src: decodeHtmlEntities(match[2]),
    alt: "",
  };
};

export const extractFirstParagraphText = (html: string): string | null => {
  return extractParagraphTexts(html)[0] || null;
};

export const extractParagraphTexts = (html: string): string[] => {
  const pattern = /<p\b[^>]*>([\s\S]*?)<\/p>/gi;
  const paragraphs: string[] = [];
  let match: RegExpExecArray | null;

  while ((match = pattern.exec(html))) {
    const value = decodeHtmlEntities(
      stripTags(match[1] || "")
        .replace(/\r\n/g, "\n")
        .replace(/\r/g, "\n")
        .replace(/\n{2,}/g, "\n\n")
        .replace(/[ \t]+/g, " ")
        .trim(),
    );

    if (value) {
      paragraphs.push(value);
    }
  }

  return paragraphs;
};

export const normalizeText = (value: string): string =>
  decodeHtmlEntities(stripTags(value).replace(/\s+/g, " ").trim());

export const stripTags = (value: string): string => value.replace(/<[^>]+>/g, " ");

export const decodeHtmlEntities = (value: string): string =>
  value
    .replace(/&nbsp;/gi, " ")
    .replace(/&amp;/gi, "&")
    .replace(/&quot;/gi, '"')
    .replace(/&#039;|&#39;/gi, "'")
    .replace(/&lt;/gi, "<")
    .replace(/&gt;/gi, ">")
    .replace(/&#(\d+);/g, (_, code) => String.fromCharCode(Number(code)))
    .replace(/&#x([0-9a-f]+);/gi, (_, code) => String.fromCharCode(Number.parseInt(code, 16)));

export const parseShortcodeAttributes = (shortcode: string): Record<string, string> => {
  const attributes: Record<string, string> = {};
  const attrPattern = /(\w+)="([^"]*)"/g;
  let match: RegExpExecArray | null;

  while ((match = attrPattern.exec(shortcode))) {
    attributes[match[1]] = match[2];
  }

  return attributes;
};

export const escapeRegExp = (value: string): string => value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
