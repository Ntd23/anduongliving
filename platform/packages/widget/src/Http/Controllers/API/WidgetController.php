<?php

namespace Botble\Widget\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Language\Facades\Language;
use Botble\Menu\Models\Menu;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Http\Resources\WidgetResource;
use Botble\Widget\Models\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class WidgetController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Widget::query();

        if ($request->has('sidebar_id')) {
            $query->where('sidebar_id', $request->query('sidebar_id'));
        }

        $locale = $this->resolveLanguageCode($request->input('locale', $request->input('lang')));
         
        if ($request->has('theme')) {
            // If theme is explicitly passed, use it
            $themeName = $request->query('theme');
        } else {
            // Otherwise use theme based on locale
            $themeName = Widget::getThemeName($locale);
        }
        
        $widgets = $query
            ->where('theme', $themeName)
            ->orderBy('position')
            ->get();

        return $this
            ->httpResponse()
            ->setData(WidgetResource::collection($widgets))
            ->toApiResponse();
    }

    public function getBySidebar(Request $request, string $sidebarId)
    {
        [$languageCode] = $this->setLanguageContext($request);
        $themeName = Widget::getThemeName($languageCode);

        $widgets = Widget::query()
            ->where('theme', $themeName)
            ->where('sidebar_id', $sidebarId)
            ->orderBy('position')
            ->get();

        app('botble.widget-group-collection')->load(true);

        $widgets->each(function ($widget) {
            if (isset($widget->data['menu_id']) && !empty($widget->data['menu_id'])) {
                $menuId = $widget->data['menu_id'];

                $menu = Menu::query()
                    ->where(function ($query) use ($menuId) {
                        $query->where('slug', $menuId)
                              ->orWhere('id', $menuId);
                    })
                    ->with(['menuNodes' => function ($query) {
                        $query->orderBy('position');
                    }])
                    ->first();
                
                if ($menu) {
                    $widget->menu_data = [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'slug' => $menu->slug,
                        'location' => $menu->location,
                        'nodes' => $menu->menuNodes ? $menu->menuNodes->map(function ($node) {
                            return [
                                'id' => $node->id,
                                'menu_id' => $node->menu_id,
                                'parent_id' => $node->parent_id,
                                'title' => $node->title,
                                'url' => $node->url,
                                'icon' => $node->icon,
                                'target' => $node->target,
                                'position' => $node->position,
                                'has_children' => $node->children && $node->children->count() > 0,
                                'children' => $node->children ? $node->children->map(function ($child) {
                                    return [
                                        'id' => $child->id,
                                        'menu_id' => $child->menu_id,
                                        'parent_id' => $child->parent_id,
                                        'title' => $child->title,
                                        'url' => $child->url,
                                        'icon' => $child->icon,
                                        'target' => $child->target,
                                        'position' => $child->position,
                                    ];
                                })->toArray() : []
                            ];
                        })->toArray() : []
                    ];
                }
            }

            $widget->rendered_html = $this->renderWidgetHtml($widget);
            $widget->meta = $this->resolveWidgetMeta($widget);
        });

        return $this
            ->httpResponse()
            ->setData([
                'sidebar_id' => $sidebarId,
                'lang' => $languageCode,
                'locale' => Language::getCurrentLocale(),
                'items' => WidgetResource::collection($widgets)->resolve($request),
            ])
            ->toApiResponse();
    }

    public function renderSidebar(Request $request, string $sidebarId)
    {
        [$languageCode, $languageLocale] = $this->setLanguageContext($request);

        app('botble.widget-group-collection')->load(true);

        return $this
            ->httpResponse()
            ->setData([
                'sidebar_id' => $sidebarId,
                'lang' => $languageCode,
                'locale' => $languageLocale,
                'html' => dynamic_sidebar($sidebarId),
            ])
            ->toApiResponse();
    }

    protected function setLanguageContext(Request $request): array
    {
        $languageCode = $this->resolveLanguageCode($request->input('locale', $request->input('lang')));
        $languageLocale = $this->resolveLanguageLocale($languageCode);

        if ($languageLocale) {
            App::setLocale($languageLocale);
            Language::setCurrentLocale($languageLocale);
        }

        if ($languageCode) {
            Language::setCurrentLocaleCode($languageCode);
        }

        return [$languageCode, $languageLocale];
    }

    protected function renderWidgetHtml(Widget $widget): ?string
    {
        if (! class_exists($widget->widget_id)) {
            return null;
        }

        try {
            $instance = new $widget->widget_id();

            if (! $instance instanceof AbstractWidget) {
                return null;
            }

            return $instance->run($widget->sidebar_id, $widget->position);
        } catch (Throwable) {
            return null;
        }
    }

    protected function resolveWidgetMeta(Widget $widget): ?array
    {
        return match ($widget->widget_id) {
            'NewsletterWidget' => $this->resolveNewsletterMeta(),
            'CheckAvailabilityForm' => $this->resolveCheckAvailabilityMeta(),
            default => null,
        };
    }

    protected function resolveNewsletterMeta(): ?array
    {
        if (! Route::has('public.newsletter.subscribe')) {
            return null;
        }

        return [
            'action_url' => route('public.newsletter.subscribe'),
        ];
    }

    protected function resolveCheckAvailabilityMeta(): ?array
    {
        if (! is_plugin_active('hotel') || ! Route::has('public.rooms')) {
            return null;
        }

        return [
            'action_url' => route('public.rooms'),
            'date_format' => HotelHelper::getDateFormat(),
            'datepicker_format' => HotelHelper::getBookingFormDateFormat(),
            'minimum_adults' => HotelHelper::getMinimumNumberOfGuests(),
            'maximum_adults' => HotelHelper::getMaximumNumberOfGuests(),
            'default_children' => 0,
            'default_rooms' => 1,
        ];
    }

    protected function resolveLanguageCode(?string $value): ?string
    {
        if (! $value || ! Schema::hasTable('languages')) {
            return $value;
        }

        $language = DB::table('languages')
            ->select(['lang_code', 'lang_locale'])
            ->where('lang_code', $value)
            ->orWhere('lang_locale', $value)
            ->first();

        if ($language?->lang_code) {
            return $language->lang_code;
        }

        return match ($value) {
            'vi_VN' => 'vi',
            'ja_JP' => 'ja',
            default => $value,
        };
    }

    protected function resolveLanguageLocale(?string $languageCode): ?string
    {
        if (! $languageCode || ! Schema::hasTable('languages')) {
            return null;
        }

        return DB::table('languages')
            ->where('lang_code', $languageCode)
            ->value('lang_locale');
    }
}
