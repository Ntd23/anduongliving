<?php

namespace Botble\Blog\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Blog\Http\Controllers\API\Concerns\InteractsWithBlogTranslations;
use Botble\Blog\Http\Resources\TagResource;
use Botble\Blog\Models\Tag;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Http\Request;

class TagController extends BaseApiController
{
    use InteractsWithBlogTranslations;

    public function index(Request $request)
    {
        $languageCode = $this->resolveLanguageCodeFromRequest($request);
        $this->setLanguageContext($languageCode);

        $type = $request->query('type');
        $limit = $request->integer('limit', $request->integer('per_page', 10));

        $query = Tag::query()
            ->wherePublished()
            ->with('slugable')
            ->withCount('posts');

        $this->applyTranslationFilter($query, 'tags_translations', 'tags_id', $languageCode);

        if (! $type) {
            $tags = $query
                ->paginate($request->integer('per_page', 10));

            $tags->setCollection(
                $tags->getCollection()->map(fn (Tag $tag) => $this->translateTag($tag, $languageCode))
            );

            return $this
                ->httpResponse()
                ->setData(TagResource::collection($tags))
                ->toApiResponse();
        }

        if (strtolower((string) $type) === 'all') {
            $tags = $query
                ->orderBy('name')
                ->when($limit > 0, fn ($builder) => $builder->limit($limit))
                ->get()
                ->map(fn (Tag $tag) => $this->translateTag($tag, $languageCode));

            return $this
                ->httpResponse()
                ->setData(TagResource::collection($tags))
                ->toApiResponse();
        }

        $tags = $query
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->limit(max(1, $limit))
            ->get()
            ->map(fn (Tag $tag) => $this->translateTag($tag, $languageCode));

        return $this
            ->httpResponse()
            ->setData(TagResource::collection($tags))
            ->toApiResponse();
    }

    public function findBySlug(string $slug, Request $request)
    {
        $languageCode = $this->resolveLanguageCodeFromRequest($request);
        $this->setLanguageContext($languageCode);

        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Tag::class));

        if (! $slug) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        $tag = Tag::query()
            ->with('slugable')
            ->withCount('posts')
            ->wherePublished()
            ->find($slug->reference_id);

        if (! $tag) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        if ($languageCode) {
            $tag = $this->translateTag($tag, $languageCode);
        }

        return $this
            ->httpResponse()
            ->setData(new TagResource($tag))
            ->toApiResponse();
    }
}
