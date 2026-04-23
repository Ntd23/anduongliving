import { cmsApiRoutes } from "~~/shared/routes/backend";
import { fetchCmsApi } from "~~/server/features/cms/gateway";

export default defineEventHandler((event) => {
  const sidebar = getRouterParam(event, "sidebar") || "";
  const query = getQuery(event);

  if (!sidebar) {
    throw createError({
      statusCode: 400,
      statusMessage: "Missing sidebar id",
    });
  }

  return fetchCmsApi(
    event,
    cmsApiRoutes.widgets.sidebar(sidebar),
    {
      lang: typeof query.lang === "string" ? query.lang : undefined,
    },
    "Failed to load sidebar widgets",
  );
});

