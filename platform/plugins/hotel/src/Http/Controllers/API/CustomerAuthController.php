<?php

namespace Botble\Hotel\Http\Controllers\API;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Http\Requests\Fronts\Auth\LoginRequest;
use Botble\Hotel\Http\Requests\Fronts\Auth\RegisterRequest;
use Botble\Hotel\Services\CustomerAuthService;
use Illuminate\Http\Request;

class CustomerAuthController extends BaseController
{
    public function __construct(protected CustomerAuthService $customerAuthService)
    {
    }

    public function register(RegisterRequest $request, BaseHttpResponse $response)
    {
        $result = $this->customerAuthService->register($request->validated());

        return $response
            ->setData([
                'token' => $result['token'],
                'customer' => $this->customerAuthService->transformCustomer($result['customer']),
            ])
            ->setMessage($result['message']);
    }

    public function login(LoginRequest $request, BaseHttpResponse $response)
    {
        $result = $this->customerAuthService->login(
            $request->string('email')->trim()->value(),
            (string) $request->input('password')
        );

        if (! $result['customer'] || ! $result['token']) {
            return $response
                ->setError()
                ->setCode($result['code'] ?? 422)
                ->setMessage($result['message'] ?: __('Email or password is not correct!'));
        }

        return $response
            ->setData([
                'token' => $result['token'],
                'customer' => $this->customerAuthService->transformCustomer($result['customer']),
            ])
            ->setMessage($result['message']);
    }

    public function logout(Request $request, BaseHttpResponse $response)
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return $response->setMessage(__('You have been successfully logged out!'));
    }
}
