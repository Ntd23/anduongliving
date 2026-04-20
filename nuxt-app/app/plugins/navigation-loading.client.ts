export default defineNuxtPlugin((nuxtApp) => {
  const indicator = useLoadingIndicator({
    throttle: 0,
    duration: 2200,
  });

  const start = () => {
    indicator.start();
  };

  const finish = () => {
    indicator.finish();
  };

  const router = useRouter();

  router.beforeEach((to, from) => {
    if (to.fullPath !== from.fullPath) {
      start();
    }
  });

  router.afterEach(() => {
    finish();
  });

  router.onError(() => {
    finish();
  });

  nuxtApp.hook("page:finish", () => {
    finish();
  });

  nuxtApp.hook("app:error", () => {
    finish();
  });

  const isInternalAnchor = (anchor: HTMLAnchorElement) => {
    if (!anchor.href) {
      return false;
    }

    if (anchor.target && anchor.target !== "_self") {
      return false;
    }

    if (anchor.hasAttribute("download")) {
      return false;
    }

    const url = new URL(anchor.href, window.location.href);

    if (url.origin !== window.location.origin) {
      return false;
    }

    // Ignore hash-only jumps on the same document.
    if (
      url.pathname === window.location.pathname
      && url.search === window.location.search
      && url.hash
    ) {
      return false;
    }

    return true;
  };

  const onClick = (event: MouseEvent) => {
    if (
      event.defaultPrevented
      || event.button !== 0
      || event.metaKey
      || event.ctrlKey
      || event.shiftKey
      || event.altKey
    ) {
      return;
    }

    const target = event.target as HTMLElement | null;
    const anchor = target?.closest("a");

    if (!(anchor instanceof HTMLAnchorElement)) {
      return;
    }

    if (isInternalAnchor(anchor)) {
      start();
    }
  };

  window.addEventListener("click", onClick, true);
});
