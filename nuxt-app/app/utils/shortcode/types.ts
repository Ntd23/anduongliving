export type ShortcodeName =
  | "about"
  | "feature"
  | "hero-story"
  | "newsletter"
  | "pricing"
  | "skill"
  | "team"
  | "services";

export type ShortcodeBlock = {
  type: "shortcode" | "html" | "text";
  raw: string;
  name?: ShortcodeName | null;
};

export type ShortcodeComponentName =
  | "About"
  | "Feature"
  | "HeroStory"
  | "Newsletter"
  | "Pricing"
  | "Skill"
  | "Team"
  | "Services";

export type ShortcodeImage = {
  src: string;
  alt: string;
};

export type ServiceItem = {
  id: number;
  name: string;
  url: string;
  image: string;
  bookLabel: string;
  price: string;
  amenities: string[];
};

export type ServiceSectionData = {
  subtitle: string | null;
  title: string | null;
  description: string | null;
  items: ServiceItem[];
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

export type AboutSectionData = {
  backgroundImage: ShortcodeImage | null;
  mainImage: ShortcodeImage | null;
  accentImage: ShortcodeImage | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  bullets: string[];
  action: {
    href: string;
    label: string;
  } | null;
  signatureImage: ShortcodeImage | null;
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

export type HeroStorySectionData = {
  backgroundImage: ShortcodeImage | null;
  foregroundImage: ShortcodeImage | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  action: {
    href: string;
    label: string;
  } | null;
};

export type FeatureSectionData = {
  backgroundColor: string | null;
  backgroundImage: ShortcodeImage | null;
  image: ShortcodeImage | null;
  secondaryImage: ShortcodeImage | null;
  quote: string | null;
  quoteAuthor: string | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  action: {
    href: string;
    label: string;
  } | null;
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

export type PricingItem = {
  title: string;
  description: string | null;
  price: string;
  currency: string;
  period: string;
  features: string[];
  buttonLabel: string;
  buttonUrl: string;
  isPopular: boolean;
};

export type PricingSectionData = {
  backgroundColor: string | null;
  backgroundImage1: ShortcodeImage | null;
  backgroundImage2: ShortcodeImage | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  items: PricingItem[];
};

export type ShortcodeDefinition = {
  name: ShortcodeName;
  aliases: readonly string[];
  componentName: ShortcodeComponentName;
  parser: (html: string) => unknown;
};
