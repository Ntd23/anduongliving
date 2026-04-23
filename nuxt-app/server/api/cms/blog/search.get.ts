import { cmsApiRoutes } from "~~/shared/routes/backend";
import { fetchCmsApi } from "~~/server/features/cms/gateway";

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

