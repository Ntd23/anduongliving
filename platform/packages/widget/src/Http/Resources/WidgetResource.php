<?php

namespace Botble\Widget\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Botble\Language\Facades\Language;

class WidgetResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = $request->query('locale', $request->query('lang', Language::getCurrentLocale()));
        
        $data = [
            'id' => $this->id,
            'widget_id' => $this->widget_id,
            'sidebar_id' => $this->sidebar_id,
            'position' => $this->position,
            'data' => $this->data, // Data đã chứa đúng ngôn ngữ theo theme
            'lang' => $locale,
        ];
        
        // Add menu_data if available
        if (isset($this->menu_data)) {
            $data['menu_data'] = $this->menu_data;
        }
        
        return $data;
    }
}