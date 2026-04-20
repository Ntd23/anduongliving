import { cmsApiRoutes } from "~~/shared/cms-routing";
import { fetchCmsApi } from "~~/server/utils/cms-api";

export default defineEventHandler((event) => {
  const slug = getRouterParam(event, "slug") || "";
  const query = getQuery(event);

  if (!slug) {
    throw createError({
      statusCode: 400,
      statusMessage: "Missing category slug",
    });
  }

  return fetchCmsApi(
    event,
    cmsApiRoutes.blog.category(slug),
    {
      lang: typeof query.lang === "string" ? query.lang : undefined,
    },
    "Failed to load blog category",
  );
});
