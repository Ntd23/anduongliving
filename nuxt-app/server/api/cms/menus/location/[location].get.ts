import { cmsApiRoutes } from "~~/shared/cms-routing";
import { fetchCmsApi } from "~~/server/utils/cms-api";

export default defineEventHandler((event) => {
  const location = getRouterParam(event, "location") || "";
  const query = getQuery(event);

  if (!location) {
    throw createError({
      statusCode: 400,
      statusMessage: "Missing menu location",
    });
  }

  return fetchCmsApi(
    event,
    cmsApiRoutes.menus.location(location),
    {
      lang: typeof query.lang === "string" ? query.lang : undefined,
    },
    "Failed to load menu location",
  );
});
