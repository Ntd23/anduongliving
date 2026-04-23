import { cmsApiRoutes } from "~~/shared/routes/backend";
import { fetchCmsApi } from "~~/server/features/cms/gateway";

export default defineEventHandler((event) => {
  const query = getQuery(event);

  return fetchCmsApi(
    event,
    cmsApiRoutes.blog.posts(),
    {
      lang: typeof query.lang === "string" ? query.lang : undefined,
      limit: typeof query.limit === "string" ? query.limit : undefined,
      per_page: typeof query.per_page === "string" ? query.per_page : undefined,
      page: typeof query.page === "string" ? query.page : undefined,
      type: typeof query.type === "string" ? query.type : undefined,
    },
    "Failed to load blog posts",
  );
});

