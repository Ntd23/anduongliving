<script setup lang="ts">
import type { ShortcodeBlock } from "~/composables/usePage";

defineProps<{
  block: ShortcodeBlock;
}>();
</script>

<template>
  <section class="shortcode-about">
    <div v-html="block.raw" />
  </section>
</template>
