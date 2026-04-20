<?php

namespace Botble\Blog\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Blog\Http\Controllers\API\Concerns\InteractsWithBlogTranslations;
use Botble\Blog\Http\Resources\ListPostResource;
use Botble\Blog\Http\Resources\PostResource;
use Botble\Blog\Models\Post;
use Botble\Blog\Supports\FilterPost;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PostController extends BaseApiController
{
    use InteractsWithBlogTranslations;

    public function index(Request $request)
    {
        $languageCode = $this->resolveLanguageCodeFromRequest($request);
        $this->setLanguageContext($languageCode);

        $type = strtolower((string) $request->query('type', ''));
        $limit = max(1, $request->integer('limit', $request->integer('per_page', 10)));

        $query = Post::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with(['tags', 'categories', 'author', 'slugable']);

        $this->applyTranslationFilter($query, 'posts_translations', 'posts_id', $languageCode);

        if ($type === 'popular') {
            $posts = $query
                ->orderByDesc('views')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
                ->map(fn (Post $post) => $this->translatePost($post, $languageCode));

            return $this
                ->httpResponse()
                ->setData(ListPostResource::collection($posts))
                ->toApiResponse();
        }

        if ($type === 'recent') {
            $posts = $query
                ->latest('created_at')
                ->limit($limit)
                ->get()
                ->map(fn (Post $post) => $this->translatePost($post, $languageCode));

            return $this
                ->httpResponse()
                ->setData(ListPostResource::collection($posts))
                ->toApiResponse();
        }

        $posts = $query
            ->latest('created_at')
            ->paginate($request->integer('per_page', 10));

        $posts->setCollection(
            $posts->getCollection()->map(fn (Post $post) => $this->translatePost($post, $languageCode))
        );

        return $this
            ->httpResponse()
            ->setData(ListPostResource::collection($posts))
            ->toApiResponse();
    }

    public function getSearch(Request $request)
    {
        $languageCode = $this->resolveLanguageCodeFromRequest($request);
        $this->setLanguageContext($languageCode);

        $searchTerm = BaseHelper::stringify($request->input('q'));

        $query = Post::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with(['tags', 'categories', 'author', 'slugable']);

        $this->applyTranslationFilter($query, 'posts_translations', 'posts_id', $languageCode);

        if ($searchTerm !== '') {
            $query->where(function (Builder $builder) use ($searchTerm, $languageCode): void {
                $builder
                    ->where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');

                if ($languageCode && Schema::hasTable('posts_translations')) {
                    $builder->orWhereExists(function ($subQuery) use ($searchTerm, $languageCode): void {
                        $subQuery->selectRaw('1')
                            ->from('posts_translations')
                            ->whereColumn('posts_translations.posts_id', 'posts.id')
                            ->where('posts_translations.lang_code', $languageCode)
                            ->where(function ($translationQuery) use ($searchTerm): void {
                                $translationQuery
                                    ->where('posts_translations.name', 'LIKE', '%' . $searchTerm . '%')
                                    ->orWhere('posts_translations.description', 'LIKE', '%' . $searchTerm . '%')
                                    ->orWhere('posts_translations.content', 'LIKE', '%' . $searchTerm . '%');
                            });
                    });
                }
            });
        }

        $posts = $query
            ->latest('created_at')
            ->limit(max(1, $request->integer('limit', 10)))
            ->get()
            ->map(fn (Post $post) => $this->translatePost($post, $languageCode));

        $data = [
            'items' => ListPostResource::collection($posts),
            'query' => $searchTerm,
            'count' => $posts->count(),
        ];

        if ($data['count'] > 0) {
            return $this
                ->httpResponse()
                ->setData(apply_filters(BASE_FILTER_SET_DATA_SEARCH, $data));
        }

        return $this
            ->httpResponse()
            ->setError()
            ->setMessage(trans('core/base::layouts.no_search_result'));
    }

    public function getFilters(Request $request)
    {
        $languageCode = $this->resolveLanguageCodeFromRequest($request);
        $this->setLanguageContext($languageCode);

        $filters = FilterPost::setFilters($request->input());

        $query = Post::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with(['tags', 'categories', 'author', 'slugable']);

        $this->applyTranslationFilter($query, 'posts_translations', 'posts_id', $languageCode);

        if ($search = $filters['search']) {
            $query->where(function (Builder $builder) use ($search, $languageCode): void {
                $builder
                    ->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%');

                if ($languageCode && Schema::hasTable('posts_translations')) {
                    $builder->orWhereExists(function ($subQuery) use ($search, $languageCode): void {
                        $subQuery->selectRaw('1')
                            ->from('posts_translations')
                            ->whereColumn('posts_translations.posts_id', 'posts.id')
                            ->where('posts_translations.lang_code', $languageCode)
                            ->where(function ($translationQuery) use ($search): void {
                                $translationQuery
                                    ->where('posts_translations.name', 'LIKE', '%' . $search . '%')
                                    ->orWhere('posts_translations.description', 'LIKE', '%' . $search . '%')
                                    ->orWhere('posts_translations.content', 'LIKE', '%' . $search . '%');
                            });
                    });
                }
            });
        }

        $categoryIds = $this->normalizeFilterIds($filters['categories']);
        if ($categoryIds) {
            $query->whereHas('categories', function (Builder $builder) use ($categoryIds): void {
                $builder->whereIn('categories.id', $categoryIds);
            });
        }

        $tagIds = $this->normalizeFilterIds($filters['tags']);
        if ($tagIds) {
            $query->whereHas('tags', function (Builder $builder) use ($tagIds): void {
                $builder->whereIn('tags.id', $tagIds);
            });
        }

        if ($filters['featured'] !== null && $filters['featured'] !== '') {
            $query->where('is_featured', (bool) $filters['featured']);
        }

        $orderBy = match ($filters['order_by']) {
            'author' => 'author_id',
            'title' => 'name',
            'slug' => 'slugs.key',
            'created_at', 'updated_at', 'id' => $filters['order_by'],
            default => 'updated_at',
        };

        if ($orderBy === 'slugs.key') {
            $query->join('slugs', function ($join): void {
                $join->on('slugs.reference_id', '=', 'posts.id')
                    ->where('slugs.reference_type', Post::class);
            })->select('posts.*');
        }

        $posts = $query
            ->orderBy($orderBy, $filters['order'])
            ->paginate((int) $filters['per_page']);

        $posts->setCollection(
            $posts->getCollection()->map(fn (Post $post) => $this->translatePost($post, $languageCode))
        );

        return $this
            ->httpResponse()
            ->setData(ListPostResource::collection($posts))
            ->toApiResponse();
    }

    public function findBySlug(string $slug, Request $request)
    {
        $languageCode = $this->resolveLanguageCodeFromRequest($request);
        $this->setLanguageContext($languageCode);

        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Post::class));

        if (! $slug) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        $post = Post::query()
            ->with(['categories', 'tags', 'author', 'slugable'])
            ->where([
                'id' => $slug->reference_id,
                'status' => BaseStatusEnum::PUBLISHED,
            ])
            ->first();

        if (! $post) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        if ($languageCode) {
            $post = $this->translatePost($post, $languageCode);
        }

        $relatedPosts = get_related_posts($post->id, 2)
            ->map(fn (Post $relatedPost) => $languageCode ? $this->translatePost($relatedPost, $languageCode) : $relatedPost)
            ->values();

        $resource = (new PostResource($post))->toArray($request);
        $resource['navigation'] = [
            'previous' => isset($relatedPosts[0]) ? ListPostResource::make($relatedPosts[0])->resolve($request) : null,
            'next' => isset($relatedPosts[1]) ? ListPostResource::make($relatedPosts[1])->resolve($request) : null,
        ];

        return $this
            ->httpResponse()
            ->setData($resource)
            ->toApiResponse();
    }

    protected function normalizeFilterIds(mixed $value): array
    {
        if (! $value) {
            return [];
        }

        $items = is_array($value) ? $value : explode(',', (string) $value);

        return array_values(array_filter(array_map(static fn ($item) => (int) $item, $items)));
    }
}
