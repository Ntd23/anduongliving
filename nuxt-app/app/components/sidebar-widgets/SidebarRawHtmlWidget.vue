<script setup lang="ts">
import type { SidebarWidgetManifest } from "~/utils/sidebar-widgets";

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
