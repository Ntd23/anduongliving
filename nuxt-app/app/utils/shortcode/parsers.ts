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
  ServiceDetailsData,
  BookingSectionData,
  BookingRoomOption,
  PricingItem,
  PricingSectionData,
  SkillItem,
  SkillSectionData,
  TeamMember,
  TeamSectionData,
  TeamSocialLink,
  TestimonialsSectionData,
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

export const parseServiceBlock = (html: string): ServiceSectionData => {
  const section = extractFirstBlockByClass(html, "section", "services-area") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");
  const serviceBlocks = extractBlocksByTag(section, "div", "single-services");

  return {
    subtitle: titleBlock ? extractTextFromTag(titleBlock, "h5") : null,
    title: titleBlock ? extractTextFromTag(titleBlock, "h2") : null,
    description: titleBlock ? extractTextFromTag(titleBlock, "p") : null,
    items: serviceBlocks
      .map((serviceBlock: string, index: number) => {
        const title = extractTextFromTag(serviceBlock, "h4")?.trim().replace(/^["']|["']$/g, '');
        const link = extractAttribute(extractFirstTag(serviceBlock, "h4 a") || "", "href");
        const image = extractFirstImage(serviceBlock); 
        const button = extractTextFromTag(serviceBlock, "button");
        const price = button ? button.replace(/[^0-9.]/g, '') : '';

        if (!title) return null;

        return {
          id: index,
          name: title,
          url: link || '#',
          image: image?.src || '',
          bookLabel: button || 'Book Now',
          price: price,
          amenities: [], // Can be enhanced later
        } satisfies ServiceItem;
      })
      .filter(Boolean) as ServiceItem[],
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

export const parseTestimonialsBlock = (html: string): TestimonialsSectionData => {
  // First try to parse as shortcode attributes
  const attributes = parseShortcodeAttributes(html);

  if (Object.keys(attributes).length > 0) {
    // Parse from shortcode attributes
    const testimonialIds = attributes.testimonial_ids
      ? attributes.testimonial_ids.split(',').map(id => id.trim()).filter(Boolean)
      : [];

    return {
      subtitle: attributes.subtitle || null,
      title: attributes.title || null,
      description: attributes.description || null,
      backgroundImage: attributes.background_image
        ? { src: attributes.background_image, alt: "" }
        : null,
      testimonialIds,
      items: [], // Items will be populated from testimonialIds via API
    };
  }

  // Fallback to HTML parsing
  const section = extractFirstBlockByClass(html, "section", "testimonial-area") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");
  const itemBlocks = extractBlocksByTag(section, "div", "single-testimonial");

  return {
    subtitle: null,
    title: titleBlock ? extractTextFromTag(titleBlock, "h2") : null,
    description: null,
    backgroundImage: null,
    testimonialIds: [],
    items: itemBlocks
      .map((itemBlock) => {
        const name = extractTextFromTag(itemBlock, "h4");
        const title = extractTextFromTag(itemBlock, "span") || null;
        const content = extractTextFromTag(itemBlock, "p");
        const image = extractFirstImage(itemBlock);

        if (!name || !content) return null;

        return {
          name,
          title,
          content,
          image,
          rating: null,
        };
      })
      .filter(Boolean) as any[], // Type assertion needed due to filter
  };
};
export const parseServiceDetailsBlock = (html: string): ServiceDetailsData => {
  // Lấy section chứa dữ liệu. Dùng id hoặc class bao ngoài cùng, nếu không có helper theo id thì dùng chính html gốc
  const section = html; 

  // Lấy các khối text chính
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title") || section;
  const subtitle = extractTextFromTag(titleBlock, "h5");
  const title = extractTextFromTag(titleBlock, "h2");
  const description = extractTextFromTag(titleBlock, "p");

  // Xử lý background image
  const animationsBlock = extractFirstBlockByClass(section, "div", "animations-01");
  const bgImgMatch = animationsBlock ? animationsBlock.match(/<img[^>]+src="([^">]+)"/) : null;
  const bgImageUrl = bgImgMatch ? bgImgMatch[1] : null;

  // Lấy danh sách item
  const itemBlocks = extractBlocksByTag(section, "div", "services-08-item");

  return {
    bgImageUrl,
    subtitle,
    title,
    description,
    items: itemBlocks
      .map((itemBlock) => {
        const itemTitle = extractTextFromTag(itemBlock, "h3");

        // Parse ảnh icon
        const iconBlock = extractFirstBlockByClass(itemBlock, "div", "services-icon2") || "";
        const iconImgUrl = iconBlock.match(/<img[^>]+src="([^">]+)"/)?.[1] || "";
        const iconImgAlt = iconBlock.match(/<img[^>]+alt="([^">]+)"/)?.[1] || itemTitle || "";

        // Parse ảnh thumb
        const thumbBlock = extractFirstBlockByClass(itemBlock, "div", "services-08-thumb") || "";
        const thumbImgUrl = thumbBlock.match(/<img[^>]+src="([^">]+)"/)?.[1] || "";
        const thumbImgAlt = thumbBlock.match(/<img[^>]+alt="([^">]+)"/)?.[1] || itemTitle || "";

        if (!itemTitle) return null;

        return { 
          title: itemTitle, 
          iconImgUrl, 
          iconImgAlt, 
          thumbImgUrl, 
          thumbImgAlt 
        };
      })
      .filter(Boolean) as ServiceDetailItem[],
  };
};
export const parseBookingBlock = (html: string): BookingSectionData => {
  const section = extractFirstBlockByClass(html, "section", "booking") || html;

  // 1. Lấy Title & Subtitle
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title") || section;
  const subtitle = extractTextFromTag(titleBlock, "h5");
  const title = extractTextFromTag(titleBlock, "h2");
  const description = extractTextFromTag(titleBlock, "p");
  // 2. Lấy Form Action URL
  const actionMatch = section.match(/<form[^>]+action="([^">]+)"/);
  const actionUrl = actionMatch ? actionMatch[1] : null;

  // 3. Lấy Ảnh
  const imgBlock = extractFirstBlockByClass(section, "div", "booking-img") || "";
  const imageSrc = imgBlock.match(/<img[^>]+src="([^">]+)"/)?.[1] || null;
  const imageAlt = imgBlock.match(/<img[^>]+alt="([^">]+)"/)?.[1] || "Booking Image";

  // 4. Lấy danh sách tuỳ chọn Phòng
  const rooms: BookingRoomOption[] = [];
  const roomSelectMatch = section.match(/<select[^>]+name="room_id"[^>]*>([\s\S]*?)<\/select>/);
  
  if (roomSelectMatch) {
    const optionsHtml = roomSelectMatch[1] || "";
    const optionRegex = /<option[^>]+value="([^"]+)"[^>]*>([^<]+)<\/option>/g;
    let match;
    while ((match = optionRegex.exec(optionsHtml)) !== null) {
      rooms.push({
        value: match[1],
        label: match[2].trim(),
      });
    }
  }

  return {
    subtitle,
    title,
    description,
    actionUrl,
    imageSrc,
    imageAlt,
    rooms,
  };
};