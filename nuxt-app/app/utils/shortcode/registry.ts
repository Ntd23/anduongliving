import {
  parseAboutBlock,
  parseFeatureBlock,
  parseHeroStoryBlock,
  parseNewsletterBlock,
  parsePricingBlock,
  parseServiceBlock,
  parseSkillBlock,
  parseTeamBlock,
  parseTestimonialsBlock,
} from "./parsers";
import type { ShortcodeDefinition, ShortcodeName } from "./types";

export const shortcodeRegistry = [
  {
    name: "hero-story",
    aliases: ["hero-story", "story-area", "our-story-area"],
    componentName: "HeroStory",
    parser: parseHeroStoryBlock,
  },
  {
    name: "about",
    aliases: ["about-area"],
    componentName: "About",
    parser: parseAboutBlock,
  },
    {
    name: "services",
    aliases: ["services-area"],
    componentName: "Services",
    parser: parseServiceBlock,
  },
  {
    name: "skill",
    aliases: ["skill-area"],
    componentName: "Skill",
    parser: parseSkillBlock,
  },
  {
    name: "feature",
    aliases: ["feature-area2"],
    componentName: "Feature",
    parser: parseFeatureBlock,
  },
  {
    name: "newsletter",
    aliases: ["newslater-area"],
    componentName: "Newsletter",
    parser: parseNewsletterBlock,
  },
  {
    name: "pricing",
    aliases: ["pricing-area"],
    componentName: "Pricing",
    parser: parsePricingBlock,
  },
  {
    name: "team",
    aliases: ["team-area", "team-area2"],
    componentName: "Team",
    parser: parseTeamBlock,
  },
  {
    name: "testimonials",
    aliases: [
      "testimonials",
      "testimonials-area",
      "testimonial-area",
      "testimonial",
      "testimonial-slider",
      "testimonials-slider",
    ],
    componentName: "Testimonials",
    parser: parseTestimonialsBlock,
  },
] as const satisfies readonly ShortcodeDefinition[];

export const findShortcodeDefinition = (name?: string | null): ShortcodeDefinition | null => {
  if (!name) {
    return null;
  }

  return shortcodeRegistry.find((definition) => definition.name === name) || null;
};

export const detectShortcodeName = (html: string): ShortcodeName | null => {
  const lowerHtml = html.toLowerCase();

  for (const definition of shortcodeRegistry) {
    if (definition.aliases.some((alias) => lowerHtml.includes(alias.toLowerCase()))) {
      return definition.name;
    }
  }

  return null;
};
