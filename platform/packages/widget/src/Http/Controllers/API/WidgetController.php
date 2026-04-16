<?php

namespace Botble\Widget\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Language\Facades\Language;
use Botble\Widget\Http\Resources\WidgetResource;
use Botble\Widget\Models\Widget;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;

class WidgetController extends BaseApiController
{
    /**
     * Get widgets by sidebar
     * @group Widget
     * @urlParam sidebarId The sidebar ID (e.g., footer_sidebar)
     */
    public function getBySidebar(Request $request, string $sidebarId)
    {
        $lang = $request->query('lang') ?? Language::getCurrentLocale();
        $widgets = Widget::query()
            ->where('theme', Theme::getThemeName())
            ->where('sidebar_id', $sidebarId)
            ->with(['translations' => fn($q) => $q->where('lang_code', $lang)])
            ->orderBy('position')
            ->get();

        return $this
            ->httpResponse()
            ->setData(WidgetResource::collection($widgets))
            ->toApiResponse();
    }

    /**
     * Get all widgets
     * @group Widget
     * @queryParam sidebar_id Filter by sidebar ID
     * @queryParam theme Filter by theme
     * @queryParam lang Filter by language code (vi, en_US, ja...)
     */
    public function index(Request $request)
    {
        $lang = $request->query('lang') ?? Language::getCurrentLocale();

        $query = Widget::query();

        if ($request->has('sidebar_id')) {
            $query->where('sidebar_id', $request->query('sidebar_id'));
        }

        if ($request->has('theme')) {
            $query->where('theme', $request->query('theme'));
        } else {
            $query->where('theme', Theme::getThemeName());
        }

        $widgets = $query
            ->with('translations')
            ->orderBy('position')
            ->get()
            ->map(function ($widget) use ($lang) {

                $translation = $widget->translations
                    ->firstWhere('lang_code', $lang);

                // fallback nếu không có hoặc rỗng
                if (!$translation || empty($translation->data)) {
                    $translation = $widget->translations
                        ->firstWhere('lang_code', Language::getDefaultLocale());
                }

                $widget->data = $translation?->data ?? $widget->data;

                return $widget;
            });

        return $this
            ->httpResponse()
            ->setData(WidgetResource::collection($widgets))
            ->toApiResponse();
    }
}
