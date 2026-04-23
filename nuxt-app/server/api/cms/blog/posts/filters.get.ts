import { cmsApiRoutes } from "~~/shared/routes/backend";
import { fetchCmsApi } from "~~/server/features/cms/gateway";

export default defineEventHandler((event) => {
  const query = getQuery(event);

  return fetchCmsApi(
    event,
    cmsApiRoutes.blog.filters(),
    {
      lang: typeof query.lang === "string" ? query.lang : undefined,
      page: typeof query.page === "string" ? query.page : undefined,
      per_page: typeof query.per_page === "string" ? query.per_page : undefined,
      search: typeof query.search === "string" ? query.search : undefined,
      categories: typeof query.categories === "string" ? query.categories : undefined,
      tags: typeof query.tags === "string" ? query.tags : undefined,
      featured: typeof query.featured === "string" ? query.featured : undefined,
      order: typeof query.order === "string" ? query.order : undefined,
      order_by: typeof query.order_by === "string" ? query.order_by : undefined,
    },
    "Failed to load filtered blog posts",
  );
});

