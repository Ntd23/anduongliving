<?php

namespace Botble\Hotel\Http\Controllers\API;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;

class CustomerHeaderExtrasController extends BaseController
{
    public function index(BaseHttpResponse $response)
    {
        $currentCurrencyId = get_application_currency_id();
        $currencies = get_all_currencies()
            ->map(fn ($currency) => [
                'title' => $currency->title,
                'href' => route('public.change-currency', $currency->title),
                'active' => (string) $currency->getKey() === (string) $currentCurrencyId,
            ])
            ->values()
            ->all();

        return $response->setData([
            'currencies' => $currencies,
        ]);
    }
}
