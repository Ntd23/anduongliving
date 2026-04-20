import {
  detectSocialPlatform,
  extractAllImages,
  extractAttribute,
  extractBlocksByTag,
  extractFirstBlockByClass,
  extractFirstDirectImage,
  extractFirstImage,
  extractFirstNthBlockByClass,
  extractFirstParagraphText,
  extractFirstTag,
  extractLinks,
  extractListItems,
  extractParagraphTexts,
  extractStyleImage,
  extractStyleValue,
  extractTextFromTag,
  normalizeText,
  parseShortcodeAttributes,
} from "./core";
import type {
  AboutSectionData,
  FeatureSectionData,
  HeroStorySectionData,
  NewsletterSectionData,
  PricingItem,
  PricingSectionData,
  SkillItem,
  SkillSectionData,
  TeamMember,
  TeamSectionData,
  TeamSocialLink,
} from "./types";

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

export const parseAboutBlock = (html: string): AboutSectionData => {
  const section = extractFirstBlockByClass(html, "section", "about-area") || html;
  const mediaBlock = extractFirstBlockByClass(section, "div", "s-about-img");
  const titleBlock = extractFirstBlockByClass(section, "div", "about-title");
  const contentBlock = extractFirstBlockByClass(section, "div", "about-content s-about-content");
  const featureList = extractFirstTag(section, "ul", "green");
  const signatureBlock = extractFirstBlockByClass(section, "div", "signature");
  const actionLink = extractLinks(extractFirstBlockByClass(section, "div", "slider-btn") || "")[0];
  const backgroundImageBlock = extractFirstBlockByClass(section, "div", "animations-02");
  const accentImageBlock = extractFirstBlockByClass(section, "div", "about-icon");
  const description = contentBlock ? extractFirstParagraphText(contentBlock) : null;

  return {
    backgroundImage: backgroundImageBlock ? extractFirstImage(backgroundImageBlock) : null,
    mainImage: mediaBlock ? extractFirstDirectImage(mediaBlock) : null,
    accentImage: accentImageBlock ? extractFirstImage(accentImageBlock) : null,
    subtitle: titleBlock ? extractTextFromTag(titleBlock, "h5") : null,
    title: titleBlock ? extractTextFromTag(titleBlock, "h2") : null,
    description,
    bullets: featureList ? extractListItems(featureList) : [],
    action: actionLink?.url && normalizeText(actionLink.raw)
      ? {
          href: actionLink.url,
          label: normalizeText(actionLink.raw),
        }
      : null,
    signatureImage: signatureBlock ? extractFirstImage(signatureBlock) : null,
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

export const parseHeroStoryBlock = (html: string): HeroStorySectionData => {
  const section =
    extractFirstBlockByClass(html, "section", "hero-story") ||
    extractFirstBlockByClass(html, "section", "story-area") ||
    extractFirstBlockByClass(html, "section", "our-story-area") ||
    html;
  const titleBlock =
    extractFirstBlockByClass(section, "div", "section-title") ||
    extractFirstBlockByClass(section, "div", "story-title") ||
    extractFirstBlockByClass(section, "div", "hero-content") ||
    section;
  const actionLink = extractLinks(extractFirstBlockByClass(section, "div", "slider-btn") || section)[0];
  const standaloneImages = extractAllImages(section);
  const styleImage = extractStyleImage(section);

  return {
    backgroundImage: styleImage || standaloneImages[0] || null,
    foregroundImage: styleImage ? standaloneImages[0] || null : standaloneImages[1] || null,
    subtitle:
      extractTextFromTag(titleBlock, "h5") ||
      extractTextFromTag(titleBlock, "p", "story-eyebrow"),
    title:
      extractTextFromTag(titleBlock, "h1") ||
      extractTextFromTag(titleBlock, "h2") ||
      extractTextFromTag(titleBlock, "h3"),
    description: extractParagraphTexts(titleBlock).join("\n\n") || null,
    action: actionLink?.url && normalizeText(actionLink.raw)
      ? {
          href: actionLink.url,
          label: normalizeText(actionLink.raw),
        }
      : null,
  };
};

export const parseFeatureBlock = (html: string): FeatureSectionData => {
  const section = extractFirstBlockByClass(html, "section", "feature-area2") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "feature-title");
  const contentBlock = extractFirstBlockByClass(section, "div", "feature-content");
  const imageBlock = extractFirstBlockByClass(section, "div", "feature-img");
  const backgroundImageBlock = extractFirstBlockByClass(section, "div", "animations-02");
  const actionLink = extractLinks(extractFirstBlockByClass(section, "div", "slider-btn") || "")[0];

  const allImages = extractAllImages(section);
  const mainImage = imageBlock ? extractFirstImage(imageBlock) : allImages[0] || null;
  const secondaryImage = allImages.length > 1 ? allImages[1] || null : null;

  return {
    backgroundColor: extractStyleValue(section, "background"),
    backgroundImage: backgroundImageBlock ? extractFirstImage(backgroundImageBlock) : null,
    image: mainImage,
    secondaryImage,
    quote: null,
    quoteAuthor: null,
    subtitle: titleBlock ? extractTextFromTag(titleBlock, "h5") : null,
    title: titleBlock ? extractTextFromTag(titleBlock, "h2") : null,
    description: contentBlock ? extractFirstParagraphText(contentBlock) : null,
    action: actionLink?.url && normalizeText(actionLink.raw)
      ? {
          href: actionLink.url,
          label: normalizeText(actionLink.raw),
        }
      : null,
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

export const parsePricingBlock = (html: string): PricingSectionData => {

  const attributes = parseShortcodeAttributes(html);
  const section = extractFirstBlockByClass(html, "section", "pricing-area") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");
  const pricingBlocks = extractBlocksByTag(section, "div", "single-pricing");

  const parsePriceText = (priceText: string): { price: string; currency: string } => {
    const priceMatch = priceText.match(/^([^\d]*)([\d.,]+)(.*)$/);
    if (!priceMatch) {
      return { price: priceText, currency: "" };
    }

    const prefix = priceMatch[1]?.trim() || "";
    const digits = priceMatch[2]?.trim() || priceText;
    const suffix = priceMatch[3]?.trim() || "";

    return {
      price: digits,
      currency: prefix || suffix || "",
    };
  };

  const getFeatures = (rawValue: string | null): string[] =>
    (rawValue || "")
      .split(/\s*,\s*/)
      .map((feature) => feature.trim().replace(/\.*$/, ""))
      .filter(Boolean);

  const buildPricingItems = (): PricingItem[] => {
    const quantity = Math.max(0, Number.parseInt(attributes.quantity || "", 10));
    const itemCount = quantity || 6;

    return Array.from({ length: itemCount }, (_, index) => index + 1)
      .map((itemIndex) => {
        const title = attributes[`title_${itemIndex}`] || null;
        const priceText = attributes[`price_${itemIndex}`] || null;
        const duration = attributes[`duration_${itemIndex}`] || null;
        const features = getFeatures(attributes[`feature_list_${itemIndex}`] || null);
        const buttonLabel = attributes[`button_label_${itemIndex}`] || "Bắt đầu ngay";
        const buttonUrl = attributes[`button_url_${itemIndex}`] || attributes[`button_url_${itemIndex}`] || "#";

        if (!title || !priceText) {
          return null;
        }

        const parsedPrice = parsePriceText(priceText);

        return {
          title,
          price: parsedPrice.price,
          currency: parsedPrice.currency,
          period: duration || "",
          features,
          buttonLabel,
          buttonUrl,
          isPopular: false,
        } satisfies PricingItem;
      })
      .filter(Boolean) as PricingItem[];
  };

  return {
    backgroundColor: attributes.background_color || null,
    backgroundImage1: attributes.background_image_1
      ? { src: attributes.background_image_1, alt: "" }
      : null,
    backgroundImage2: attributes.background_image_2
      ? { src: attributes.background_image_2, alt: "" }
      : null,
    subtitle: attributes.subtitle ? attributes.subtitle : titleBlock ? extractTextFromTag(titleBlock, "h5") : null,
    title: attributes.title ? attributes.title : titleBlock ? extractTextFromTag(titleBlock, "h2") : null,
    description:
      attributes.description || (titleBlock ? extractTextFromTag(titleBlock, "p") : null),
    items: buildPricingItems().length ? buildPricingItems() : pricingBlocks
      .map((pricingBlock) => {
        const title = extractTextFromTag(pricingBlock, "h4");
        const priceText = extractTextFromTag(pricingBlock, "h2");
        const features = extractListItems(pricingBlock);
        const buttonLink = extractLinks(pricingBlock)[0];

        if (!title || !priceText) return null;

        const parsedPrice = parsePriceText(priceText);

        return {
          title,
          description: null,
          price: parsedPrice.price,
          currency: parsedPrice.currency,
          period: extractTextFromTag(pricingBlock, "span") || "",
          features,
          buttonLabel: buttonLink ? normalizeText(buttonLink.raw) : "Get Started",
          buttonUrl: buttonLink?.url || "#",
          isPopular: pricingBlock.includes("popular") || pricingBlock.includes("featured"),
        } satisfies PricingItem;
      })
      .filter(Boolean) as PricingItem[],
  };
};