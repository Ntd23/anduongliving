import { cmsApiRoutes } from "~~/shared/cms-routing";
import { fetchCmsApi } from "~~/server/utils/cms-api";

export default defineEventHandler((event) => {
  const query = getQuery(event);

  return fetchCmsApi(
    event,
    cmsApiRoutes.pages.homepage(),
    {
      lang: typeof query.lang === "string" ? query.lang : undefined,
    },
    "Failed to load homepage",
  );
});
