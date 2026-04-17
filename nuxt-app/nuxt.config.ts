import { defineNuxtConfig } from 'nuxt/config'

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',

  devtools: {
    enabled: true,
  },

  devServer: {
    host: '0.0.0.0',
    port: 3000,
  },

  vite: {
    server: {
      allowedHosts: ['anduongliving.test'],
    },
  },
})