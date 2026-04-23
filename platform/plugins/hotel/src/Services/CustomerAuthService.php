<?php

namespace Botble\Hotel\Services;

use Botble\Base\Facades\BaseHelper;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Models\Customer;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Hash;

class CustomerAuthService
{
    public function register(array $attributes): array
    {
        do_action('customer_register_validation', request());

        $customer = Customer::query()->forceCreate([
            'first_name' => BaseHelper::clean($attributes['first_name'] ?? ''),
            'last_name' => BaseHelper::clean($attributes['last_name'] ?? ''),
            'email' => BaseHelper::clean(mb_strtolower((string) ($attributes['email'] ?? ''))),
            'password' => Hash::make((string) ($attributes['password'] ?? '')),
        ]);

        event(new Registered($customer));

        if (HotelHelper::isEnableEmailVerification()) {
            return [
                'customer' => $customer,
                'token' => null,
                'message' => trans('plugins/hotel::customer.verification_email_sent'),
            ];
        }

        $customer->confirmed_at = Carbon::now();
        $customer->save();

        return [
            'customer' => $customer->fresh() ?: $customer,
            'token' => $this->issueToken($customer),
            'message' => trans('plugins/hotel::customer.registered_successfully'),
        ];
    }

    public function login(string $email, string $password): array
    {
        $guard = $this->guard();
        $credentials = [
            'email' => mb_strtolower(trim($email)),
            'password' => $password,
        ];

        if (! $guard->validate($credentials)) {
            return [
                'customer' => null,
                'token' => null,
                'message' => __('Email or password is not correct!'),
                'code' => 422,
            ];
        }

        /** @var Customer|null $customer */
        $customer = $guard->getLastAttempted();

        if (! $customer) {
            return [
                'customer' => null,
                'token' => null,
                'message' => __('Email or password is not correct!'),
                'code' => 422,
            ];
        }

        if (HotelHelper::isEnableEmailVerification() && empty($customer->confirmed_at)) {
            return [
                'customer' => null,
                'token' => null,
                'message' => trans('plugins/hotel::customer.email_not_confirmed', [
                    'resend_link' => route('customer.resend_confirmation', ['email' => $customer->email]),
                ]),
                'code' => 422,
            ];
        }

        return [
            'customer' => $customer->fresh() ?: $customer,
            'token' => $this->issueToken($customer),
            'message' => null,
            'code' => 200,
        ];
    }

    public function transformCustomer(Customer $customer): array
    {
        $customer = $customer->fresh() ?: $customer;
        $confirmedAt = $customer->confirmed_at;

        if (is_string($confirmedAt) && $confirmedAt !== '') {
            $confirmedAt = Carbon::parse($confirmedAt);
        }

        return [
            'id' => $customer->getKey(),
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'name' => $customer->name,
            'email' => $customer->email,
            'avatar_url' => $customer->avatar_url,
            'confirmed_at' => $confirmedAt instanceof DateTimeInterface ? $confirmedAt->format(DateTimeInterface::ATOM) : null,
        ];
    }

    protected function issueToken(Customer $customer): string
    {
        return $customer->createToken('customer-auth')->plainTextToken;
    }

    protected function guard(): Guard
    {
        return auth('customer');
    }
}
