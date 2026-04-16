<script setup lang="ts">
import { useIntersectionObserver } from '@/composables/useIntersectionObserver'

const { target: sectionRef, isIntersecting } = useIntersectionObserver({
  threshold: 0.1,
  triggerOnce: false,
})
</script>

<template>
  <section
    ref="sectionRef"
    class="py-16 px-4 bg-gradient-to-b from-white to-gray-50"
  >
    <div class="max-w-7xl mx-auto">
      <div class="text-center mb-6">

        <!-- Title -->
        <h2
          :class="[
            'text-5xl md:text-6xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-6 transition-all duration-1000',
            isIntersecting
              ? 'opacity-100 translate-y-0'
              : 'opacity-0 translate-y-8'
          ]"
        >
          Welcome
        </h2>

        <!-- Description -->
        <p
          :class="[
            'text-xl text-gray-600 max-w-4xl mx-auto transition-all duration-1000 delay-300',
            isIntersecting
              ? 'opacity-100 translate-y-0'
              : 'opacity-0 translate-y-8'
          ]"
        >
          Welcome to Avana Retreat where remarkable landscapes meet tribal heritages.
          Located among three ethnic groups deep in a tropical forest, Avana represents
          the inseparable connection between nature and people.
        </p>

      </div>
    </div>
  </section>
</template>