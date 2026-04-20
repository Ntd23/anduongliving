import { cmsApiRoutes } from "~~/shared/cms-routing";
import { fetchCmsApi } from "~~/server/utils/cms-api";

export default defineEventHandler((event) => {
  const query = getQuery(event);

  return fetchCmsApi(
    event,
    cmsApiRoutes.blog.search(),
    {
      lang: typeof query.lang === "string" ? query.lang : undefined,
      q: typeof query.q === "string" ? query.q : undefined,
      limit: typeof query.limit === "string" ? query.limit : undefined,
    },
    "Failed to search blog posts",
  );
});
