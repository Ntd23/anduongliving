<script setup lang="ts">
import { resolveShortcodeComponent } from "~/features/shortcodes/render/component-registry";
import type { ShortcodeBlock } from "~/features/shortcodes/core";

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




