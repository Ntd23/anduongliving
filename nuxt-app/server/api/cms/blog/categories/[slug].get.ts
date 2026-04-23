import { cmsApiRoutes } from "~~/shared/routes/backend";
import { fetchCmsApi } from "~~/server/features/cms/gateway";

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

