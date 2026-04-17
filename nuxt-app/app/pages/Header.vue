<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <header
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
      isScrolled
        ? 'bg-white/95 backdrop-blur-md shadow-lg'
        : 'bg-transparent'
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">

        <!-- Logo -->
        <div class="flex-shrink-0">
          <NuxtLink
            to="/"
            :class="[
              'text-3xl font-bold transition-colors',
              isScrolled ? 'text-gray-900' : 'text-white'
            ]"
          >
            AVANA
          </NuxtLink>
        </div>

        <!-- Desktop Menu -->
        <nav class="hidden md:flex space-x-8">
          <NuxtLink
            v-for="item in ['Home','About','Retreat','Gallery','Contact']"
            :key="item"
            :to="'#' + item.toLowerCase()"
            :class="[
              'font-medium transition-colors hover:text-green-500',
              isScrolled
                ? 'text-gray-900 hover:text-green-600'
                : 'text-white hover:text-green-200'
            ]"
          >
            {{ item }}
          </NuxtLink>
        </nav>

        <!-- CTA -->
        <div class="hidden md:block">
          <button class="px-6 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-colors shadow-lg">
            Book Now
          </button>
        </div>

        <!-- Mobile Button -->
        <button class="md:hidden" @click="isMobileMenuOpen = !isMobileMenuOpen">
          <svg
            class="w-6 h-6 transition-colors"
            :class="isScrolled ? 'text-gray-900' : 'text-white'"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            />
          </svg>
        </button>

      </div>

      <!-- Mobile Menu -->
      <div
        v-if="isMobileMenuOpen"
        :class="[
          'md:hidden py-4 transition-colors',
          isScrolled ? 'bg-white/95 text-gray-900' : 'bg-black/50 text-white'
        ]"
      >
        <NuxtLink
          v-for="item in ['Home','About','Retreat','Gallery','Contact']"
          :key="item"
          :to="'#' + item.toLowerCase()"
          class="block py-2 font-medium hover:text-green-500 transition-colors"
          @click="isMobileMenuOpen = false"
        >
          {{ item }}
        </NuxtLink>

        <button class="w-full mt-4 px-4 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-colors">
          Book Now
        </button>
      </div>

    </div>
  </header>
</template>