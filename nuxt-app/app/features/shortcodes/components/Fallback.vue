<script setup lang="ts">
import type { ShortcodeBlock } from "~/features/shortcodes/core";
import { useSanitizedCmsHtml } from "~/composables/useSanitizedCmsHtml";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const sanitizedHtml = useSanitizedCmsHtml(() => props.block.raw);

const hasMeaningfulContent = computed(() => {
  const raw = sanitizedHtml.value || "";

  // Ignore placeholder fragments like <p>&nbsp;</p> that only create empty spacing.
  const textOnly = raw
    .replace(/&nbsp;|&#160;/gi, " ")
    .replace(/<br\s*\/?>/gi, " ")
    .replace(/<[^>]+>/g, " ")
    .replace(/\s+/g, " ")
    .trim();

  return textOnly.length > 0;
});
</script>

<template>
  <section v-if="hasMeaningfulContent" class="shortcode-fallback">
    <div v-html="sanitizedHtml" />
  </section>
</template>




