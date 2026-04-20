<script setup lang="ts">
import type { ThemeOptionsData } from "~/composables/useThemeOptions";
import SidebarRawHtmlWidget from "~/components/sidebar-widgets/SidebarRawHtmlWidget.vue";
import SidebarWidgetCard from "~/components/sidebar-widgets/SidebarWidgetCard.vue";
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";

const props = defineProps<{
  widget: SidebarWidgetManifest;
  variant?: "sidebar" | "footer";
  themeOptions?: ThemeOptionsData | null;
}>();

type ContactItem = {
  key: "phone" | "email" | "address";
  label: string;
  value: string;
  href?: string;
};

const normalizeText = (value: unknown) => {
  if (typeof value !== "string") {
    return "";
  }

  return value.trim();
};

const title = computed(() => normalizeText(props.widget.data?.name) || null);
const variant = computed(() => props.variant || "sidebar");

const phoneNumber = computed(() => normalizeText(props.widget.data?.phone_number));
const email = computed(() => normalizeText(props.widget.data?.email));
const address = computed(() => normalizeText(props.widget.data?.address));

const contactItems = computed<ContactItem[]>(() => {
  const items: ContactItem[] = [];

  if (phoneNumber.value) {
    items.push({
      key: "phone",
      label: "Phone",
      value: phoneNumber.value,
      href: `tel:${phoneNumber.value.replace(/[^\d+]/g, "")}`,
    });
  }

  if (email.value) {
    items.push({
      key: "email",
      label: "Email",
      value: email.value,
      href: `mailto:${email.value}`,
    });
  }

  if (address.value) {
    items.push({
      key: "address",
      label: "Address",
      value: address.value,
    });
  }

  return items;
});

const fallbackToHtml = computed(() => !contactItems.value.length);

const brandLogoUrl = computed(() => normalizeText(props.themeOptions?.logo_url) || null);
const brandName = computed(() => normalizeText(props.themeOptions?.site_title) || title.value || "An Duong Living");
</script>

<template>
  <SidebarRawHtmlWidget v-if="fallbackToHtml" :widget="widget" />

  <SidebarWidgetCard v-else-if="variant === 'sidebar'" :title="title">
    <ul class="sidebar-contact-widget__list">
      <li
        v-for="item in contactItems"
        :key="item.key"
        class="sidebar-contact-widget__item"
      >
        <span class="sidebar-contact-widget__label">
          {{ item.label }}
        </span>

        <a
          v-if="item.href"
          :href="item.href"
          class="sidebar-contact-widget__value sidebar-contact-widget__value--link"
        >
          {{ item.value }}
        </a>

        <span v-else class="sidebar-contact-widget__value">
          {{ item.value }}
        </span>
      </li>
    </ul>
  </SidebarWidgetCard>

  <article v-else class="footer-contact-widget">
    <div class="footer-contact-widget__brand">
      <img
        v-if="brandLogoUrl"
        :src="brandLogoUrl"
        :alt="brandName"
        class="footer-contact-widget__logo"
      >

      <div v-else class="footer-contact-widget__brand-text">
        {{ brandName }}
      </div>
    </div>

    <ul class="footer-contact-widget__list">
      <li
        v-for="item in contactItems"
        :key="item.key"
        class="footer-contact-widget__item"
      >
        <span class="footer-contact-widget__label">
          {{ item.label }}
        </span>

        <a
          v-if="item.href"
          :href="item.href"
          class="footer-contact-widget__value footer-contact-widget__value--link"
        >
          {{ item.value }}
        </a>

        <span v-else class="footer-contact-widget__value">
          {{ item.value }}
        </span>
      </li>
    </ul>
  </article>
</template>

<style scoped>
.sidebar-contact-widget__list,
.footer-contact-widget__list {
  margin: 0;
  padding: 0;
  list-style: none;
}

.sidebar-contact-widget__item + .sidebar-contact-widget__item,
.footer-contact-widget__item + .footer-contact-widget__item {
  margin-top: 0.9rem;
}

.sidebar-contact-widget__label,
.footer-contact-widget__label {
  display: inline-block;
  margin-bottom: 0.2rem;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.sidebar-contact-widget__label {
  color: #8d7355;
}

.sidebar-contact-widget__value,
.footer-contact-widget__value {
  display: block;
  white-space: pre-line;
  line-height: 1.7;
}

.sidebar-contact-widget__value {
  color: #3f352d;
}

.sidebar-contact-widget__value--link {
  text-decoration: none;
}

.sidebar-contact-widget__value--link:hover {
  color: #7c5c3b;
}

.footer-contact-widget__brand {
  margin-bottom: 1.4rem;
}

.footer-contact-widget__logo {
  display: block;
  max-width: 220px;
  width: 100%;
  height: auto;
}

.footer-contact-widget__brand-text {
  color: #fff3dc;
  font-family: "Cormorant Garamond", "Times New Roman", Georgia, serif;
  font-size: 1.75rem;
  line-height: 1.1;
}

.footer-contact-widget__label {
  color: rgba(216, 181, 107, 0.92);
}

.footer-contact-widget__value {
  color: rgba(245, 234, 214, 0.78);
}

.footer-contact-widget__value--link {
  color: rgba(255, 243, 220, 0.86);
  text-decoration: none;
  transition: color 0.2s ease;
}

.footer-contact-widget__value--link:hover {
  color: #d8b56b;
}
</style>
