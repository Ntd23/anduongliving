import { tr } from "@nuxt/ui/runtime/locale/index.js";

const DEV_HOST = process.env.NUXT_DEV_HOST || "127.0.0.1";
const DEV_PORT = Number(process.env.NUXT_DEV_PORT || 3000);
const API_BASE_URL =
    process.env.NUXT_API_BASE_URL || "http://anduongliving.test/api";
const API_KEY =
    process.env.NUXT_API_KEY || "Xg4liH4fHPAh8AaFkBo5OoksJ3QxkIlJ";
const PUBLIC_SITE_URL =
    process.env.NUXT_PUBLIC_SITE_URL || `http://${DEV_HOST}:${DEV_PORT}`;

const NUXT_MODULES = [
    "@pinia/nuxt",
    "@nuxt/ui",
    "@vueuse/motion/nuxt",
    "@nuxt/image",
    "@nuxtjs/i18n",
] as const;

const I18N_LOCALES = [
    { code: "vi", language: "vi-VN", name: "Tiếng Việt", file: "vi.json" },
    { code: "en", language: "en-US", name: "English", file: "en.json" },
    { code: "ja", language: "ja-JP", name: "日本語", file: "ja.json" },
] as const;

export default defineNuxtConfig({
    compatibilityDate: "2025-07-15",
    devtools: { enabled: true },
    ssr: true,
    sourcemap: {
        server: true,
        client: true,
    },
    devServer: {
        host: DEV_HOST,
        port: DEV_PORT,
    },
    css: ["~/assets/css/main.css"],
    vite: {
        define: {
            __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: true,
        },
        server: {
            allowedHosts: ["anduongliving.test"],
            hmr: {
                protocol: "ws",
                host: "anduongliving.test",
                clientPort: DEV_PORT,
            },
        },
    },
    modules: [...NUXT_MODULES],
    i18n: {
        locales: [...I18N_LOCALES],
        defaultLocale: "vi",
        strategy: "prefix_except_default",
        langDir: "locales",
        detectBrowserLanguage: false,
    },

    imports: {
        dirs: ["composables/**", "stores"],
    },

    runtimeConfig: {
        apiBaseUrl: API_BASE_URL,
        apiKey: API_KEY,
        public: {
            apiBaseUrl: API_BASE_URL,
            siteUrl: PUBLIC_SITE_URL,
        },
    },
});
