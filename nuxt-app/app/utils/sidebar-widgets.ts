import type { Component } from "vue";
import BlogCategoriesWidget from "~/components/sidebar-widgets/BlogCategoriesWidget.vue";
import BlogPostsWidget from "~/components/sidebar-widgets/BlogPostsWidget.vue";
import BlogSearchWidget from "~/components/sidebar-widgets/BlogSearchWidget.vue";
import BlogSocialsWidget from "~/components/sidebar-widgets/BlogSocialsWidget.vue";
import BlogTagsWidget from "~/components/sidebar-widgets/BlogTagsWidget.vue";
import CheckAvailabilityWidget from "~/components/sidebar-widgets/CheckAvailabilityWidget.vue";
import ContactInformationWidget from "~/components/sidebar-widgets/ContactInformationWidget.vue";
import CoreSimpleMenuWidget from "~/components/sidebar-widgets/CoreSimpleMenuWidget.vue";
import CustomMenuWidget from "~/components/sidebar-widgets/CustomMenuWidget.vue";
import NewsletterWidget from "~/components/sidebar-widgets/NewsletterWidget.vue";
import RoomContactWidget from "~/components/sidebar-widgets/RoomContactWidget.vue";
import SidebarRawHtmlWidget from "~/components/sidebar-widgets/SidebarRawHtmlWidget.vue";
import TextWidget from "~/components/sidebar-widgets/TextWidget.vue";

export type SidebarMenuNode = {
  id: number;
  menu_id?: number | null;
  parent_id?: number | null;
  title: string;
  url: string;
  icon?: string | null;
  target?: string | null;
  position?: number | null;
  children?: SidebarMenuNode[];
};

export type SidebarMenuData = {
  id: number;
  name: string;
  slug: string;
  location?: string | null;
  nodes: SidebarMenuNode[];
};

export type SidebarWidgetManifest = {
  id: number;
  widget_id: string;
  sidebar_id: string;
  position: number;
  data: Record<string, any>;
  lang?: string | null;
  rendered_html?: string | null;
  meta?: Record<string, any> | null;
  menu_data?: SidebarMenuData | null;
};

export const SIDEBAR_WIDGET_REGISTRY: Record<string, Component> = {
  BlogSearchWidget,
  BlogPostsWidget,
  BlogCategoriesWidget,
  BlogTagsWidget,
  BlogSocialsWidget,
  CustomMenuWidget,
  ContactInformationMenuWidget: ContactInformationWidget,
  CoreSimpleMenu: CoreSimpleMenuWidget,
  NewsletterWidget,
  CheckAvailabilityForm: CheckAvailabilityWidget,
  RoomContactWidget,
  Text: TextWidget,
};

export const resolveSidebarWidgetComponent = (widgetId: string): Component =>
  SIDEBAR_WIDGET_REGISTRY[widgetId] || SidebarRawHtmlWidget;

export const isNativeSidebarWidget = (widgetId: string) => Boolean(SIDEBAR_WIDGET_REGISTRY[widgetId]);
