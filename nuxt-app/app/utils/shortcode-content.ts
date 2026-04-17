export type ShortcodeImage = {
  src: string;
  alt: string;
};

export type TeamSocialPlatform = "facebook" | "twitter" | "instagram" | "website";

export type TeamSocialLink = {
  platform: TeamSocialPlatform;
  url: string;
};

export type TeamMember = {
  name: string;
  title: string | null;
  profileUrl: string | null;
  image: ShortcodeImage | null;
  socials: TeamSocialLink[];
};

export type TeamSectionData = {
  members: TeamMember[];
};

export type SkillItem = {
  title: string;
  percentage: number;
};

export type SkillSectionData = {
  backgroundColor: string | null;
  backgroundImage: ShortcodeImage | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  image: ShortcodeImage | null;
  items: SkillItem[];
};

export type NewsletterSectionData = {
  backgroundColor: string | null;
  floatingImage: ShortcodeImage | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  action: string | null;
  placeholder: string;
  buttonLabel: string;
};

export const parseTeamBlock = (html: string): TeamSectionData => {
  const section = extractFirstBlockByClass(html, "section", "team-area") || html;
  const memberBlocks = extractBlocksByTag(section, "div", "single-team");

  return {
    members: memberBlocks
      .map((memberBlock) => {
        const socialBlock = extractFirstBlockByClass(memberBlock, "div", "team-social");
        const socialLinks = socialBlock ? extractLinks(socialBlock) : [];
        const profileLink = extractLinks(memberBlock)[0];
        const name = extractTextFromTag(memberBlock, "h4");

        if (!name) return null;

        return {
          name,
          title: extractTextFromTag(memberBlock, "p"),
          profileUrl: profileLink?.url || null,
          image: extractFirstImage(memberBlock),
          socials: socialLinks
            .map((socialLink) => {
              const platform = detectSocialPlatform(socialLink.raw);

              if (!platform || !socialLink.url) return null;

              return {
                platform,
                url: socialLink.url,
              };
            })
            .filter(Boolean) as TeamSocialLink[],
        } satisfies TeamMember;
      })
      .filter(Boolean) as TeamMember[],
  };
};

export const parseSkillBlock = (html: string): SkillSectionData => {
  const section = extractFirstBlockByClass(html, "section", "skill-area") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "skills-title");
  const leftColumn = extractFirstNthBlockByClass(section, "div", "skills-content", 0) || section;
  const skillBlocks = extractBlocksByTag(section, "div", "skill mb-30");
  const rightImageBlock = extractFirstBlockByClass(section, "div", "skills-img");
  const backgroundImageBlock = extractFirstBlockByClass(section, "div", "animations-01");

  return {
    backgroundColor: extractStyleValue(section, "background"),
    backgroundImage: backgroundImageBlock ? extractFirstImage(backgroundImageBlock) : null,
    subtitle: titleBlock ? extractTextFromTag(titleBlock, "h5") : null,
    title: titleBlock ? extractTextFromTag(titleBlock, "h2") : null,
    description: extractTextFromTag(leftColumn, "p"),
    image: rightImageBlock ? extractFirstImage(rightImageBlock) : null,
    items: skillBlocks
      .map((skillBlock) => {
        const percentageRaw = extractAttribute(
          extractFirstTag(skillBlock, "div", "skill-per") || "",
          "id",
        );
        const percentage = Number.parseInt((percentageRaw || "").replace(/[^\d]/g, ""), 10);
        const title = extractTextFromTag(skillBlock, "div", "skill-name");

        if (!title || Number.isNaN(percentage)) return null;

        return {
          title,
          percentage: Math.min(Math.max(percentage, 0), 100),
        } satisfies SkillItem;
      })
      .filter(Boolean) as SkillItem[],
  };
};

export const parseNewsletterBlock = (html: string): NewsletterSectionData => {
  const section = extractFirstBlockByClass(html, "section", "newslater-area") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");
  const formTag = extractFirstTag(section, "form");
  const inputTag = extractFirstTag(formTag || "", "input", undefined, /type=["']email["']/i);
  const buttonText = extractTextFromTag(formTag || "", "button");
  const floatingImageBlock = extractFirstBlockByClass(section, "div", "animations-01");

  return {
    backgroundColor: extractStyleValue(section, "background-color"),
    floatingImage: floatingImageBlock ? extractFirstImage(floatingImageBlock) : null,
    subtitle: titleBlock ? extractTextFromTag(titleBlock, "h5") : null,
    title: titleBlock ? extractTextFromTag(titleBlock, "h2") : null,
    description: titleBlock ? extractTextFromTag(titleBlock, "p") : null,
    action: formTag ? extractAttribute(formTag, "action") : null,
    placeholder: extractAttribute(inputTag || "", "placeholder") || "Your Email Address",
    buttonLabel: buttonText || "Subscribe Now",
  };
};

const detectSocialPlatform = (html: string): TeamSocialPlatform | null => {
  const lowerHtml = html.toLowerCase();

  if (lowerHtml.includes("facebook")) return "facebook";
  if (lowerHtml.includes("twitter")) return "twitter";
  if (lowerHtml.includes("instagram")) return "instagram";
  if (lowerHtml.includes("http")) return "website";

  return null;
};

const extractBlocksByTag = (
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

const extractBalancedTag = (html: string, startIndex: number, tagName: string): string | null => {
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

const extractFirstBlockByClass = (html: string, tagName: string, classNeedle: string): string | null =>
  extractBlocksByTag(html, tagName, classNeedle)[0] || null;

const extractFirstNthBlockByClass = (
  html: string,
  tagName: string,
  classNeedle: string,
  index: number,
): string | null => extractBlocksByTag(html, tagName, classNeedle)[index] || null;

const extractFirstTag = (
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

const extractLinks = (html: string): Array<{ raw: string; url: string }> => {
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

const extractFirstImage = (html: string): ShortcodeImage | null => {
  const match = /<img\b[^>]*src=(["'])(.*?)\1[^>]*>/i.exec(html);

  if (!match) {
    return null;
  }

  return {
    src: decodeHtmlEntities(match[2] || ""),
    alt: decodeHtmlEntities(extractAttribute(match[0], "alt") || ""),
  };
};

const extractTextFromTag = (html: string, tagName: string, classNeedle?: string): string | null => {
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

const extractAttribute = (html: string, attribute: string): string | null => {
  const pattern = new RegExp(`${attribute}\\s*=\\s*(["'])(.*?)\\1`, "i");
  const match = pattern.exec(html);

  if (!match) {
    return null;
  }

  return decodeHtmlEntities(match[2] || "");
};

const extractStyleValue = (html: string, property: string): string | null => {
  const style = extractAttribute(html, "style");

  if (!style) {
    return null;
  }

  const pattern = new RegExp(`${escapeRegExp(property)}\\s*:\\s*([^;]+)`, "i");
  const match = pattern.exec(style);

  return match ? match[1].trim() : null;
};

const normalizeText = (value: string): string =>
  decodeHtmlEntities(stripTags(value).replace(/\s+/g, " ").trim());

const stripTags = (value: string): string => value.replace(/<[^>]+>/g, " ");

const decodeHtmlEntities = (value: string): string =>
  value
    .replace(/&nbsp;/gi, " ")
    .replace(/&amp;/gi, "&")
    .replace(/&quot;/gi, '"')
    .replace(/&#039;|&#39;/gi, "'")
    .replace(/&lt;/gi, "<")
    .replace(/&gt;/gi, ">")
    .replace(/&#(\d+);/g, (_, code) => String.fromCharCode(Number(code)))
    .replace(/&#x([0-9a-f]+);/gi, (_, code) => String.fromCharCode(Number.parseInt(code, 16)));

const escapeRegExp = (value: string): string => value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
