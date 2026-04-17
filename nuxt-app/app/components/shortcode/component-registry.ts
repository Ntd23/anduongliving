import type { Component } from "vue";
import { findShortcodeDefinition } from "~/utils/shortcode";

type ShortcodeComponentModule = {
  default: Component;
};

const shortcodeComponentModules = import.meta.glob("./*.vue", {
  eager: true,
}) as Record<string, ShortcodeComponentModule>;

const fallbackComponent = shortcodeComponentModules["./Fallback.vue"]?.default;

export const resolveShortcodeComponent = (name?: string | null): Component | null => {
  const definition = findShortcodeDefinition(name);
  const component = definition
    ? shortcodeComponentModules[`./${definition.componentName}.vue`]?.default
    : null;

  return component || fallbackComponent || null;
};
