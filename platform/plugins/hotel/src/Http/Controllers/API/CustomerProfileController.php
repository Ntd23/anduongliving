<?php

namespace Botble\Hotel\Http\Controllers\API;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Services\CustomerAuthService;
use Illuminate\Http\Request;

class CustomerProfileController extends BaseController
{
    public function __construct(protected CustomerAuthService $customerAuthService)
    {
    }

    public function me(Request $request, BaseHttpResponse $response)
    {
        $customer = $request->user();

        return $response->setData($this->customerAuthService->transformCustomer($customer));
    }
}
