<script setup lang="ts">
const socials = ['facebook', 'instagram', 'twitter']

const stayLinks = [
  'Villas',
  'Suites',
  'Wellness Packages'
]

const experienceLinks = [
  'Spa & Wellness',
  'Yoga Retreats',
  'Culinary Journey'
]
</script>

<template>
  <footer class="bg-gray-900 text-white py-16 px-4">
    <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-12">

      <!-- Brand -->
      <div>
        <h3 class="text-2xl font-bold mb-6 bg-gradient-to-r from-green-400 to-blue-500 bg-clip-text text-transparent">
          Avana Retreat
        </h3>

        <p class="text-gray-400 mb-6">
          Where luxury meets nature. Transform your life in paradise.
        </p>

        <div class="flex space-x-4">
          <a
            v-for="social in socials"
            :key="social"
            href="#"
            class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-all duration-300"
          >
            <span class="sr-only">{{ social }}</span>
          </a>
        </div>
      </div>

      <!-- Stay -->
      <div>
        <h4 class="text-lg font-semibold mb-6">Stay With Us</h4>
        <ul class="space-y-3 text-gray-400">
          <li v-for="item in stayLinks" :key="item">
            <NuxtLink to="#" class="hover:text-white transition-colors">
              {{ item }}
            </NuxtLink>
          </li>
        </ul>
      </div>

      <!-- Experiences -->
      <div>
        <h4 class="text-lg font-semibold mb-6">Experiences</h4>
        <ul class="space-y-3 text-gray-400">
          <li v-for="item in experienceLinks" :key="item">
            <NuxtLink to="#" class="hover:text-white transition-colors">
              {{ item }}
            </NuxtLink>
          </li>
        </ul>
      </div>

      <!-- Contact -->
      <div>
        <h4 class="text-lg font-semibold mb-6">Contact</h4>
        <div class="space-y-3 text-gray-400">
          <p>+84 123 456 789</p>
          <p>hello@avanaretreat.com</p>
          <p>Phu Quoc Island, Vietnam</p>
        </div>
      </div>

    </div>

    <!-- Bottom -->
    <div class="border-t border-white/10 mt-16 pt-12 text-center text-gray-500 text-sm">
      <p>&copy; 2024 Avana Retreat. All rights reserved.</p>
    </div>
  </footer>
</template>