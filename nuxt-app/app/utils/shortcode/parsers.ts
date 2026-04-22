import {
  detectSocialPlatform,
  extractAllImages,
  extractAttribute,
  extractBlocksByTag,
  extractFirstBlockByClass,
  extractFirstDirectImage,
  extractFirstImage,
  extractFirstNthBlockByClass,
  extractInnerHtmlFromTag,
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
  AboutUsSectionData,
  AllRoomItem,
  AllRoomSectionData,
  BrandsSectionData,
  BrandItem,
  AvailabilityBookingFormSectionData,
  BlogPostsSectionData,
  BookingFormSectionData,
  BookingSelectOption,
  CheckAvailabilityFormSectionData,
  CuisineShowcaseSectionData,
  FaqItem,
  FaqsSectionData,
  FeatureAreaSectionData,
  FeaturedAmenitiesSectionData,
  FeaturedRoomsSectionData,
  FeatureGridItem,
  ForestFacilityShowcaseSectionData,
  GalleriesSectionData,
  GalleryFilter,
  GalleryItem,
  HeroStorySectionData,
  HeroBannerWithBookingFormSectionData,
  HotelPlacesSectionData,
  HotelServicesSectionData,
  IntroVideoSectionData,
  LocationTourismShowcaseSectionData,
  LogoShowcaseBannerSectionData,
  NewsSectionData,
  NewsletterSectionData,
  OnsenSpaGalleryItem,
  OnsenSpaGallerySectionData,
  PickupGalleryShowcaseSectionData,
  PricingItem,
  PricingSectionData,
  RoomMosaicShowcaseCard,
  RoomMosaicShowcaseSectionData,
  ServiceItem,
  SkillItem,
  SimpleSliderSectionData,
  SimpleSliderSlide,
  SpecialStoryShowcaseCard,
  SpecialStoryShowcaseNavEntry,
  SpecialStoryShowcaseSectionData,
  SpaCollageShowcaseSectionData,
  ShortcodeAction,
  TeamsSectionData,
  TeamMember,
  TeamSocialLink,
  TestimonialItem,
  TestimonialsSectionData,
  UserProfileMetric,
  UserProfileSectionData,
  WhyChooseUsSectionData,
} from "./types";

export const parseGenericShortcodeBlock = (html: string) => html;

const unwrapOuterTag = (html: string) => {
  const startIndex = html.indexOf(">");
  const endIndex = html.lastIndexOf("</");

  if (startIndex === -1 || endIndex === -1 || endIndex <= startIndex) {
    return html;
  }

  return html.slice(startIndex + 1, endIndex);
};

const buildAction = (link?: { raw: string; url: string }): ShortcodeAction | null => {
  if (!link?.url) {
    return null;
  }

  const label = normalizeText(link.raw);

  if (!label) {
    return null;
  }

  return {
    href: link.url,
    label,
  };
};

const extractInnerDivTexts = (html: string) => {
  const innerHtml = unwrapOuterTag(html);
  const lines: string[] = [];
  const pattern = /<div\b[^>]*>([\s\S]*?)<\/div>/gi;
  let match: RegExpExecArray | null;

  while ((match = pattern.exec(innerHtml))) {
    const value = normalizeText(match[1] || "");

    if (value) {
      lines.push(value);
    }
  }

  return lines;
};

const clampInteger = (value: string | null, fallback: number | null = null) => {
  if (!value) {
    return fallback;
  }

  const parsed = Number.parseInt(value, 10);
  return Number.isNaN(parsed) ? fallback : parsed;
};

const extractInputByName = (html: string, name: string) => {
  const pattern = new RegExp(
    `<input\\b[^>]*name\\s*=\\s*(['"])${name}\\1[^>]*>`,
    "i",
  );

  return pattern.exec(html)?.[0] || null;
};

const extractSelectByName = (html: string, name: string) => {
  const pattern = new RegExp(
    `<select\\b[^>]*name\\s*=\\s*(['"])${name}\\1[^>]*>[\\s\\S]*?<\\/select>`,
    "i",
  );

  return pattern.exec(html)?.[0] || null;
};

const extractSelectOptions = (selectHtml: string | null): BookingSelectOption[] => {
  if (!selectHtml) {
    return [];
  }

  const pattern = /<option\b([^>]*)value=(['"])(.*?)\2([^>]*)>([\s\S]*?)<\/option>/gi;
  const options: BookingSelectOption[] = [];
  let match: RegExpExecArray | null;

  while ((match = pattern.exec(selectHtml))) {
    const rawAttributes = `${match[1] || ""} ${match[4] || ""}`;
    options.push({
      value: normalizeText(match[3] || ""),
      label: normalizeText(match[5] || ""),
      selected: /selected/i.test(rawAttributes),
    });
  }

  return options;
};

const parseAvailabilityBookingForm = (html: string): AvailabilityBookingFormSectionData | null => {
  const formTag = extractFirstTag(html, "form");

  if (!formTag) {
    return null;
  }

  const titleBlock = extractFirstBlockByClass(formTag, "div", "section-title");
  const startInput = extractInputByName(formTag, "start_date");
  const endInput = extractInputByName(formTag, "end_date");
  const adultsInput = extractInputByName(formTag, "adults");
  const childrenInput = extractInputByName(formTag, "children");
  const roomsInput = extractInputByName(formTag, "rooms");

  return {
    title: titleBlock ? extractTextFromTag(titleBlock, "h2") : null,
    actionUrl: extractAttribute(formTag, "action"),
    dateFormat:
      extractAttribute(startInput || "", "data-date-format") ||
      extractAttribute(endInput || "", "data-date-format"),
    submitLabel: extractTextFromTag(formTag, "button"),
    startDate: extractAttribute(startInput || "", "value"),
    endDate: extractAttribute(endInput || "", "value"),
    minAdults: clampInteger(extractAttribute(adultsInput || "", "min"), 1),
    maxAdults: clampInteger(extractAttribute(adultsInput || "", "max"), 10),
    defaultAdults: clampInteger(extractAttribute(adultsInput || "", "value"), 1),
    defaultChildren: clampInteger(extractAttribute(childrenInput || "", "value"), 0),
    defaultRooms: clampInteger(extractAttribute(roomsInput || "", "value"), 1),
  };
};

const parseFeatureGridItem = (html: string): FeatureGridItem | null => {
  const titleLink = extractLinks(extractFirstTag(html, "h3") || "")[0];
  const actionLink = extractLinks(html).find((link) => !titleLink || link.url !== titleLink.url);
  const title =
    extractTextFromTag(html, "h3") ||
    extractTextFromTag(html, "h2") ||
    extractTextFromTag(html, "h4");
  const paragraphs = extractParagraphTexts(html);

  if (!title) {
    return null;
  }

  return {
    title,
    description: paragraphs[0] || null,
    image:
      extractFirstImage(extractFirstBlockByClass(html, "div", "services-08-thumb") || "") ||
      extractFirstImage(extractFirstBlockByClass(html, "div", "slide-post") || "") ||
      extractFirstImage(extractFirstBlockByClass(html, "div", "services-icon2") || "") ||
      extractFirstImage(extractFirstBlockByClass(html, "figure", "gallery-image") || "") ||
      extractFirstImage(html),
    href: titleLink?.url || actionLink?.url || null,
    actionLabel: actionLink ? normalizeText(actionLink.raw) : null,
    price: extractTextFromTag(html, "div", "service-price"),
    meta:
      extractTextFromTag(html, "div", "date-home") ||
      extractTextFromTag(html, "p", "blog-item-custom-truncate"),
  };
};

export const parseTeamsBlock = (html: string): TeamsSectionData => {
  const section = extractFirstBlockByClass(html, "section", "team-area") || html;
  const title = extractTextFromTag(section, "h2") || "";
  const subtitle = extractTextFromTag(section, "h5") || "";
  const description = extractTextFromTag(section, "p") || "";
  const memberBlocks = extractBlocksByTag(section, "div", "single-team");

  return {
    title: title || "",
    subtitle: subtitle || "",
    description: description || "",
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

export const parseAboutUsBlock = (html: string): AboutUsSectionData => {
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

export const parseWhyChooseUsBlock = (html: string): WhyChooseUsSectionData => {
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

export const parseSimpleSliderBlock = (html: string): SimpleSliderSectionData => {
  const section = extractFirstBlockByClass(html, "section", "slider-showcase-area") || html;
  const showcaseSlides = extractBlocksByTag(section, "div", "slider-showcase-slide");
  const slides = showcaseSlides.length
    ? showcaseSlides
    : extractBlocksByTag(section, "div", "single-slider");

  return {
    slides: slides
      .map((slide) => {
        const mediaLink = extractLinks(slide)[0];
        const mediaBlock =
          extractFirstBlockByClass(slide, "a", "slider-showcase-media") ||
          extractFirstBlockByClass(slide, "div", "slider-showcase-media") ||
          slide;
        const captionHtml =
          extractInnerHtmlFromTag(slide, "p", "slider-showcase-caption") ||
          extractInnerHtmlFromTag(slide, "h1", "slider-showcase-title") ||
          extractInnerHtmlFromTag(slide, "h2", "slider-showcase-title");
        const caption =
          extractTextFromTag(slide, "p", "slider-showcase-caption") ||
          extractTextFromTag(slide, "h1", "slider-showcase-title") ||
          extractTextFromTag(slide, "h2", "slider-showcase-title") ||
          extractFirstParagraphText(slide) ||
          extractAttribute(mediaBlock, "aria-label");

        return {
          image: extractFirstImage(mediaBlock),
          caption,
          captionHtml: captionHtml || caption || null,
          href: mediaLink?.url || null,
        } satisfies SimpleSliderSlide;
      })
      .filter((slide) => slide.image?.src || slide.caption) as SimpleSliderSlide[],
  };
};

export const parseLogoShowcaseBannerBlock = (html: string): LogoShowcaseBannerSectionData => {
  const section = extractFirstBlockByClass(html, "section", "logo-showcase-banner") || html;
  const topBlock = extractFirstBlockByClass(section, "div", "logo-showcase-banner__top");
  const bottomBlock = extractFirstBlockByClass(section, "div", "logo-showcase-banner__bottom");
  const logoBlock = extractFirstBlockByClass(section, "div", "logo-showcase-banner__center") || section;

  return {
    backgroundImage: extractStyleImage(section),
    logoImage: extractFirstImage(logoBlock),
    topLines: topBlock ? extractParagraphTexts(topBlock) : [],
    bottomLines: bottomBlock ? extractParagraphTexts(bottomBlock) : [],
  };
};

export const parseRoomMosaicShowcaseBlock = (html: string): RoomMosaicShowcaseSectionData => {
  const section = extractFirstBlockByClass(html, "section", "room-mosaic-showcase") || html;
  const heroBlock = extractFirstBlockByClass(section, "div", "room-mosaic-showcase__hero") || section;
  const descriptionBlock = extractFirstBlockByClass(section, "div", "room-mosaic-showcase__description");
  const mainImageBlock = extractFirstBlockByClass(section, "div", "room-mosaic-showcase__main");
  const sideTextBlock = extractFirstBlockByClass(section, "div", "room-mosaic-showcase__side-text");
  const ctaBlock = extractFirstBlockByClass(section, "div", "room-mosaic-showcase__cta");
  const actionLink = extractLinks(ctaBlock || "")[0];

  return {
    backgroundImage: extractStyleImage(heroBlock),
    subtitle: extractTextFromTag(section, "p", "room-mosaic-showcase__subtitle"),
    title: extractTextFromTag(section, "h2", "room-mosaic-showcase__title"),
    description: descriptionBlock ? normalizeText(descriptionBlock) : null,
    mainImage: mainImageBlock ? extractFirstImage(mainImageBlock) : null,
    cards: extractBlocksByTag(section, "div", "room-mosaic-showcase__card")
      .map((cardBlock) => ({
        title: extractTextFromTag(cardBlock, "div", "room-mosaic-showcase__card-title"),
        image: extractFirstImage(cardBlock),
      }) satisfies RoomMosaicShowcaseCard)
      .filter((card) => card.title || card.image?.src),
    sideTextLines: sideTextBlock ? extractInnerDivTexts(sideTextBlock) : [],
    roomItems: extractBlocksByTag(section, "div", "room-mosaic-showcase__room-item")
      .map((item) => normalizeText(item))
      .filter(Boolean),
    ctaTitle: extractTextFromTag(section, "h3", "room-mosaic-showcase__cta-title"),
    action: buildAction(actionLink),
  };
};

export const parseOnsenSpaGalleryBlock = (html: string): OnsenSpaGallerySectionData => {
  const section = extractFirstBlockByClass(html, "section", "onsen-spa-gallery") || html;
  const footerBlock = extractFirstBlockByClass(section, "div", "onsen-spa-gallery__footer");

  return {
    backgroundImage: extractStyleImage(section),
    backgroundTitle: extractTextFromTag(section, "div", "onsen-spa-gallery__bg-title"),
    subtitle: extractTextFromTag(section, "div", "onsen-spa-gallery__subtitle"),
    items: extractBlocksByTag(section, "div", "onsen-spa-gallery__item")
      .map((itemBlock) => ({
        title: extractTextFromTag(itemBlock, "div", "onsen-spa-gallery__item-title"),
        image: extractFirstImage(itemBlock),
      }) satisfies OnsenSpaGalleryItem)
      .filter((item) => item.title || item.image?.src),
    sectionLabel: extractTextFromTag(section, "div", "onsen-spa-gallery__label"),
    actions: extractLinks(footerBlock || "")
      .map((link) => buildAction(link))
      .filter(Boolean) as ShortcodeAction[],
  };
};

export const parsePickupGalleryShowcaseBlock = (html: string): PickupGalleryShowcaseSectionData => {
  const section = extractFirstBlockByClass(html, "section", "pickup") || html;
  const galleryBlocks = extractBlocksByTag(section, "div", "pickup__gallery-item");
  const actionLink = extractLinks(extractFirstBlockByClass(section, "div", "pickup__info") || "")[0];

  return {
    backgroundImage: extractStyleImage(section),
    pretitle: extractTextFromTag(section, "span", "pickup__pretitle"),
    title: extractTextFromTag(section, "h2", "pickup__title"),
    galleryImages: galleryBlocks
      .map((block) => extractStyleImage(block))
      .filter((image): image is NonNullable<typeof image> => Boolean(image?.src)),
    description: extractTextFromTag(section, "p", "pickup__desc"),
    sectionLabel: extractTextFromTag(section, "span", "pickup__label"),
    action: buildAction(actionLink),
  };
};

export const parseSpecialStoryShowcaseBlock = (html: string): SpecialStoryShowcaseSectionData => {
  const section = extractFirstBlockByClass(html, "section", "story") || html;
  const actionLink = extractLinks(section).find((link) => link.raw.includes("story__btn"));

  return {
    backgroundImage: extractStyleImage(section),
    decorativeText: extractTextFromTag(section, "span", "story__deco"),
    sectionLabel: extractTextFromTag(section, "span", "story__label"),
    description: extractTextFromTag(section, "p", "story__desc"),
    cards: extractBlocksByTag(section, "div", "story__card")
      .map((cardBlock) => ({
        title: extractTextFromTag(cardBlock, "span", "story__card-title"),
        image: extractFirstImage(cardBlock),
      }) satisfies SpecialStoryShowcaseCard)
      .filter((card) => card.title || card.image?.src),
    navEntries: extractBlocksByTag(section, "div", "story__nav-item")
      .map((navBlock) => ({
        text: extractTextFromTag(navBlock, "span", "story__nav-text"),
        badgeTop: extractTextFromTag(navBlock, "span", "story__nav-badge-top"),
        badgeVolume: extractTextFromTag(navBlock, "span", "story__nav-badge-vol"),
      }) satisfies SpecialStoryShowcaseNavEntry)
      .filter((entry) => entry.text || entry.badgeTop || entry.badgeVolume),
    action: buildAction(actionLink),
  };
};

export const parseForestFacilityShowcaseBlock = (html: string): ForestFacilityShowcaseSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-forest-facility-showcase") || html;
  const imagesBlock = extractFirstBlockByClass(section, "div", "rfs__images");
  const titleLayer = extractFirstBlockByClass(section, "div", "rfs__title-layer");
  const infoLayer = extractFirstBlockByClass(section, "div", "rfs__info-layer");
  const infoBlock = extractFirstBlockByClass(infoLayer || section, "div", "rfs__info") || infoLayer || "";
  const titleTag = extractFirstTag(titleLayer || section, "h2", "rfs__heading");
  const descriptionTag = extractFirstTag(infoBlock || section, "p", "rfs__desc");
  const labelTag = extractFirstTag(infoBlock || section, "span", "rfs__label")
    || extractFirstTag(infoBlock || section, "div", "rfs__label");
  const actionLink = extractLinks(infoBlock || "")[0];
  const sectionParagraphs = extractParagraphTexts(section);
  const infoParagraphs = extractParagraphTexts(infoBlock || "");

  return {
    leftImage: extractFirstImage(extractFirstBlockByClass(imagesBlock || "", "div", "rfs__img-left") || ""),
    rightImages: extractBlocksByTag(imagesBlock || "", "div", "rfs__img-right-item")
      .map((block) => extractStyleImage(block))
      .filter((image): image is NonNullable<typeof image> => Boolean(image?.src)),
    title:
      (titleTag ? extractTextFromTag(titleTag, "h2") : null) ||
      extractTextFromTag(titleLayer || section, "h2", "rfs__heading") ||
      extractTextFromTag(titleLayer || section, "h2") ||
      extractTextFromTag(section, "h2") ||
      extractTextFromTag(section, "h3"),
    description:
      (descriptionTag ? extractTextFromTag(descriptionTag, "p") : null) ||
      extractTextFromTag(infoBlock || section, "p", "rfs__desc") ||
      extractTextFromTag(infoBlock || section, "p") ||
      infoParagraphs[0] ||
      sectionParagraphs[0] ||
      null,
    sectionLabel:
      (labelTag ? extractTextFromTag(labelTag, "span") || extractTextFromTag(labelTag, "div") : null) ||
      extractTextFromTag(infoBlock || section, "div", "rfs__label") ||
      extractTextFromTag(infoBlock || section, "span", "rfs__label"),
    action: buildAction(actionLink),
  };
};

export const parseLocationTourismShowcaseBlock = (html: string): LocationTourismShowcaseSectionData => {
  const section =
    extractFirstBlockByClass(html, "section", "shortcode-location-tourism-showcase") || html;
  const infoColumns = extractBlocksByTag(section, "div", "loc__col");
  const accessBlock = infoColumns[0] || "";
  const tourismBlock = infoColumns[1] || "";

  return {
    backgroundImage: extractStyleImage(section),
    mapImage: extractFirstImage(extractFirstBlockByClass(section, "div", "loc__map") || ""),
    gridImages: extractBlocksByTag(section, "div", "loc__grid-item")
      .map((block) => extractStyleImage(block))
      .filter((image): image is NonNullable<typeof image> => Boolean(image?.src)),
    decorativeText: extractTextFromTag(section, "div", "loc__deco"),
    title: extractTextFromTag(section, "h2", "loc__title"),
    access: {
      description: extractTextFromTag(accessBlock, "p", "loc__desc"),
      label: extractTextFromTag(accessBlock, "div", "loc__col-label"),
      action: buildAction(extractLinks(accessBlock)[0]),
    },
    tourism: {
      description: extractTextFromTag(tourismBlock, "p", "loc__desc"),
      label: extractTextFromTag(tourismBlock, "div", "loc__col-label"),
      action: buildAction(extractLinks(tourismBlock)[0]),
    },
  };
};

export const parseCuisineShowcaseBlock = (html: string): CuisineShowcaseSectionData => {
  const attributes = parseShortcodeAttributes(html);
  const section = extractFirstBlockByClass(html, "section", "shortcode-cuisine-showcase") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "cuisine__copy") || section;
  const imagesBlock = extractFirstBlockByClass(section, "div", "cuisine__images") || section;
  const footerBlock = extractFirstBlockByClass(section, "div", "cuisine__footer");
  const titleTag = extractFirstTag(titleBlock, "h2", "cuisine__title") || extractFirstTag(titleBlock, "h3", "cuisine__title");
  const descriptionTag = extractFirstTag(titleBlock, "p", "cuisine__desc");
  const labelTag = extractFirstTag(footerBlock || section, "span", "cuisine__label")
    || extractFirstTag(footerBlock || section, "div", "cuisine__label");
  const sectionParagraphs = extractParagraphTexts(titleBlock);
  const footerAction = buildAction(extractLinks(footerBlock || "")[0]);
  const buttonLabel = normalizeText(attributes.button_label || "");
  const buttonUrl = normalizeText(attributes.button_url || "");
  const descriptionFromAttributes = normalizeText((attributes.description || "").replace(/\{\{NEWLINE\}\}/g, "\n"));
  const sectionLabelFromAttributes = normalizeText(attributes.section_label || "");
  const parsedImages = extractBlocksByTag(imagesBlock, "div", "cuisine__img")
    .map((block) => extractFirstImage(block))
    .filter((image): image is NonNullable<typeof image> => Boolean(image?.src));
  const imagesFromAttributes = [attributes.image_1, attributes.image_2]
    .map((src) => normalizeText(src || ""))
    .filter(Boolean)
    .map((src) => ({ src, alt: "" }));

  return {
    title:
      normalizeText(attributes.title || "") ||
      (titleTag ? extractTextFromTag(titleTag, "h2") || extractTextFromTag(titleTag, "h3") : null) ||
      extractTextFromTag(titleBlock, "h2", "cuisine__title") ||
      extractTextFromTag(titleBlock, "h2") ||
      extractTextFromTag(titleBlock, "h3"),
    description:
      descriptionFromAttributes ||
      (descriptionTag ? extractTextFromTag(descriptionTag, "p") : null) ||
      extractTextFromTag(titleBlock, "p", "cuisine__desc") ||
      extractTextFromTag(titleBlock, "p") ||
      sectionParagraphs[0] ||
      null,
    images: parsedImages.length ? parsedImages : imagesFromAttributes,
    sectionLabel:
      sectionLabelFromAttributes ||
      (labelTag ? extractTextFromTag(labelTag, "span") || extractTextFromTag(labelTag, "div") : null) ||
      extractTextFromTag(footerBlock || section, "span", "cuisine__label") ||
      extractTextFromTag(footerBlock || section, "div", "cuisine__label"),
    action:
      footerAction ||
      (buttonUrl && buttonLabel
        ? {
            href: buttonUrl,
            label: buttonLabel,
          }
        : null),
    buttonLabel: buttonLabel || footerAction?.label || null,
    buttonUrl: buttonUrl || footerAction?.href || null,
  };
};

export const parseSpaCollageShowcaseBlock = (html: string): SpaCollageShowcaseSectionData => {
  const section = extractFirstBlockByClass(html, "section", "spa-collage-showcase") || html;
  const leftBlock = extractFirstBlockByClass(section, "div", "spa-collage-showcase__left");
  const rightBlock = extractFirstBlockByClass(section, "div", "spa-collage-showcase__right");

  return {
    leftPanelImage: extractStyleImage(leftBlock || ""),
    rightPanelImage: extractStyleImage(rightBlock || ""),
    bottomImages: extractBlocksByTag(section, "div", "spa-collage-showcase__bottom-item")
      .map((block) => extractStyleImage(block))
      .filter((image): image is NonNullable<typeof image> => Boolean(image?.src)),
    title: extractTextFromTag(section, "h2", "spa-collage-showcase__title"),
    subtitle: extractTextFromTag(section, "div", "spa-collage-showcase__subtitle"),
    description: extractTextFromTag(section, "div", "spa-collage-showcase__description"),
  };
};

export const parseHeroBannerWithBookingFormBlock = (html: string): HeroBannerWithBookingFormSectionData => {
  const section =
    extractFirstBlockByClass(html, "section", "shortcode-hero-banner-with-booking-form") || html;
  const sliderBlock = extractFirstBlockByClass(section, "div", "single-slider") || section;
  const contentBlock = extractFirstBlockByClass(section, "div", "slider-content");
  const actionLink = extractLinks(extractFirstBlockByClass(section, "div", "slider-btn") || "")[0];
  const bookingBlock = extractFirstBlockByClass(section, "div", "booking-area2");

  return {
    backgroundColor: extractStyleValue(extractFirstBlockByClass(section, "div", "slider-active") || "", "background"),
    backgroundImage: extractStyleImage(sliderBlock),
    title: extractTextFromTag(contentBlock || "", "h2"),
    description: extractTextFromTag(contentBlock || "", "p"),
    action: buildAction(actionLink),
    bookingForm: bookingBlock ? parseAvailabilityBookingForm(bookingBlock) : null,
  };
};

export const parseCheckAvailabilityFormBlock = (html: string): CheckAvailabilityFormSectionData => {
  const section =
    extractFirstBlockByClass(html, "div", "shortcode-check-availability-form") || html;

  return {
    bookingForm: parseAvailabilityBookingForm(section),
  };
};

export const parseBookingFormBlock = (html: string): BookingFormSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-booking-form") || html;
  const formTag = extractFirstTag(section, "form");
  const hiddenToken = extractInputByName(formTag || "", "_token");

  return {
    shapeImage: extractFirstImage(extractFirstBlockByClass(section, "div", "animations-01") || ""),
    image: extractFirstImage(extractFirstBlockByClass(section, "div", "booking-img") || ""),
    subtitle: extractTextFromTag(section, "h5"),
    title: extractTextFromTag(section, "h2"),
    actionUrl: extractAttribute(formTag || "", "action"),
    method: (extractAttribute(formTag || "", "method") || "post").toUpperCase(),
    csrfToken: extractAttribute(hiddenToken || "", "value"),
    dateFormat:
      extractAttribute(extractInputByName(formTag || "", "start_date") || "", "data-date-format") ||
      extractAttribute(extractInputByName(formTag || "", "end_date") || "", "data-date-format"),
    startDate: extractAttribute(extractInputByName(formTag || "", "start_date") || "", "value"),
    endDate: extractAttribute(extractInputByName(formTag || "", "end_date") || "", "value"),
    submitLabel: extractTextFromTag(formTag || "", "button"),
    roomOptions: extractSelectOptions(extractSelectByName(formTag || "", "room_id")),
    adultOptions: extractSelectOptions(extractSelectByName(formTag || "", "adults")),
  };
};

export const parseFeaturedAmenitiesBlock = (html: string): FeaturedAmenitiesSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-featured-amenities") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");

  return {
    backgroundColor: extractStyleValue(section, "background-color"),
    backgroundImage: extractFirstImage(extractFirstBlockByClass(section, "div", "animations-01") || ""),
    subtitle: extractTextFromTag(titleBlock || "", "h5"),
    title: extractTextFromTag(titleBlock || "", "h2"),
    description: extractTextFromTag(titleBlock || "", "p"),
    items: extractBlocksByTag(section, "div", "services-08-item")
      .map(parseFeatureGridItem)
      .filter(Boolean) as FeatureGridItem[],
  };
};

export const parseServiceListBlock = (html: string): ServiceListSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-service-list") || html;

  return {
    backgroundImage: extractFirstImage(extractFirstBlockByClass(section, "div", "animations-01") || ""),
    items: extractBlocksByTag(section, "div", "services-08-item")
      .map(parseFeatureGridItem)
      .filter(Boolean) as FeatureGridItem[],
  };
};

export const parseHotelPlacesBlock = (html: string): HotelPlacesSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-hotel-places") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");

  return {
    subtitle: extractTextFromTag(titleBlock || "", "h5"),
    title: extractTextFromTag(titleBlock || "", "h2"),
    description: extractTextFromTag(titleBlock || "", "p"),
    items: extractBlocksByTag(section, "div", "bsingle__post")
      .map(parseFeatureGridItem)
      .filter(Boolean) as FeatureGridItem[],
  };
};

export const parseHotelServicesBlock = (html: string): HotelServicesSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-hotel-services") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");

  return {
    subtitle: extractTextFromTag(titleBlock || "", "h5"),
    title: extractTextFromTag(titleBlock || "", "h2"),
    description: extractTextFromTag(titleBlock || "", "p"),
    items: extractBlocksByTag(section, "div", "services-08-item")
      .map(parseFeatureGridItem)
      .filter(Boolean) as FeatureGridItem[],
  };
};

export const parseNewsBlock = (html: string): NewsSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-news") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");

  return {
    backgroundImage: extractFirstImage(extractFirstBlockByClass(section, "div", "animations-02") || ""),
    subtitle: extractTextFromTag(titleBlock || "", "h5"),
    title: extractTextFromTag(titleBlock || "", "h2"),
    description: extractTextFromTag(titleBlock || "", "p"),
    items: extractBlocksByTag(section, "div", "bsingle__post")
      .map(parseFeatureGridItem)
      .filter(Boolean) as FeatureGridItem[],
  };
};

export const parseBlogPostsBlock = (html: string): BlogPostsSectionData => {
  const paginationBlock = extractFirstBlockByClass(html, "div", "text-center mt-30");
  const postBlocks = extractBlocksByTag(html, "div", "bsingle__post")
    .filter((block) => block.includes("blog__btn") || block.includes("blog-item-custom-truncate"));

  return {
    items: postBlocks
      .map(parseFeatureGridItem)
      .filter(Boolean) as FeatureGridItem[],
    paginationHtml: paginationBlock ? paginationBlock.trim() : null,
  };
};

export const parseTestimonialsBlock = (html: string): TestimonialsSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-testimonials") || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");

  return {
    backgroundImage: extractStyleImage(section),
    subtitle: extractTextFromTag(titleBlock || "", "h5"),
    title: extractTextFromTag(titleBlock || "", "h2"),
    description: extractTextFromTag(titleBlock || "", "p"),
    items: extractBlocksByTag(section, "div", "single-testimonial")
      .map((block) => {
        const name = extractTextFromTag(block, "h6");

        if (!name) {
          return null;
        }

        return {
          name,
          image: extractFirstImage(extractFirstBlockByClass(block, "div", "testi-author") || ""),
          content: extractTextFromTag(block, "p"),
        } satisfies TestimonialItem;
      })
      .filter(Boolean) as TestimonialItem[],
  };
};

export const parseGalleriesBlock = (html: string): GalleriesSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-galleries") || html;
  const filters = extractBlocksByTag(section, "button")
    .map((button) => {
      const value = extractAttribute(button, "data-filter");
      const label = normalizeText(button);

      if (!label || !value) {
        return null;
      }

      return {
        label,
        value,
      } satisfies GalleryFilter;
    })
    .filter(Boolean) as GalleryFilter[];

  return {
    filters,
    items: extractBlocksByTag(section, "div", "grid-item")
      .map((block) => {
        const link = extractLinks(block)[0];
        const className = extractAttribute(block, "class");
        const filter =
          className
            ?.split(/\s+/)
            .find((value) => value && value !== "grid-item") || null;

        return {
          title: extractAttribute(link?.raw || "", "title"),
          image: extractFirstImage(block),
          href: link?.url || null,
          filter,
        } satisfies GalleryItem;
      })
      .filter((item) => item.image?.src || item.href) as GalleryItem[],
  };
};

export const parseFaqsBlock = (html: string): FaqsSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-faqs") || html;

  return {
    items: extractBlocksByTag(section, "div", "card")
      .map((block) => {
        const button = extractFirstTag(block, "button", "faq-btn");
        const question = normalizeText(button || "");

        if (!question) {
          return null;
        }

        const target = extractAttribute(button || "", "data-bs-target") || "";
        const sortNumber = (target.match(/collapse(\d+)/i)?.[1] || "").trim();
        const answer = extractFirstParagraphText(block) || "";

        return {
          id: sortNumber || question,
          question,
          answer,
        } satisfies FaqItem;
      })
      .filter(Boolean)
      .sort((a, b) => Number.parseInt(a.id, 10) - Number.parseInt(b.id, 10)) as FaqItem[],
  };
};

export const parseBrandsBlock = (html: string): BrandsSectionData => {
  const section = extractFirstBlockByClass(html, "div", "shortcode-brands") || html;

  return {
    backgroundColor: extractStyleValue(section, "background-color"),
    items: extractBlocksByTag(section, "div", "single-brand")
      .map((block) => {
        const link = extractLinks(block)[0];
        const image = extractFirstImage(block);

        if (!image?.src) {
          return null;
        }

        return {
          name: image.alt || null,
          image,
          href: link?.url || null,
        } satisfies BrandItem;
      })
      .filter(Boolean) as BrandItem[],
  };
};

export const parseIntroVideoBlock = (html: string): IntroVideoSectionData => {
  const section = extractFirstBlockByClass(html, "section", "shortcode-intro-video") || html;
  const videoBlock = extractFirstBlockByClass(section, "div", "s-video-content");
  const videoLink = extractLinks(videoBlock || "")[0];

  return {
    backgroundImage: extractStyleImage(section),
    buttonIcon: extractFirstImage(videoBlock || ""),
    videoUrl: videoLink?.url || null,
    title: extractTextFromTag(section, "h2"),
  };
};

export const parseUserProfileBlock = (html: string): UserProfileSectionData => {
  const section = extractFirstBlockByClass(html, "div", "shortcode-user-profile") || html;

  return {
    metrics: extractBlocksByTag(section, "div", "skill mb-30")
      .map((block) => {
        const title = extractTextFromTag(block, "div", "skill-name");
        const percentage = clampInteger(
          extractAttribute(extractFirstTag(block, "div", "skill-per") || "", "id"),
          null,
        );

        if (!title || percentage === null) {
          return null;
        }

        return {
          title,
          percentage: Math.min(Math.max(percentage, 0), 100),
        } satisfies UserProfileMetric;
      })
      .filter(Boolean) as UserProfileMetric[],
    images: extractBlocksByTag(section, "figure", "image")
      .map((block) => extractFirstImage(block))
      .filter((image): image is NonNullable<typeof image> => Boolean(image?.src)),
  };
};

export const parseFeatureAreaBlock = (html: string): FeatureAreaSectionData => {
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

export const parseServicesBlock = (html: string): FeatureAreaSectionData => {
  const section = parseFeatureAreaBlock(html);

  return {
    ...section,
    backgroundColor: section.backgroundColor || "#f7f5f1",
    secondaryImage: null,
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
  const section =
    extractFirstBlockByClass(html, "section", "services-area") ||
    extractFirstBlockByClass(html, "section", "shortcode-all-rooms") ||
    html;
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
  const sectionParagraphs = extractParagraphTexts(section);

  const pricingBlocks = [
    ...extractBlocksByTag(section, "div", "single-pricing"),
    ...extractBlocksByTag(section, "div", "pricing-box"),
  ];

  const parsePriceText = (priceText: string): { price: string; currency: string } => {
    const normalized = (priceText || "").trim();

    const priceMatch = normalized.match(/^([^\d]*)([\d.,]+)(.*)$/);
    if (!priceMatch) {
      return { price: normalized, currency: "" };
    }

    const prefix = priceMatch[1]?.trim() || "";
    const digits = priceMatch[2]?.trim() || normalized;
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
        const description = attributes[`description_${itemIndex}`] || null;
        const features = getFeatures(attributes[`feature_list_${itemIndex}`] || null);
        const buttonLabel = attributes[`button_label_${itemIndex}`] || "Bat dau ngay";
        const buttonUrl = attributes[`button_url_${itemIndex}`] || "#";

        if (!title || !priceText) {
          return null;
        }

        const parsedPrice = parsePriceText(priceText);

        return {
          title,
          description,
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

  const builtItems = buildPricingItems();

  return {
    backgroundColor: attributes.background_color || null,

    backgroundImage1: attributes.background_image_1
      ? { src: attributes.background_image_1, alt: "" }
      : extractFirstBlockByClass(section, "div", "animations-01")
        ? extractFirstImage(extractFirstBlockByClass(section, "div", "animations-01") || "")
        : null,

    backgroundImage2: attributes.background_image_2
      ? { src: attributes.background_image_2, alt: "" }
      : extractFirstBlockByClass(section, "div", "animations-02")
        ? extractFirstImage(extractFirstBlockByClass(section, "div", "animations-02") || "")
        : null,

    subtitle: attributes.subtitle
      ? attributes.subtitle
      : titleBlock
        ? extractTextFromTag(titleBlock, "h5")
        : null,

    title: attributes.title
      ? attributes.title
      : titleBlock
        ? extractTextFromTag(titleBlock, "h2")
        : null,

    description:
      attributes.description ||
      (titleBlock
        ? (() => {
            const titleBlockText = extractTextFromTag(titleBlock, "p");
            if (titleBlockText) return titleBlockText;

            return sectionParagraphs.length ? sectionParagraphs[0] : null;
          })()
        : (sectionParagraphs.length ? sectionParagraphs[0] : null)),

    items: builtItems.length
      ? builtItems
      : pricingBlocks
          .map((pricingBlock) => {
            const title =
              extractTextFromTag(pricingBlock, "h3") ||
              extractTextFromTag(pricingBlock, "h4");

            const description =
              extractFirstBlockByClass(pricingBlock, "div", "pricing-head")
                ? extractTextFromTag(
                    extractFirstBlockByClass(pricingBlock, "div", "pricing-head") || "",
                    "p",
                  )
                : extractTextFromTag(pricingBlock, "p");

            const priceText =
              extractTextFromTag(pricingBlock, "h2") ||
              extractTextFromTag(
                extractFirstBlockByClass(pricingBlock, "div", "price-count") || "",
                "h2",
              );
            const features = extractListItems(pricingBlock);
            const buttonLink =
              extractLinks(extractFirstBlockByClass(pricingBlock, "div", "pricing-btn") || "")[0] ||
              extractLinks(pricingBlock)[0];

            const period = extractTextFromTag(pricingBlock, "div", "month") || "";

            if (!title || !priceText) return null;

            const parsedPrice = parsePriceText(priceText);

            return {
              title,
              description,
              price: parsedPrice.price,
              currency: parsedPrice.currency,
              period,
              features,
              buttonLabel: buttonLink ? normalizeText(buttonLink.raw) : "Bat dau ngay",
              buttonUrl: buttonLink?.url || "#",
              isPopular:
                pricingBlock.includes("popular") || pricingBlock.includes("featured"),
            } satisfies PricingItem;
          })
          .filter(Boolean) as PricingItem[],
  };
};
const parseRoomsListingSection = (html: string, sectionClass: string): FeaturedRoomsSectionData => {
  const section = extractFirstBlockByClass(html, "section", sectionClass)
    || extractFirstBlockByClass(html, "section", "services-area")
    || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");
  const serviceBlocks = extractBlocksByTag(section, "div", "single-services");
  const paginationBlock = extractFirstBlockByClass(section, "div", "text-center mt-30");

  return {
    countLabel: extractTextFromTag(section, "h3"),
    paginationHtml: paginationBlock ? paginationBlock.trim() : null,
    subtitle: titleBlock ? extractTextFromTag(titleBlock, "h5") : null,
    title: titleBlock ? extractTextFromTag(titleBlock, "h2") : null,
    description: titleBlock ? extractTextFromTag(titleBlock, "p") : null,
    items: serviceBlocks
      .map((serviceBlock: string, index: number) => {
        const title = extractTextFromTag(serviceBlock, "h4")?.trim().replace(/^["']|["']$/g, '');
        const link = extractAttribute(extractFirstTag(serviceBlock, "h4 a") || "", "href");
        const image = extractFirstImage(serviceBlock); 
        const description = extractFirstParagraphText(serviceBlock);
        const button = extractTextFromTag(serviceBlock, "button");
        const price = button ? button.replace(/[^0-9.]/g, '') : '';
        const amenityImages = extractBlocksByTag(serviceBlock, "li")
          .map((amenityBlock) => extractFirstImage(amenityBlock)?.alt || null)
          .filter((amenity): amenity is string => Boolean(amenity));

        if (!title) return null;

        return {
          id: index,
          name: title,
          url: link || '#',
          image: image?.src || '',
          description,
          bookLabel: button || 'Book Now',
          price: price,
          amenities: amenityImages,
        } satisfies ServiceItem;
      })
      .filter(Boolean) as any[], // Type assertion needed due to filter
  };
};

export const parseFeaturedRoomsBlock = (html: string): FeaturedRoomsSectionData =>
  parseRoomsListingSection(html, "shortcode-featured-rooms");

export const parseRoomListBlock = (html: string): FeaturedRoomsSectionData =>
  parseRoomsListingSection(html, "shortcode-room-list");

const parseAllRoomsSection = (html: string, sectionClass: string): AllRoomSectionData => {
  const section = extractFirstBlockByClass(html, "section", sectionClass)
    || extractFirstBlockByClass(html, "section", "services-area")
    || html;
  const titleBlock = extractFirstBlockByClass(section, "div", "section-title");
  const serviceBlocks = extractBlocksByTag(section, "div", "single-services");
  const paginationBlock = extractFirstBlockByClass(section, "div", "text-center mt-30");

  return {
    countLabel: extractTextFromTag(section, "h3"),
    paginationHtml: paginationBlock ? paginationBlock.trim() : null,
    subtitle: titleBlock ? extractTextFromTag(titleBlock, "h5") : null,
    title: titleBlock ? extractTextFromTag(titleBlock, "h2") : null,
    description: titleBlock ? extractTextFromTag(titleBlock, "p") : null,
    items: serviceBlocks
      .map((serviceBlock: string, index: number) => {
        const title = extractTextFromTag(serviceBlock, "h4")?.trim().replace(/^["']|["']$/g, '');
        const link = extractAttribute(extractFirstTag(serviceBlock, "h4 a") || "", "href");
        const image = extractFirstImage(serviceBlock); 
        const description = extractFirstParagraphText(serviceBlock);
        const button = extractTextFromTag(serviceBlock, "button");
        const price = button ? button.replace(/[^0-9.]/g, '') : '';
        const amenityImages = extractBlocksByTag(serviceBlock, "li")
          .map((amenityBlock) => extractFirstImage(amenityBlock)?.alt || null)
          .filter((amenity): amenity is string => Boolean(amenity));

        // Extract room-specific fields from room-specifications structure
        const roomSpecsBlock = extractFirstBlockByClass(serviceBlock, "div", "room-specifications");
        
        let size = null;
        let number_of_beds = null;
        let max_adults = null;
        let max_children = null;
        
        if (roomSpecsBlock) {
          // Extract from individual room-spec li elements
          const roomSpecs = extractBlocksByTag(roomSpecsBlock, "li", "room-spec");
          
          roomSpecs.forEach((spec) => {
            const icon = extractFirstTag(spec, "i");
            const text = extractTextFromTag(spec, "span") || "";
            
            if (icon?.includes('fa-ruler-combined')) {
              size = parseInt(text) || null;
            } else if (icon?.includes('fa-bed')) {
              number_of_beds = parseInt(text) || null;
            } else if (icon?.includes('fa-user')) {
              max_adults = parseInt(text) || null;
            } else if (icon?.includes('fa-child')) {
              max_children = parseInt(text) || null;
            }
          });
        }
        
        // Fallback to regex extraction if room-specifications not found
        if (!size) {
          const sizeText = extractFirstParagraphText(serviceBlock)?.match(/\d+(?:\.\d+)?\s*(?:m²|m²|sqm|sqft|m|meters?|metres?)/gi)?.[0] || "0";
          size = parseInt(sizeText) || null;
        }
        
        if (!number_of_beds) {
          const bedsText = extractFirstParagraphText(serviceBlock)?.match(/\d+\s*(?:bed|beds|giuong|giuong)/gi)?.[0] || "0";
          number_of_beds = parseInt(bedsText) || null;
        }
        
        if (!max_adults) {
          const adultsText = extractFirstParagraphText(serviceBlock)?.match(/\d+\s*(?:adult|adults|nguoi lon|nguoi)/gi)?.[0] || "0";
          max_adults = parseInt(adultsText) || null;
        }
        
        if (!max_children) {
          const childrenText = extractFirstParagraphText(serviceBlock)?.match(/\d+\s*(?:child|children|tre em|tre)/gi)?.[0] || "0";
          max_children = parseInt(childrenText) || null;
        }

        console.log('Room extraction results:', {
          title,
          size,
          number_of_beds,
          max_adults,
          max_children,
          roomSpecsBlock: roomSpecsBlock ? 'found' : 'not found'
        });

        if (!title) return null;

        return {
          id: index,
          name: title,
          url: link || '#',
          image: image?.src || '',
          description,
          bookLabel: button || 'Book Now',
          price: price,
          amenities: amenityImages,
          size: size || null,
          number_of_beds: number_of_beds || null,
          max_adults: max_adults || null,
          max_children: max_children || null,
        } satisfies AllRoomItem;
      })
      .filter(Boolean) as any[], // Type assertion needed due to filter
  };
};

export const parseAllRoomsBlock = (html: string): AllRoomSectionData =>
  parseAllRoomsSection(html, "shortcode-all-rooms");
