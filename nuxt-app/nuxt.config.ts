import { tr } from "@nuxt/ui/runtime/locale/index.js";
import { buildDevOrigin, resolveBackendSiteUrl, resolveCmsProxyBaseUrl } from "./shared/cms-routing";

const DEV_HOST = process.env.NUXT_DEV_HOST || "127.0.0.1";
const DEV_PORT = Number(process.env.NUXT_DEV_PORT || 3000);
const DEV_ORIGIN = process.env.NUXT_DEV_ORIGIN || buildDevOrigin(DEV_HOST, DEV_PORT);
const HMR_HOST = process.env.NUXT_HMR_HOST || DEV_HOST;
const API_BASE_URL = process.env.NUXT_API_BASE_URL || "http://127.0.0.1/api";
const API_KEY = process.env.NUXT_API_KEY || "";
const PUBLIC_SITE_URL =
    process.env.NUXT_PUBLIC_SITE_URL || `http://${DEV_HOST}:${DEV_PORT}`;
const BACKEND_SITE_URL = resolveBackendSiteUrl({
    explicitBaseUrl: process.env.NUXT_BACKEND_SITE_URL,
    apiBaseUrl: API_BASE_URL,
    publicSiteUrl: PUBLIC_SITE_URL,
    isDevelopment: process.env.NODE_ENV !== "production",
});
const ALLOWED_HOSTS = (process.env.NUXT_ALLOWED_HOSTS || "")
    .split(",")
    .map((item) => item.trim())
    .filter(Boolean);
const CMS_PROXY_BASE_URL = resolveCmsProxyBaseUrl({
    explicitBaseUrl: process.env.NUXT_PUBLIC_CMS_PROXY_BASE_URL,
    isDevelopment: process.env.NODE_ENV !== "production",
    devHost: DEV_HOST,
    devPort: DEV_PORT,
});

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
    devtools: { enabled: false },
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
            origin: DEV_ORIGIN,
            allowedHosts: ALLOWED_HOSTS.length ? ALLOWED_HOSTS : true,
            cors: {
                origin: true,
                credentials: true,
            },
            hmr: {
                protocol: "ws",
                host: HMR_HOST,
                clientPort: DEV_PORT,
            },
        },
    },
    routeRules: {
        "/api/cms/**": {
            cors: true,
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
        backendSiteUrl: BACKEND_SITE_URL,
        public: {
            apiBaseUrl: API_BASE_URL,
            siteUrl: PUBLIC_SITE_URL,
            cmsProxyBaseUrl: CMS_PROXY_BASE_URL,
        },
    },
});
