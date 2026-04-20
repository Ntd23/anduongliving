<script setup lang="ts">
import type { ShortcodeBlock } from "~/utils/shortcode";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);
</script>

<template>
  <section class="shortcode-section">
    <div v-html="sanitizedHtml" />
  </section>
</template>
