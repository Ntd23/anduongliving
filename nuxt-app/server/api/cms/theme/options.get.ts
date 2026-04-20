import { cmsApiRoutes } from "~~/shared/cms-routing";
import { fetchCmsApi } from "~~/server/utils/cms-api";

export default defineEventHandler((event) => {
  const query = getQuery(event);

  return fetchCmsApi(
    event,
    cmsApiRoutes.theme.options(),
    {
      lang: typeof query.lang === "string" ? query.lang : undefined,
    },
    "Failed to load theme options",
  );
});
