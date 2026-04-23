export const resolvePageLayoutName = (template?: string | null) => {
  switch (template) {
    case "side-menu":
      return "cms-side-menu";
    case "full-menu":
      return "cms-full-menu";
    case "full-width":
      return "cms-full-width";
    case "blog-sidebar":
      return "cms-blog-sidebar";
    default:
      return "default";
  }
};

export const layoutUsesBreadcrumbs = (layoutName?: string | null) =>
  ["default", "cms-full-menu", "cms-full-width", "cms-blog-sidebar"].includes(layoutName || "");

export const layoutUsesFooter = (layoutName?: string | null) =>
  ["default", "cms-full-menu", "cms-full-width", "cms-blog-sidebar"].includes(layoutName || "");



