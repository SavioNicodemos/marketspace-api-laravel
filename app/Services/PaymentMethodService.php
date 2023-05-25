<?php

namespace App\Services;

use App\Models\PaymentMethod;

class PaymentMethodService
{
    public function getIdsByKeys(array $keys): array
    {
        $paymentMethodIds = PaymentMethod::whereIn('payment_methods.key', $keys)
            ->get('payment_methods.*')
            ->pluck('id')
            ->toArray();

        return $paymentMethodIds;
    }
}
