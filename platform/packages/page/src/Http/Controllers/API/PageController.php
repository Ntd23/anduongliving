<?php

namespace Botble\Page\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Page\Http\Resources\ListPageResource;
use Botble\Page\Http\Resources\PageResource;
use Botble\Page\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PageController extends BaseApiController
{
    /**
     * List pages
     *
     * @group Page
     *
     * @queryParam per_page integer The number of items to return per page (default: 10).
     * @queryParam page integer The page number to retrieve (default: 1).
     *
     * @response 200 {
     *   "error": false,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "About Us",
     *       "slug": "about-us",
     *       "content": "This is the about us page content...",
     *       "published_at": "2023-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "message": null
     * }
     */
    public function index(Request $request)
    {
        $languageCode = $this->languageCode($request);

        $pages = Page::query()
            ->wherePublished()
            ->with('slugable')
            ->when($languageCode, function (Builder $query, string $languageCode): void {
                $this->applyLanguageFilter($query, $languageCode);
            })
            ->paginate($request->integer('per_page', 10) ?: 10);

        if ($languageCode) {
            $pages->setCollection($pages->getCollection()->map(function (Page $page) use ($languageCode): Page {
                return $this->translatePage($page, $languageCode);
            }));
        }

        return $this
            ->httpResponse()
            ->setData(ListPageResource::collection($pages))
            ->toApiResponse();
    }

    /**
     * Get page by slug or ID
     *
     * @group Page
     *
     * @urlParam slug string required The slug or ID of the page to retrieve.
     *
     * @response 200 {
     *   "error": false,
     *   "data": {
     *     "id": 1,
     *     "title": "About Us",
     *     "slug": "about-us",
     *     "content": "This is the about us page content...",
     *     "published_at": "2023-01-01T00:00:00.000000Z"
     *   },
     *   "message": null
     * }
     *
     * @response 404 {
     *   "error": true,
     *   "message": "Not found"
     * }
     */
    public function show(int|string $slug, Request $request)
    {
        $languageCode = $this->languageCode($request);

        $page = Page::query()
            ->wherePublished()
            ->with('slugable')
            ->when($languageCode, function (Builder $query, string $languageCode): void {
                $this->applyLanguageFilter($query, $languageCode);
            })
            ->where(function (Builder $query) use ($slug): void {
                if (is_numeric($slug)) {
                    $query->where('id', (int) $slug);
                }

                $query->orWhereHas('slugable', function (Builder $slugQuery) use ($slug): void {
                    $slugQuery->where('key', (string) $slug)
                        ->where('reference_type', Page::class);
                });
            })
            ->first();

        if (! $page) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage(trans('packages/page::pages.not_found'));
        }

        if ($languageCode) {
            $page = $this->translatePage($page, $languageCode);
        }

        return $this
            ->httpResponse()
            ->setData(new PageResource($page))
            ->toApiResponse();
    }

    protected function languageCode(Request $request): ?string
    {
        $language = (string) $request->input('lang', $request->input('locale', ''));

        if ($language === '' || ! Schema::hasTable('languages')) {
            return null;
        }

        $languageCode = DB::table('languages')
            ->where('lang_locale', $language)
            ->value('lang_code');

        return $languageCode ?: null;
    }

    protected function applyLanguageFilter(Builder $query, string $languageCode): void
    {
        if (! Schema::hasTable('pages_translations')) {
            return;
        }

        $query->whereExists(function ($subQuery) use ($languageCode): void {
            $subQuery->selectRaw('1')
                ->from('pages_translations')
                ->whereColumn('pages_translations.pages_id', 'pages.id')
                ->where('pages_translations.lang_code', $languageCode);
        });
    }

    protected function translatePage(Page $page, string $languageCode): Page
    {
        if (! Schema::hasTable('pages_translations')) {
            return $page;
        }

        $translation = DB::table('pages_translations')
            ->where('pages_id', $page->getKey())
            ->where('lang_code', $languageCode)
            ->first();

        if (! $translation) {
            return $page;
        }

        $page->setAttribute('name', $translation->name ?? $page->name);
        $page->setAttribute('description', $translation->description ?? $page->description);
        $page->setAttribute('content', $translation->content ?? $page->content);

        foreach (['slug', 'title', 'excerpt'] as $field) {
            if (isset($translation->{$field})) {
                $page->setAttribute($field, $translation->{$field});
            }
        }

        $page->setRelation('translation', $translation);

        return $page;
    }
}
