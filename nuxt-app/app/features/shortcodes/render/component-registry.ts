import type { Component } from "vue";
import { findShortcodeDefinition } from "~/features/shortcodes/core";

type ShortcodeComponentModule = {
  default: Component;
};

const shortcodeComponentModules = import.meta.glob("../components/*.vue", {
  eager: true,
}) as Record<string, ShortcodeComponentModule>;

const fallbackComponent = shortcodeComponentModules["../components/Fallback.vue"]?.default;

export const resolveShortcodeComponent = (name?: string | null): Component | null => {
  const definition = findShortcodeDefinition(name);
  const component = definition
    ? shortcodeComponentModules[`../components/${definition.componentName}.vue`]?.default
    : null;

  return component || fallbackComponent || null;
};



