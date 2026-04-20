import { cmsApiRoutes } from "~~/shared/cms-routing";
import { fetchCmsApi } from "~~/server/utils/cms-api";

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
