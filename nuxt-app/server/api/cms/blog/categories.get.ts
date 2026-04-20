import { cmsApiRoutes } from "~~/shared/cms-routing";
import { fetchCmsApi } from "~~/server/utils/cms-api";

export default defineEventHandler((event) => {
  const query = getQuery(event);

  return fetchCmsApi(
    event,
    cmsApiRoutes.blog.categories(),
    {
      lang: typeof query.lang === "string" ? query.lang : undefined,
      limit: typeof query.limit === "string" ? query.limit : undefined,
      per_page: typeof query.per_page === "string" ? query.per_page : undefined,
      type: typeof query.type === "string" ? query.type : undefined,
    },
    "Failed to load blog categories",
  );
});
