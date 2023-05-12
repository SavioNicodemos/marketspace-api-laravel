<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductService
{

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $product = new Product();
            $product->name = $request['name'];
            $product->description = $request['description'];
            $product->is_new = $request['is_new'];
            $product->price = $request['price'];
            $product->accept_trade = $request['accept_trade'];
            $product->user_id = auth()->user()->id;
            $product->is_active = true;

            $product->save();

            $paymentMethodService = new PaymentMethodService();
            $paymentMethodsIds = $paymentMethodService->getIdsByKeys($request['payment_methods']);

            $product->paymentMethods()->sync($paymentMethodsIds);
            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
}
