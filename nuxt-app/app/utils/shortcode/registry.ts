import {
  parseAboutUsBlock,
  parseBrandsBlock,
  parseBookingFormBlock,
  parseCheckAvailabilityFormBlock,
  parseCuisineShowcaseBlock,
  parseFaqsBlock,
  parseFeatureAreaBlock,
  parseFeaturedAmenitiesBlock,
  parseFeaturedRoomsBlock,
  parseForestFacilityShowcaseBlock,
  parseGalleriesBlock,
  parseHeroStoryBlock,
  parseHeroBannerWithBookingFormBlock,
  parseHotelPlacesBlock,
  parseHotelServicesBlock,
  parseIntroVideoBlock,
  parseLocationTourismShowcaseBlock,
  parseLogoShowcaseBannerBlock,
  parseNewsBlock,
  parseNewsletterBlock,
  parseOnsenSpaGalleryBlock,
  parsePickupGalleryShowcaseBlock,
  parsePricingBlock,
  parseRoomListBlock,
  parseRoomMosaicShowcaseBlock,
  parseServiceListBlock,
  parseServicesBlock,
  parseSimpleSliderBlock,
  parseSpaCollageShowcaseBlock,
  parseSpecialStoryShowcaseBlock,
  parseTeamsBlock,
  parseTestimonialsBlock,
  parseUserProfileBlock,
  parseWhyChooseUsBlock,
  parseAllRoomsBlock,
} from "./parsers";
import type { ShortcodeDefinition, ShortcodeName } from "./types";

export const shortcodeRegistry = [
  {
    name: "hero-banner-with-booking-form",
    aliases: ["shortcode-hero-banner-with-booking-form"],
    componentName: "HeroBannerWithBookingForm",
    parser: parseHeroBannerWithBookingFormBlock,
  },
  {
    name: "simple-slider",
    aliases: ["shortcode-simple-slider", "slider-showcase-area"],
    componentName: "SimpleSlider",
    parser: parseSimpleSliderBlock,
  },
  {
    name: "hero-story",
    aliases: ["hero-story", "story-area", "our-story-area"],
    componentName: "HeroStory",
    parser: parseHeroStoryBlock,
  },
  {
    name: "about-us",
    aliases: ["about-area"],
    componentName: "AboutUs",
    parser: parseAboutUsBlock,
  },
  {
    name: "check-availability-form",
    aliases: ["shortcode-check-availability-form"],
    componentName: "CheckAvailabilityForm",
    parser: parseCheckAvailabilityFormBlock,
  },
  {
    name: "booking-form",
    aliases: ["shortcode-booking-form"],
    componentName: "BookingForm",
    parser: parseBookingFormBlock,
  },
  {
    name: "featured-rooms",
    aliases: ["shortcode-featured-rooms"],
    componentName: "FeaturedRooms",
    parser: parseFeaturedRoomsBlock,
  },
  {
    name: "room-list",
    aliases: ["shortcode-room-list"],
    componentName: "RoomList",
    parser: parseRoomListBlock,
  },
  {
    name: "all-rooms",
    aliases: ["shortcode-all-rooms"],
    componentName: "AllRooms",
    parser: parseAllRoomsBlock,
  },
  {
    name: "service-list",
    aliases: ["shortcode-service-list"],
    componentName: "ServiceList",
    parser: parseServiceListBlock,
  },
  {
    name: "featured-amenities",
    aliases: ["shortcode-featured-amenities"],
    componentName: "FeaturedAmenities",
    parser: parseFeaturedAmenitiesBlock,
  },
  {
    name: "why-choose-us",
    aliases: ["skill-area"],
    componentName: "WhyChooseUs",
    parser: parseWhyChooseUsBlock,
  },
  {
    name: "services",
    aliases: ["shortcode-services"],
    componentName: "Services",
    parser: parseServicesBlock,
  },
  {
    name: "feature-area",
    aliases: ["shortcode-feature-area"],
    componentName: "FeatureArea",
    parser: parseFeatureAreaBlock,
  },
  {
    name: "newsletter",
    aliases: ["newslater-area"],
    componentName: "Newsletter",
    parser: parseNewsletterBlock,
  },
  {
    name: "brands",
    aliases: ["shortcode-brands", "brand-area"],
    componentName: "Brands",
    parser: parseBrandsBlock,
  },
  {
    name: "pricing",
    aliases: ["pricing-area"],
    componentName: "Pricing",
    parser: parsePricingBlock,
  },
  {
    name: "intro-video",
    aliases: ["shortcode-intro-video"],
    componentName: "IntroVideo",
    parser: parseIntroVideoBlock,
  },
  {
    name: "hotel-places",
    aliases: ["shortcode-hotel-places", "hotel-place-area"],
    componentName: "HotelPlaces",
    parser: parseHotelPlacesBlock,
  },
  {
    name: "hotel-services",
    aliases: ["shortcode-hotel-services", "hotel-service-area"],
    componentName: "HotelServices",
    parser: parseHotelServicesBlock,
  },
  {
    name: "news",
    aliases: ["shortcode-news"],
    componentName: "News",
    parser: parseNewsBlock,
  },
  {
    name: "teams",
    aliases: ["team-area", "team-area2"],
    componentName: "Teams",
    parser: parseTeamsBlock,
  },
  {
    name: "testimonials",
    aliases: ["shortcode-testimonials", "testimonial-area"],
    componentName: "Testimonials",
    parser: parseTestimonialsBlock,
  },
  {
    name: "faqs",
    aliases: ["shortcode-faqs", "faq-area"],
    componentName: "Faqs",
    parser: parseFaqsBlock,
  },
  {
    name: "galleries",
    aliases: ["shortcode-galleries"],
    componentName: "Galleries",
    parser: parseGalleriesBlock,
  },
  {
    name: "logo-showcase-banner",
    aliases: ["logo-showcase-banner"],
    componentName: "LogoShowcaseBanner",
    parser: parseLogoShowcaseBannerBlock,
  },
  {
    name: "room-mosaic-showcase",
    aliases: ["room-mosaic-showcase"],
    componentName: "RoomMosaicShowcase",
    parser: parseRoomMosaicShowcaseBlock,
  },
  {
    name: "spa-collage-showcase",
    aliases: ["spa-collage-showcase"],
    componentName: "SpaCollageShowcase",
    parser: parseSpaCollageShowcaseBlock,
  },
  {
    name: "onsen-spa-gallery",
    aliases: ["onsen-spa-gallery"],
    componentName: "OnsenSpaGallery",
    parser: parseOnsenSpaGalleryBlock,
  },
  {
    name: "pickup-gallery-showcase",
    aliases: ["shortcode-pickup-gallery-showcase"],
    componentName: "PickupGalleryShowcase",
    parser: parsePickupGalleryShowcaseBlock,
  },
  {
    name: "forest-facility-showcase",
    aliases: ["shortcode-forest-facility-showcase"],
    componentName: "ForestFacilityShowcase",
    parser: parseForestFacilityShowcaseBlock,
  },
  {
    name: "cuisine-showcase",
    aliases: ["shortcode-cuisine-showcase"],
    componentName: "CuisineShowcase",
    parser: parseCuisineShowcaseBlock,
  },
  {
    name: "special-story-showcase",
    aliases: ["shortcode-special-story-showcase"],
    componentName: "SpecialStoryShowcase",
    parser: parseSpecialStoryShowcaseBlock,
  },
  {
    name: "location-tourism-showcase",
    aliases: ["shortcode-location-tourism-showcase"],
    componentName: "LocationTourismShowcase",
    parser: parseLocationTourismShowcaseBlock,
  },
  {
    name: "user-profile",
    aliases: ["shortcode-user-profile"],
    componentName: "UserProfile",
    parser: parseUserProfileBlock,
  },
  {
  name: "booking",
  aliases: ["booking", "form-booking"],
  componentName: "Booking",
  parser: parseBookingBlock,
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
