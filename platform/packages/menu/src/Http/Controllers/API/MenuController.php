<?php

namespace Botble\Menu\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Base\Facades\BaseHelper;
use Botble\Language\Models\LanguageMeta;
use Botble\Menu\Models\Menu;
use Botble\Menu\Models\MenuLocation;
use Botble\Menu\Models\MenuNode;
use Botble\Page\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MenuController extends BaseApiController
{
    public function mainMenu(Request $request)
    {
        $language = $this->resolveLanguage($request, true);

        if ($language['requested'] && ! $language['code']) {
            return $this->missingMenuResponse('Main menu language is not supported.');
        }

        if (! $language['code']) {
            return $this->missingMenuResponse('Main menu language is required.');
        }

        $menu = $this->resolveMenuForLocation('main-menu', $language['code']);

        if (! $menu) {
            return $this->missingMenuResponse(sprintf(
                'Main menu is not configured for locale [%s].',
                $language['locale'] ?: $language['code']
            ));
        }

        return $this->menuResponse($menu, $language['code'], $language['locale'], 'main-menu');
    }

    public function byLocation(string $location, Request $request)
    {
        $location = trim($location);

        if ($location === '') {
            return $this->missingMenuResponse('Menu location is required.');
        }

        $language = $this->resolveLanguage($request, true);

        if ($language['requested'] && ! $language['code']) {
            return $this->missingMenuResponse('Menu language is not supported.');
        }

        if (! $language['code']) {
            return $this->missingMenuResponse('Menu language is required.');
        }

        $menu = $this->resolveMenuForLocation($location, $language['code']);

        if (! $menu) {
            return $this->missingMenuResponse(sprintf(
                'Menu location [%s] is not configured for locale [%s].',
                $location,
                $language['locale'] ?: $language['code']
            ));
        }

        return $this->menuResponse($menu, $language['code'], $language['locale'], $location);
    }

    public function show(int|string $menu, Request $request)
    {
        $language = $this->resolveLanguage($request);

        if ($language['requested'] && ! $language['code']) {
            return $this->missingMenuResponse('Menu language is not supported.');
        }

        $resolvedMenu = $this->findMenu($menu);

        if (! $resolvedMenu) {
            return $this->missingMenuResponse('Menu not found.');
        }

        if ($language['code']) {
            $resolvedMenu = $this->resolveTranslatedMenu($resolvedMenu, $language['code']);

            if (! $resolvedMenu) {
                return $this->missingMenuResponse(sprintf(
                    'Menu is not available for locale [%s].',
                    $language['locale'] ?: $language['code']
                ));
            }
        }

        return $this->menuResponse($resolvedMenu, $language['code'], $language['locale']);
    }

    protected function menuResponse(Menu $menu, ?string $languageCode, ?string $languageLocale, ?string $location = null)
    {
        $nodes = MenuNode::query()
            ->where('menu_id', $menu->getKey())
            ->orderBy('parent_id')
            ->orderBy('position')
            ->get();

        $defaultLocale = $this->defaultLanguage()['locale'];
        $items = $this->buildTree(
            $nodes->map(
                fn (MenuNode $node): array => $this->transformNode(
                    $node,
                    $languageCode,
                    $languageLocale,
                    $defaultLocale
                )
            )->all()
        );

        return $this
            ->httpResponse()
            ->setData([
                'id' => $menu->getKey(),
                'name' => $menu->name,
                'slug' => $menu->slug,
                'location' => $location,
                'locale' => $languageLocale,
                'items' => $items,
            ])
            ->toApiResponse();
    }

    protected function transformNode(
        MenuNode $node,
        ?string $languageCode,
        ?string $languageLocale,
        ?string $defaultLocale
    ): array {
        $reference = null;
        $title = (string) ($node->title ?? '');
        $url = (string) ($node->url ?? '');

        if ($node->reference_type === Page::class && $node->reference_id) {
            $page = Page::query()
                ->wherePublished()
                ->with('slugable')
                ->find($node->reference_id);

            if ($page) {
                if ($languageCode) {
                    $page = $this->translatePage($page, $languageCode);
                }

                $slug = trim((string) ($page->getAttribute('slug') ?: $page->slugable?->key ?: ''), '/');

                $reference = [
                    'slug' => $slug ?: null,
                ];

                if ($title === '') {
                    $title = (string) ($page->name ?? '');
                }

                $url = $this->buildPageUrl($page, $slug, $languageLocale, $defaultLocale);
            }
        }

        if ($url === '') {
            $url = '#';
        }

        return [
            'id' => $node->getKey(),
            'title' => $title,
            'url' => $url,
            'target' => $node->target,
            'css_class' => $node->css_class,
            'icon_font' => $node->icon_font,
            'reference_type' => $node->reference_type,
            'reference_id' => $node->reference_id,
            'is_external' => $this->isExternalUrl($url),
            'reference' => $reference,
            'parent_id' => (int) ($node->parent_id ?: 0),
            'position' => (int) ($node->position ?: 0),
        ];
    }

    protected function buildTree(array $items, int $parentId = 0): array
    {
        $branch = [];

        foreach ($items as $item) {
            if ((int) ($item['parent_id'] ?? 0) !== $parentId) {
                continue;
            }

            $children = $this->buildTree($items, (int) $item['id']);
            $item['children'] = $children;
            $branch[] = $item;
        }

        usort($branch, static function (array $left, array $right): int {
            return ($left['position'] ?? 0) <=> ($right['position'] ?? 0);
        });

        foreach ($branch as &$item) {
            unset($item['position']);
            unset($item['parent_id']);
        }

        return $branch;
    }

    protected function findMenu(int|string $menu): ?Menu
    {
        return Menu::query()
            ->wherePublished()
            ->where(function (Builder $query) use ($menu): void {
                if (is_numeric($menu)) {
                    $query->whereKey((int) $menu);
                }

                $query->orWhere('slug', (string) $menu);
            })
            ->first();
    }

    protected function resolveTranslatedMenu(Menu $menu, string $languageCode): ?Menu
    {
        if (! Schema::hasTable('language_meta')) {
            return null;
        }

        $meta = LanguageMeta::query()
            ->where('reference_type', Menu::class)
            ->where('reference_id', $menu->getKey())
            ->first();

        if (! $meta) {
            return null;
        }

        if ($meta->lang_meta_code === $languageCode) {
            return $menu;
        }

        $translatedMenuId = LanguageMeta::query()
            ->where('reference_type', Menu::class)
            ->where('lang_meta_origin', $meta->lang_meta_origin)
            ->where('lang_meta_code', $languageCode)
            ->value('reference_id');

        if (! $translatedMenuId) {
            return null;
        }

        return Menu::query()
            ->wherePublished()
            ->find($translatedMenuId);
    }

    protected function resolveMenuForLocation(string $location, string $languageCode): ?Menu
    {
        $locations = MenuLocation::query()
            ->where('location', $location)
            ->get(['id', 'menu_id']);

        if ($locations->isEmpty()) {
            return null;
        }

        $menuIds = $locations->pluck('menu_id')->filter()->values();

        if (Schema::hasTable('language_meta')) {
            $menuId = LanguageMeta::query()
                ->where('reference_type', Menu::class)
                ->whereIn('reference_id', $menuIds)
                ->where('lang_meta_code', $languageCode)
                ->value('reference_id');

            if ($menuId) {
                $menu = Menu::query()->wherePublished()->find($menuId);

                if ($menu) {
                    return $menu;
                }
            }

            $locationId = LanguageMeta::query()
                ->where('reference_type', MenuLocation::class)
                ->whereIn('reference_id', $locations->pluck('id'))
                ->where('lang_meta_code', $languageCode)
                ->value('reference_id');

            if ($locationId) {
                $menuId = MenuLocation::query()
                    ->whereKey($locationId)
                    ->value('menu_id');

                $menu = Menu::query()->wherePublished()->find($menuId);

                if ($menu) {
                    return $menu;
                }
            }
        }

        return null;
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

        return $page;
    }

    protected function buildPageUrl(
        Page $page,
        string $slug,
        ?string $languageLocale,
        ?string $defaultLocale
    ): string {
        $homepageId = BaseHelper::getHomepageId();
        $prefix = $this->localePrefix($languageLocale, $defaultLocale);

        if ((string) $page->getKey() === (string) $homepageId) {
            return $prefix ?: '/';
        }

        if ($slug === '') {
            return $prefix ?: '/';
        }

        return ($prefix ?: '') . '/' . ltrim($slug, '/');
    }

    protected function localePrefix(?string $languageLocale, ?string $defaultLocale): string
    {
        if (! $languageLocale || $languageLocale === $defaultLocale) {
            return '';
        }

        return '/' . trim($languageLocale, '/');
    }

    protected function isExternalUrl(string $url): bool
    {
        return preg_match('/^(?:[a-z][a-z0-9+.-]*:|\/\/)/i', $url) === 1;
    }

    protected function resolveLanguage(Request $request, bool $useDefaultWhenEmpty = false): array
    {
        $rawLanguage = trim((string) $request->input('lang', $request->input('locale', '')));
        $defaultLanguage = $this->defaultLanguage();

        if (! Schema::hasTable('languages')) {
            return [
                'requested' => $rawLanguage !== '',
                'code' => $useDefaultWhenEmpty && $rawLanguage === '' ? $defaultLanguage['code'] : null,
                'locale' => $useDefaultWhenEmpty && $rawLanguage === '' ? $defaultLanguage['locale'] : null,
            ];
        }

        if ($rawLanguage === '') {
            return [
                'requested' => false,
                'code' => $useDefaultWhenEmpty ? $defaultLanguage['code'] : null,
                'locale' => $useDefaultWhenEmpty ? $defaultLanguage['locale'] : null,
            ];
        }

        $normalized = str_replace('-', '_', $rawLanguage);
        $candidates = array_values(array_unique(array_filter([
            $normalized,
            str_replace('_', '-', $normalized),
        ])));

        $language = DB::table('languages')
            ->select('lang_code', 'lang_locale')
            ->whereIn('lang_code', $candidates)
            ->orWhereIn('lang_locale', $candidates)
            ->first();

        if (! $language) {
            $shortCodes = array_values(array_unique(array_filter(array_map(
                static fn (string $value): string => preg_split('/[-_]/', $value)[0] ?? '',
                $candidates
            ))));

            if ($shortCodes) {
                $language = DB::table('languages')
                    ->select('lang_code', 'lang_locale')
                    ->whereIn('lang_code', $shortCodes)
                    ->orWhereIn('lang_locale', $shortCodes)
                    ->first();
            }
        }

        return [
            'requested' => true,
            'code' => $language->lang_code ?? null,
            'locale' => $language->lang_locale ?? null,
        ];
    }

    protected function defaultLanguage(): array
    {
        if (! Schema::hasTable('languages')) {
            return [
                'code' => null,
                'locale' => null,
            ];
        }

        $language = DB::table('languages')
            ->select('lang_code', 'lang_locale')
            ->where('lang_is_default', 1)
            ->first();

        if (! $language) {
            $language = DB::table('languages')
                ->select('lang_code', 'lang_locale')
                ->orderByDesc('lang_is_default')
                ->orderBy('lang_id')
                ->first();
        }

        return [
            'code' => $language->lang_code ?? null,
            'locale' => $language->lang_locale ?? null,
        ];
    }

    protected function missingMenuResponse(string $message)
    {
        return $this
            ->httpResponse()
            ->setError()
            ->setCode(404)
            ->setMessage($message);
    }
}
