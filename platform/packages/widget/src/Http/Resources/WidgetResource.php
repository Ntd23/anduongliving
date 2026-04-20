<?php

namespace Botble\Widget\Http\Resources;

use Botble\Language\Facades\Language;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'data' => $this->data,
            'lang' => $locale,
        ];

        if (isset($this->menu_data)) {
            $data['menu_data'] = $this->menu_data;
        }

        if (isset($this->rendered_html)) {
            $data['rendered_html'] = $this->rendered_html;
        }

        if (isset($this->meta)) {
            $data['meta'] = $this->meta;
        }

        return $data;
    }
}
