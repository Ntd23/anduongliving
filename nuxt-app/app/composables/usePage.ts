export type PageData = {
  id: number;
  name: string;
  slug: string;
  description?: string | null;
  content?: string | null;
  image?: string | null;
  template?: string | null;
  status?: {
    value?: string;
    label?: string;
  };
  created_at?: string;
  updated_at?: string;
};

export type ShortcodeBlock = {
  type: "shortcode" | "html" | "text";
  raw: string;
  name?: string | null;
};

export const usePage = async (slug: string, locale?: string) => {
  const response = await $fetch<{ data?: PageData }>(`/api/cms/pages/${slug}`, {
    query: locale ? { lang: locale } : undefined,
  });

  const page = response.data;

  if (!page) {
    throw createError({
      statusCode: 404,
      statusMessage: "Page not found",
    });
  }

  return {
    page,
    blocks: parseShortcodeBlocks(page.content || ""),
  };
};

const SHORTCODE_OPEN_TAG = /<shortcode(\s[^>]*)?>([\s\S]*?)<\/shortcode>/gi;
const SECTION_TAG = /<section(\s[^>]*)?>([\s\S]*?)<\/section>/gi;

export const parseShortcodeBlocks = (content: string): ShortcodeBlock[] => {
  if (!content) return [];

  const shortcodeBlocks = collectMatchedBlocks(content, SHORTCODE_OPEN_TAG, (match) => {
    const inner = (match[2] || "").trim();

    if (!inner) return null;

    return {
      type: "shortcode" as const,
      raw: inner,
      name: detectShortcodeName(inner),
    };
  });

  if (shortcodeBlocks.some((block) => block.type === "shortcode")) {
    return shortcodeBlocks;
  }

  const sectionBlocks = collectMatchedBlocks(content, SECTION_TAG, (match) => {
    const section = (match[0] || "").trim();

    if (!section) return null;

    return {
      type: "shortcode" as const,
      raw: section,
      name: detectShortcodeName(section),
    };
  });

  if (sectionBlocks.length > 0) {
    return sectionBlocks;
  }

  const fallback = content.trim();

  return fallback ? [{ type: "html", raw: fallback, name: null }] : [];
};

const collectMatchedBlocks = (
  content: string,
  pattern: RegExp,
  resolver: (match: RegExpExecArray) => ShortcodeBlock | null,
): ShortcodeBlock[] => {
  const blocks: ShortcodeBlock[] = [];
  let lastIndex = 0;
  let match: RegExpExecArray | null;

  pattern.lastIndex = 0;

  while ((match = pattern.exec(content))) {
    const startIndex = match.index;
    const rawPrefix = content.slice(lastIndex, startIndex).trim();

    if (rawPrefix) {
      blocks.push({ type: "html", raw: rawPrefix, name: null });
    }

    const block = resolver(match);

    if (block) {
      blocks.push(block);
    }

    lastIndex = startIndex + match[0].length;
  }

  const suffix = content.slice(lastIndex).trim();

  if (suffix) {
    blocks.push({ type: "html", raw: suffix, name: null });
  }

  return blocks;
};

const detectShortcodeName = (html: string): string | null => {
  const lowerHtml = html.toLowerCase();
  const mappings = [
    ["about-area", "about"],
    ["skill-area", "skill"],
    ["feature-area2", "feature"],
    ["newslater-area", "newsletter"],
    ["team-area", "team"],
    ["team-area2", "team"],
  ] as const;

  for (const [needle, name] of mappings) {
    if (lowerHtml.includes(needle)) return name;
  }

  return null;
};
