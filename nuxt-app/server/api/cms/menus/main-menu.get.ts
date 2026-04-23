import { cmsApiRoutes } from "~~/shared/routes/backend";
import { fetchCmsApi } from "~~/server/features/cms/gateway";

export default defineEventHandler((event) => {
  const query = getQuery(event);

  return fetchCmsApi(
    event,
    cmsApiRoutes.menus.main(),
    {
      lang: typeof query.lang === "string" ? query.lang : undefined,
    },
    "Failed to load main menu",
  );
});

