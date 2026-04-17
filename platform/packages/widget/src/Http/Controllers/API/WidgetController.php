<?php

namespace Botble\Widget\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Language\Facades\Language;
use Botble\Widget\Http\Resources\WidgetResource;
use Botble\Widget\Models\Widget;
use Botble\Menu\Models\Menu;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
class WidgetController extends BaseApiController
{

    public function index(Request $request)
    {
        $query = Widget::query();

        if ($request->has('sidebar_id')) {
            $query->where('sidebar_id', $request->query('sidebar_id'));
        }

        $locale = $request->input('locale', $request->input('lang'));
        
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
        $locale = $request->input('lang', $request->input('lang'));
        $themeName = Widget::getThemeName($locale);
        
        $widgets = Widget::query()
            ->where('theme', $themeName)
            ->where('sidebar_id', $sidebarId)
            ->orderBy('position')
            ->get();

        // Fetch menu data for widgets that have menu_id
        $widgets->each(function ($widget) use ($themeName) {
            if (isset($widget->data['menu_id']) && !empty($widget->data['menu_id'])) {
                $menuId = $widget->data['menu_id'];
                
                // Try to find menu by slug first, then by ID
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
        });

        return $this
            ->httpResponse()
            ->setData(WidgetResource::collection($widgets))
            ->toApiResponse();
    }
}