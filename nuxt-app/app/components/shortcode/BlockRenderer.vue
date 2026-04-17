<script setup lang="ts">
import ShortcodeAbout from "./About.vue";
import ShortcodeFallback from "./Fallback.vue";
import ShortcodeFeature from "./Feature.vue";
import ShortcodeNewsletter from "./Newsletter.vue";
import ShortcodeSkill from "./Skill.vue";
import ShortcodeTeam from "./Team.vue";
import type { ShortcodeBlock } from "~/composables/usePage";

const props = defineProps<{
  block: ShortcodeBlock;
}>();

const componentMap = {
  about: ShortcodeAbout,
  skill: ShortcodeSkill,
  feature: ShortcodeFeature,
  newsletter: ShortcodeNewsletter,
  team: ShortcodeTeam,
} as const;

const componentName = computed(() => {
  const name = (props.block.name || "").toLowerCase();

  return componentMap[name as keyof typeof componentMap] || ShortcodeFallback;
});
</script>

<template>
  <component
    :is="componentName"
    v-if="block"
    :block="block"
  />
</template>
