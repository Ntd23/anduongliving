<script setup lang="ts">
import type { SidebarWidgetManifest } from "~/features/cms/ui/sidebar-widgets/registry";

defineProps<{
  widget: SidebarWidgetManifest;
}>();
</script>

<template>
  <div
    class="sidebar-widget-raw"
    v-html="widget.rendered_html || ''"
  />
</template>



