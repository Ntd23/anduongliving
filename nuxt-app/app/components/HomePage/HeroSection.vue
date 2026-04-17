<script setup lang="ts">
</script>

<template>
  <section class="relative h-screen flex items-center justify-center overflow-hidden pt-20">

    <!-- Video Background -->
    <div class="absolute inset-0 w-full h-full">
      <video
        autoplay
        muted
        loop
        playsinline
        class="w-full h-full object-cover"
        poster="https://via.placeholder.com/1920x1080?text=Avana+Retreat"
      >
        <source
          src="https://avanaretreat.com/storage/2025/04/1745225909Colors of Green 30s - 25mb.mp4"
          type="video/mp4"
        />
      </video>

      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/40"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">

      <!-- Badge -->
      <div class="animate-float mb-8">
        <span class="inline-block bg-white/20 backdrop-blur-sm px-6 py-3 rounded-full text-sm font-medium mb-6">
          🌿 Wellness Retreat in Vietnam
        </span>
      </div>

      <!-- Title -->
      <h1 class="text-6xl md:text-7xl lg:text-8xl font-bold mb-8 bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent leading-tight">
        AVANA
        <br />
        <span class="text-5xl md:text-6xl lg:text-7xl">RETREAT</span>
      </h1>

      <!-- Description -->
      <p class="text-xl md:text-2xl mb-12 max-w-2xl mx-auto leading-relaxed opacity-90">
        Escape to paradise. Rejuvenate your mind, body & soul in the heart of Vietnam's 
        most breathtaking landscapes.
      </p>

      <!-- Buttons -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
        <button class="group relative px-12 py-5 bg-white text-gray-900 font-semibold text-lg rounded-2xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden">
          <span class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
          <span class="relative z-10">Book Your Stay</span>
        </button>

        <button class="px-12 py-5 border-2 border-white/50 backdrop-blur-sm text-white font-semibold text-lg rounded-2xl hover:bg-white/20 transition-all duration-300">
          Watch Video
        </button>
      </div>

      <!-- Scroll Indicator -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <div class="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center">
          <div class="w-1 h-3 bg-white/70 rounded-full mt-2 animate-pulse"></div>
        </div>
      </div>

    </div>
  </section>
</template>