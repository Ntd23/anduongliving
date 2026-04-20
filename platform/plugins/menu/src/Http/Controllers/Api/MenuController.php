<?php

namespace Menu\Http\Controllers\Api;

use Botble\Menu\Models\Menu;
use Botble\Menu\Models\MenuLocation;
use Botble\Menu\Models\MenuNode;
use Botble\Page\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        $menus = Menu::query()
            ->where('status', 'published')
            ->orderBy('name')
            ->get();

        return response()->json([
            'error' => false,
            'message' => 'Lấy danh sách menu thành công',
            'data' => $menus->map(fn (Menu $menu): array => [
                'id' => $menu->id,
                'name' => $menu->name,
                'slug' => $menu->slug,
            ])->values(),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function show(string $location, Request $request): JsonResponse
    {
        $lang = $this->languageCode($request);

        $menu = $this->menuQuery($location)
            ->first();

        if (! $menu) {
            return $this->missingMenuResponse('Menu không tồn tại');
        }

        $nodes = MenuNode::query()
            ->where('menu_id', $menu->id)
            ->orderBy('parent_id')
            ->orderBy('position')
            ->get();

        $items = $nodes->map(function (MenuNode $node) use ($lang): array {
            $reference = null;
            $url = $node->url ?: '#';
            $title = $node->title;

            if ($node->reference_type === Page::class && $node->reference_id) {
                $page = Page::query()->find($node->reference_id);

                if ($page) {
                    $page = $lang ? $this->translatePage($page, $lang) : $page;

                    $reference = [
                        'id' => $page->id,
                        'name' => $page->name,
                        'slug' => $page->slug,
                    ];

                    if (! empty($page->name)) {
                        $title = $page->name;
                    }

                    if (! empty($page->slug)) {
                        $url = url($page->slug);
                    }
                }
            }

            return [
                'id' => $node->id,
                'title' => $title,
                'url' => $url,
                'icon_font' => $node->icon_font,
                'css_class' => $node->css_class,
                'target' => $node->target,
                'parent_id' => $node->parent_id,
                'position' => $node->position,
                'reference_type' => $node->reference_type,
                'reference_id' => $node->reference_id,
                'reference' => $reference,
            ];
        })->toArray();

        return response()->json([
            'error' => false,
            'message' => 'Lấy menu thành công',
            'debug' => [
                'requested_location' => $location,
                'lang' => $lang,
                'menu_id' => $menu->id,
                'menu_name' => $menu->name,
            ],
            'data' => [
                'id' => $menu->id,
                'name' => $menu->name,
                'slug' => $menu->slug,
                'location' => $location,
                'lang' => $lang,
                'items' => $this->buildTree($items),
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
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

    protected function menuQuery(string $location): Builder
    {
        $menuId = MenuLocation::query()
            ->where('location', $location)
            ->value('menu_id');

        return Menu::query()
            ->when($menuId, fn (Builder $query) => $query->whereKey($menuId))
            ->when(! $menuId, fn (Builder $query) => $query->whereRaw('1 = 0'));
    }

    protected function missingMenuResponse(string $message): JsonResponse
    {
        return response()->json([
            'error' => true,
            'message' => $message,
            'data' => null,
        ], 404, [], JSON_UNESCAPED_UNICODE);
    }

    protected function buildTree(array $items, int $parentId = 0): array
    {
        $branch = [];

        foreach ($items as $item) {
            if ((int) $item['parent_id'] === $parentId) {
                $item['children'] = $this->buildTree($items, (int) $item['id']);
                $branch[] = $item;
            }
        }

        return $branch;
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
