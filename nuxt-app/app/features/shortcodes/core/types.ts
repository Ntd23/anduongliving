export type ShortcodeName =
  | "all-rooms"
  | "about-us"
  | "blog-posts"
  | "booking-form"
  | "brands"
  | "check-availability-form"
  | "cuisine-showcase"
  | "feature-area"
  | "featured-amenities"
  | "featured-rooms"
  | "faqs"
  | "forest-facility-showcase"
  | "galleries"
  | "hero-story"
  | "hero-banner-with-booking-form"
  | "hotel-places"
  | "hotel-services"
  | "intro-video"
  | "location-tourism-showcase"
  | "logo-showcase-banner"
  | "news"
  | "newsletter"
  | "onsen-spa-gallery"
  | "pickup-gallery-showcase"
  | "pricing"
  | "room-mosaic-showcase"
  | "room-list"
  | "service-list"
  | "services"
  | "simple-slider"
  | "spa-collage-showcase"
  | "special-story-showcase"
  | "teams"
  | "testimonials"
  | "user-profile"
  | "why-choose-us";

export type ShortcodeBlock = {
  type: "shortcode" | "html" | "text";
  raw: string;
  name?: ShortcodeName | null;
};

export type ShortcodeComponentName =
  | "AllRooms"
  | "AboutUs"
  | "BlogPosts"
  | "Brands"
  | "BookingForm"
  | "CheckAvailabilityForm"
  | "CuisineShowcase"
  | "FeatureArea"
  | "Faqs"
  | "FeaturedRooms"
  | "FeaturedAmenities"
  | "ForestFacilityShowcase"
  | "Galleries"
  | "HeroStory"
  | "HeroBannerWithBookingForm"
  | "HotelPlaces"
  | "HotelServices"
  | "IntroVideo"
  | "LocationTourismShowcase"
  | "LogoShowcaseBanner"
  | "News"
  | "Newsletter"
  | "OnsenSpaGallery"
  | "PickupGalleryShowcase"
  | "Pricing"
  | "RoomList"
  | "RoomMosaicShowcase"
  | "Section"
  | "ServiceList"
  | "Services"
  | "SimpleSlider"
  | "SpaCollageShowcase"
  | "SpecialStoryShowcase"
  | "Teams"
  | "Testimonials"
  | "UserProfile"
  | "WhyChooseUs";

export type ShortcodeImage = {
  src: string;
  alt: string;
};

export type ServiceItem = {
  id: number;
  name: string;
  url: string;
  image: string;
  description?: string | null;
  bookLabel: string;
  price: string;
  amenities: string[];
};

export type AllRoomItem = {
  id: number;
  name: string;
  url: string;
  image: string | null;
  images: string[] | null;
  vr_image: string | null;
  vr_images: string[] | null;
  vr_hotspots?: Array<{
    id: number;
    name: string;
    image: string;
    yaw: number;
    pitch: number;
  }> | null;
  description?: string | null;
  bookLabel: string;
  price: string;
  amenities: string[];
  size?: number | null;
  number_of_beds?: number | null;
  max_adults?: number | null;
  max_children?: number | null;
};

export type GoogleMapItem = {
  address: string;
  iframeSrc: string;
  height: string;
  width: string;
  title: string;
};

export type AllRoomSectionData = {
  countLabel?: string | null;
  paginationHtml?: string | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  items: AllRoomItem[];
};

export type FeaturedRoomsSectionData = {
  countLabel?: string | null;
  paginationHtml?: string | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  items: ServiceItem[];
};

export type TestimonialItem = {
  name: string;
  title: string | null;
  content: string | null;
  image: ShortcodeImage | null;
  rating: number | null;
};

export type TestimonialsSectionData = {
  subtitle: string | null;
  title: string | null;
  description: string | null;
  backgroundImage: ShortcodeImage | null;
  testimonialIds: string[];
  items: TestimonialItem[];
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

export type TeamsSectionData = {
  title: string | null;
  subtitle: string | null;
  description: string | null;
  members: TeamMember[];
};

export type AboutUsSectionData = {
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

export type WhyChooseUsSectionData = {
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

export type FeatureAreaSectionData = {
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
  description: string | null | undefined;
  items: PricingItem[];
};
export type ServiceDetailItem = {
  title: string;
  iconImgUrl: string;
  iconImgAlt: string;
  thumbImgUrl: string;
  thumbImgAlt: string;
};

export type ShortcodeAction = {
  href: string;
  label: string;
};

export type SimpleSliderSlide = {
  image: ShortcodeImage | null;
  caption: string | null;
  captionHtml: string | null;
  href: string | null;
};

export type SimpleSliderSectionData = {
  slides: SimpleSliderSlide[];
};

export type LogoShowcaseBannerSectionData = {
  backgroundImage: ShortcodeImage | null;
  logoImage: ShortcodeImage | null;
  topLines: string[];
  bottomLines: string[];
};

export type RoomMosaicShowcaseCard = {
  title: string | null;
  image: ShortcodeImage | null;
};

export type RoomMosaicShowcaseSectionData = {
  backgroundImage: ShortcodeImage | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  mainImage: ShortcodeImage | null;
  cards: RoomMosaicShowcaseCard[];
  sideTextLines: string[];
  roomItems: string[];
  ctaTitle: string | null;
  action: ShortcodeAction | null;
};

export type OnsenSpaGalleryItem = {
  title: string | null;
  image: ShortcodeImage | null;
};

export type OnsenSpaGallerySectionData = {
  backgroundImage: ShortcodeImage | null;
  backgroundTitle: string | null;
  subtitle: string | null;
  items: OnsenSpaGalleryItem[];
  sectionLabel: string | null;
  actions: ShortcodeAction[];
};

export type PickupGalleryShowcaseSectionData = {
  backgroundImage: ShortcodeImage | null;
  pretitle: string | null;
  title: string | null;
  galleryImages: ShortcodeImage[];
  description: string | null;
  sectionLabel: string | null;
  action: ShortcodeAction | null;
};

export type SpecialStoryShowcaseCard = {
  title: string | null;
  image: ShortcodeImage | null;
};

export type SpecialStoryShowcaseNavEntry = {
  text: string | null;
  badgeTop: string | null;
  badgeVolume: string | null;
};

export type SpecialStoryShowcaseSectionData = {
  backgroundImage: ShortcodeImage | null;
  decorativeText: string | null;
  sectionLabel: string | null;
  description: string | null;
  cards: SpecialStoryShowcaseCard[];
  navEntries: SpecialStoryShowcaseNavEntry[];
  action: ShortcodeAction | null;
};

export type ForestFacilityShowcaseSectionData = {
  leftImage: ShortcodeImage | null;
  rightImages: ShortcodeImage[];
  title: string | null;
  description: string | null;
  sectionLabel: string | null;
  action: ShortcodeAction | null;
};

export type LocationTourismShowcaseColumn = {
  description: string | null;
  label: string | null;
  action: ShortcodeAction | null;
};

export type LocationTourismShowcaseSectionData = {
  backgroundImage: ShortcodeImage | null;
  mapImage: ShortcodeImage | null;
  gridImages: ShortcodeImage[];
  decorativeText: string | null;
  title: string | null;
  access: LocationTourismShowcaseColumn;
  tourism: LocationTourismShowcaseColumn;
};

export type CuisineShowcaseSectionData = {
  title: string | null;
  description: string | null;
  images: ShortcodeImage[];
  sectionLabel: string | null;
  action: ShortcodeAction | null;
  buttonLabel: string | null;
  buttonUrl: string | null;
};

export type SpaCollageShowcaseSectionData = {
  leftPanelImage: ShortcodeImage | null;
  rightPanelImage: ShortcodeImage | null;
  bottomImages: ShortcodeImage[];
  title: string | null;
  subtitle: string | null;
  description: string | null;
};

export type AvailabilityBookingFormSectionData = {
  title: string | null;
  actionUrl: string | null;
  dateFormat: string | null;
  submitLabel: string | null;
  startDate: string | null;
  endDate: string | null;
  minAdults: number | null;
  maxAdults: number | null;
  defaultAdults: number | null;
  defaultChildren: number | null;
  defaultRooms: number | null;
};

export type HeroBannerWithBookingFormSectionData = {
  backgroundColor: string | null;
  backgroundImage: ShortcodeImage | null;
  title: string | null;
  description: string | null;
  action: ShortcodeAction | null;
  bookingForm: AvailabilityBookingFormSectionData | null;
};

export type CheckAvailabilityFormSectionData = {
  bookingForm: AvailabilityBookingFormSectionData | null;
};

export type BookingSelectOption = {
  value: string;
  label: string;
  selected: boolean;
};

export type BookingFormSectionData = {
  shapeImage: ShortcodeImage | null;
  image: ShortcodeImage | null;
  subtitle: string | null;
  title: string | null;
  actionUrl: string | null;
  method: string;
  csrfToken: string | null;
  dateFormat: string | null;
  startDate: string | null;
  endDate: string | null;
  submitLabel: string | null;
  roomOptions: BookingSelectOption[];
  adultOptions: BookingSelectOption[];
};

export type FeatureGridItem = {
  title: string;
  description: string | null;
  image: ShortcodeImage | null;
  href: string | null;
  actionLabel: string | null;
  price: string | null;
  meta: string | null;
};

export type FeaturedAmenitiesSectionData = {
  backgroundColor: string | null;
  backgroundImage: ShortcodeImage | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  items: FeatureGridItem[];
};

export type ServiceListSectionData = {
  backgroundImage: ShortcodeImage | null;
  items: FeatureGridItem[];
};

export type HotelPlacesSectionData = {
  subtitle: string | null;
  title: string | null;
  description: string | null;
  items: FeatureGridItem[];
};

export type HotelServicesSectionData = {
  subtitle: string | null;
  title: string | null;
  description: string | null;
  items: FeatureGridItem[];
};

export type NewsSectionData = {
  backgroundImage: ShortcodeImage | null;
  subtitle: string | null;
  title: string | null;
  description: string | null;
  items: FeatureGridItem[];
};

export type BlogPostsSectionData = {
  items: FeatureGridItem[];
  paginationHtml: string | null;
};

export type GalleryFilter = {
  label: string;
  value: string;
};

export type GalleryItem = {
  title: string | null;
  image: ShortcodeImage | null;
  href: string | null;
  filter: string | null;
};

export type GalleriesSectionData = {
  filters: GalleryFilter[];
  items: GalleryItem[];
};

export type FaqItem = {
  id: string;
  question: string;
  answer: string | null;
};

export type FaqsSectionData = {
  items: FaqItem[];
};

export type BrandItem = {
  name: string | null;
  image: ShortcodeImage | null;
  href: string | null;
};

export type BrandsSectionData = {
  backgroundColor: string | null;
  items: BrandItem[];
};

export type IntroVideoSectionData = {
  backgroundImage: ShortcodeImage | null;
  buttonIcon: ShortcodeImage | null;
  videoUrl: string | null;
  title: string | null;
};

export type UserProfileMetric = {
  title: string;
  percentage: number;
};

export type UserProfileSectionData = {
  metrics: UserProfileMetric[];
  images: ShortcodeImage[];
};

export type ShortcodeDefinition = {
  name: ShortcodeName;
  aliases: readonly string[];
  componentName: ShortcodeComponentName;
  parser: (html: string) => unknown;
};
export type BookingRoomOption = {
  value: string | undefined;
  label: string | undefined;
};

export type BookingSectionData = {
  subtitle: string | null;
  title: string | null;
  description:string | null ;
  actionUrl: string | null | undefined;
  imageSrc: string | null;
  imageAlt: string | null;
  rooms: BookingRoomOption[] | null;
};



