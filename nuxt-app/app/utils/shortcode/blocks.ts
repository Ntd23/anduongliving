import { detectShortcodeName } from "./registry";
import type { ShortcodeBlock } from "./types";

const SHORTCODE_OPEN_TAG = /<shortcode(\s[^>]*)?>([\s\S]*?)<\/shortcode>/gi;
const BRACKET_SHORTCODE_TAG = /\[([a-zA-Z0-9_-]+)(\s[^\]]*)?\]([\s\S]*?)\[\/\1\]/gi;
const SECTION_TAG = /<section(\s[^>]*)?>([\s\S]*?)<\/section>/gi;

export const parseShortcodeBlocks = (content: string): ShortcodeBlock[] => {
  if (!content) return [];

  const shortcodeBlocks = collectMatchedBlocks(content, SHORTCODE_OPEN_TAG, (match) => {
    const inner = (match[2] || "").trim();

    if (!inner) return null;

    const name = detectShortcodeName(inner);

    if (!name) {
      return {
        type: "html" as const,
        raw: inner,
        name: null,
      };
    }

    return {
      type: "shortcode" as const,
      raw: inner,
      name,
    };
  });

  if (shortcodeBlocks.some((block) => block.type === "shortcode")) {
    return shortcodeBlocks;
  }

  const bracketBlocks = collectMatchedBlocks(content, BRACKET_SHORTCODE_TAG, (match) => {
    const block = (match[0] || "").trim();
    const name = detectShortcodeName(block);

    if (!name) {
      return {
        type: "html" as const,
        raw: block,
        name: null,
      };
    }

    return {
      type: "shortcode" as const,
      raw: block,
      name,
    };
  });

  if (bracketBlocks.some((block) => block.type === "shortcode")) {
    return bracketBlocks;
  }

  const sectionBlocks = collectMatchedBlocks(content, SECTION_TAG, (match) => {
    const section = (match[0] || "").trim();

    if (!section) return null;

    const name = detectShortcodeName(section);

    if (!name) {
      return {
        type: "html" as const,
        raw: section,
        name: null,
      };
    }

    return {
      type: "shortcode" as const,
      raw: section,
      name,
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
