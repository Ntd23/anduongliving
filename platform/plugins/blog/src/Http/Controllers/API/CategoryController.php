<?php

namespace Botble\Blog\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Http\Controllers\API\Concerns\InteractsWithBlogTranslations;
use Botble\Blog\Http\Resources\CategoryResource;
use Botble\Blog\Http\Resources\ListCategoryResource;
use Botble\Blog\Models\Category;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CategoryController extends BaseApiController
{
    use InteractsWithBlogTranslations;

    public function index(Request $request)
    {
        $languageCode = $this->resolveLanguageCodeFromRequest($request);
        $this->setLanguageContext($languageCode);

        $type = $request->query('type');
        $limit = $request->integer('limit', $request->integer('per_page', 10));

        $query = Category::query()
            ->wherePublished()
            ->with(['slugable', 'children', 'parent'])
            ->withCount('posts');

        $this->applyTranslationFilter($query, 'categories_translations', 'categories_id', $languageCode);

        if (! $type) {
            $categories = $query
                ->latest()
                ->paginate($request->integer('per_page', 10));

            $categories->setCollection(
                $categories->getCollection()->map(fn (Category $category) => $this->translateCategory($category, $languageCode))
            );

            return $this
                ->httpResponse()
                ->setData(ListCategoryResource::collection($categories))
                ->toApiResponse();
        }

        if (strtolower((string) $type) === 'all') {
            $categories = $query
                ->orderBy('name')
                ->when($limit > 0, fn (Builder $builder) => $builder->limit($limit))
                ->get()
                ->map(fn (Category $category) => $this->translateCategory($category, $languageCode));

            return $this
                ->httpResponse()
                ->setData(ListCategoryResource::collection($categories))
                ->toApiResponse();
        }

        $categories = $query
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->limit(max(1, $limit))
            ->get()
            ->map(fn (Category $category) => $this->translateCategory($category, $languageCode));

        return $this
            ->httpResponse()
            ->setData(ListCategoryResource::collection($categories))
            ->toApiResponse();
    }

    public function getFilters(Request $request)
    {
        $languageCode = $this->resolveLanguageCodeFromRequest($request);
        $this->setLanguageContext($languageCode);

        $query = Category::query()
            ->wherePublished()
            ->with(['slugable', 'children', 'parent'])
            ->withCount('posts');

        $this->applyTranslationFilter($query, 'categories_translations', 'categories_id', $languageCode);

        if ($search = $request->input('search')) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        $orderBy = $request->input('order_by', 'created_at');
        $orderBy = in_array($orderBy, ['created_at', 'updated_at', 'id', 'name'], true) ? $orderBy : 'created_at';
        $order = strtolower((string) $request->input('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        $categories = $query
            ->orderBy($orderBy, $order)
            ->paginate($request->integer('per_page', 10));

        $categories->setCollection(
            $categories->getCollection()->map(fn (Category $category) => $this->translateCategory($category, $languageCode))
        );

        return $this
            ->httpResponse()
            ->setData(CategoryResource::collection($categories))
            ->toApiResponse();
    }

    public function findBySlug(string $slug, Request $request)
    {
        $languageCode = $this->resolveLanguageCodeFromRequest($request);
        $this->setLanguageContext($languageCode);

        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Category::class));

        if (! $slug) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        $category = Category::query()
            ->with(['slugable', 'children', 'parent'])
            ->withCount('posts')
            ->where([
                'id' => $slug->reference_id,
                'status' => BaseStatusEnum::PUBLISHED,
            ])
            ->first();

        if (! $category) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        if ($languageCode) {
            $category = $this->translateCategory($category, $languageCode);
        }

        return $this
            ->httpResponse()
            ->setData(new ListCategoryResource($category))
            ->toApiResponse();
    }
}
