<?php

namespace Botble\Widget\Models;

use Botble\Base\Models\BaseModel;

class WidgetTranslation extends BaseModel
{
    protected $table = 'widgets_translations';
    protected $fillable = ['lang_code', 'widgets_id', 'data'];
    protected $casts = ['data' => 'json'];

    public function widget()
    {
        return $this->belongsTo(Widget::class, 'widgets_id');
    }
}