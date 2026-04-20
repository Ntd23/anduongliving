<script setup lang="ts">
import { resolveShortcodeComponent } from "./component-registry";
import type { ShortcodeBlock } from "~/utils/shortcode";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const componentName = computed(() => {
  return resolveShortcodeComponent(props.block.name);
});
</script>

<template>
  <component
    :is="componentName"
    v-if="block"
    :block="block"
  />
</template>
