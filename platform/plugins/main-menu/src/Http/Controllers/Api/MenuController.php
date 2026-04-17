<?php

namespace Anduongliving\MainMenu\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Botble\Menu\Models\Menu;
use Botble\Menu\Models\MenuNode;
use Botble\Page\Models\Page;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $lang = request('lang', session('language', 'vi'));
        $lang = in_array($lang, ['vi', 'ja']) ? $lang : 'vi';

        session(['language' => $lang]);

        app()->setLocale($lang);

        $localizedSlug = $this->resolveMenuSlug($slug, $lang);

        $menu = Menu::query()
            ->where('slug', $localizedSlug)
            ->first();

        if (!$menu) {
            return response()->json([
                'error' => true,
                'message' => 'Menu không tồn tại',
                'debug' => [
                    'request_lang' => request('lang'),
                    'resolved_lang' => $lang,
                    'requested_slug' => $slug,
                    'localized_slug' => $localizedSlug,
                ],
                'data' => null,
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $nodes = MenuNode::query()
            ->where('menu_id', $menu->id)
            ->orderBy('parent_id')
            ->orderBy('position')
            ->get();

        $items = $nodes->map(function ($node) use ($lang) {
            $reference = null;
            $url = $node->url ?: '#';
            $translatedTitle = $node->title;

            if ($node->reference_type === Page::class && $node->reference_id) {
                $page = Page::query()->find($node->reference_id);

                if ($page) {
                    $page = apply_filters('base_filter_get_translation_model', $page, $lang);

                    $reference = [
                        'id' => $page->id,
                        'name' => $page->name,
                        'slug' => $page->slug,
                    ];

                    if (!empty($page->name)) {
                        $translatedTitle = $page->name;
                    }

                    if (!empty($page->slug)) {
                        $url = url($page->slug);
                    }
                }
            }

            return [
                'id' => $node->id,
                'title' => $translatedTitle,
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
        });

        return response()->json([
            'error' => false,
            'message' => 'Lấy menu thành công',
            'debug' => [
                'request_lang' => request('lang'),
                'session_lang' => session('language'),
                'resolved_lang' => $lang,
                'requested_slug' => $slug,
                'localized_slug' => $localizedSlug,
                'menu_id' => $menu->id,
                'menu_name' => $menu->name,
            ],
            'data' => [
                'id' => $menu->id,
                'name' => $menu->name,
                'slug' => $menu->slug,
                'items' => $this->buildTree($items->toArray()),
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function setLanguage(Request $request)
    {
        $lang = $request->get('lang', 'vi');
        $lang = in_array($lang, ['vi', 'ja']) ? $lang : 'vi';

        session(['language' => $lang]);

        return response()->json([
            'error' => false,
            'message' => 'Ngôn ngữ đã được cập nhật',
            'data' => [
                'language' => $lang,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    protected function resolveMenuSlug(string $slug, string $lang): string
    {
        $baseSlug = preg_replace('/-(vi|ja)$/', '', $slug);

        return $baseSlug . '-' . $lang;
    }

    protected function buildTree(array $items, int $parentId = 0): array
    {
        $branch = [];

        foreach ($items as $item) {
            if ((int) $item['parent_id'] === $parentId) {
                $children = $this->buildTree($items, (int) $item['id']);
                $item['children'] = $children;

                $branch[] = $item;
            }
        }

        return $branch;
    }
}