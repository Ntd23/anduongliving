<?php

namespace Botble\Widget\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Botble\Language\Facades\Language;
class WidgetResource extends JsonResource
{
    public function toArray($request): array
    {
        $lang = $request->query('lang') ?? Language::getCurrentLocale();
        
        // Lấy translation nếu có
        return [
            'id' => $this->id,
            'widget_id' => $this->widget_id,
            'sidebar_id' => $this->sidebar_id,
            'position' => $this->position,
            'data' => $this->translations->first()?->data ?? $this->data,
            'lang' => $lang,
        ];
    }
}