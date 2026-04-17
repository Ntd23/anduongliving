<script setup lang="ts">
import type { ShortcodeBlock } from "~/utils/shortcode";

defineProps<{
  block: ShortcodeBlock;
}>();
</script>

<template>
  <section class="shortcode-fallback">
    <div v-html="block.raw" />
  </section>
</template>
