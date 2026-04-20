const HYDRATION_PATTERN = /Hydration .*mismatch/i;

const normalizeComponentName = (type: unknown) => {
  if (!type || typeof type !== "object") {
    return null;
  }

  const component = type as {
    __name?: string;
    name?: string;
    file?: string;
  };

  return component.name || component.__name || component.file || null;
};

const buildTrace = (trace?: string) =>
  (trace || "")
    .split("\n")
    .map((line) => line.trim())
    .filter(Boolean);

const normalizeConsoleMessage = (args: unknown[]) =>
  args
    .map((arg) => {
      if (typeof arg === "string") {
        return arg;
      }

      if (arg instanceof Error) {
        return arg.message;
      }

      return "";
    })
    .filter(Boolean)
    .join(" ");

const logHydrationDebug = (
  originalConsole: Pick<typeof console, "groupCollapsed" | "groupEnd" | "info" | "warn">,
  source: string,
  message: string,
  details?: {
    componentName?: string | null;
    traceLines?: string[];
    args?: unknown[];
  },
) => {
  const stack = new Error("[hydration-debug]").stack
    ?.split("\n")
    .slice(2)
    .map((line) => line.trim())
    .filter(Boolean);

  originalConsole.groupCollapsed(`[hydration-mismatch:${source}]`);
  originalConsole.warn(message);

  if (details?.componentName) {
    originalConsole.info("component:", details.componentName);
  }

  if (details?.traceLines?.length) {
    originalConsole.info("trace:");

    for (const line of details.traceLines) {
      originalConsole.info(line);
    }
  }

  if (details?.args?.length) {
    originalConsole.info("args:", details.args);
  }

  if (stack?.length) {
    originalConsole.info("stack:");

    for (const line of stack) {
      originalConsole.info(line);
    }
  }

  originalConsole.groupEnd();
};

export default defineNuxtPlugin((nuxtApp) => {
  if (!import.meta.dev) {
    return;
  }

  const originalWarnHandler = nuxtApp.vueApp.config.warnHandler;
  const originalConsoleWarn = window.console.warn.bind(window.console);
  const originalConsoleError = window.console.error.bind(window.console);
  const originalConsoleGroupCollapsed = window.console.groupCollapsed.bind(window.console);
  const originalConsoleGroupEnd = window.console.groupEnd.bind(window.console);
  const originalConsoleInfo = window.console.info.bind(window.console);
  const originalConsoleTrace = window.console.trace.bind(window.console);

  const debugConsole = {
    groupCollapsed: originalConsoleGroupCollapsed,
    groupEnd: originalConsoleGroupEnd,
    info: originalConsoleInfo,
    warn: originalConsoleWarn,
  };

  window.console.warn = (...args: unknown[]) => {
    const message = normalizeConsoleMessage(args);

    if (HYDRATION_PATTERN.test(message)) {
      logHydrationDebug(debugConsole, "console.warn", message, { args });
      originalConsoleTrace("[hydration-mismatch trace]");
      return;
    }

    originalConsoleWarn(...args);
  };

  window.console.error = (...args: unknown[]) => {
    const message = normalizeConsoleMessage(args);

    if (HYDRATION_PATTERN.test(message)) {
      logHydrationDebug(debugConsole, "console.error", message, { args });
      originalConsoleTrace("[hydration-mismatch trace]");
      return;
    }

    originalConsoleError(...args);
  };

  nuxtApp.vueApp.config.warnHandler = (msg, instance, trace) => {
    if (HYDRATION_PATTERN.test(msg)) {
      const componentName = normalizeComponentName(instance?.type);
      const lines = buildTrace(trace);

      logHydrationDebug(debugConsole, "warnHandler", msg, {
        componentName,
        traceLines: lines,
      });
    }

    if (originalWarnHandler) {
      originalWarnHandler(msg, instance, trace);
      return;
    }

    console.warn(msg + trace);
  };
});
