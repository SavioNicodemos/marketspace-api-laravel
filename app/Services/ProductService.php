<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Throwable;
use Exception;

class ProductService
{

    /**
     * @throws Throwable
     */
    public function create($request): Product|null
    {
        DB::beginTransaction();
        try {
            $product = new Product();
            $product->name = $request['name'];
            $product->description = $request['description'];
            $product->is_new = $request['is_new'];
            $product->price = $request['price'];
            $product->accept_trade = $request['accept_trade'];
            if (!empty(auth()->user()->id)) {
                $product->user_id = auth()->user()->id;
            }
            $product->is_active = true;

            $product->save();

            $paymentMethodService = new PaymentMethodService();
            $paymentMethodsIds = $paymentMethodService->getIdsByKeys($request['payment_methods']);

            $product->paymentMethods()->sync($paymentMethodsIds);
            DB::commit();

            return $product;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @throws NotFoundException
     */
    public function findOneById(string $productId): array
    {
        $product = Product::with([
            'user:id,name,tel',
            'paymentMethods:key,name',
            'user.image:imageable_id,name',
            'productImages' => function ($query) {
                return $query->select(['id', 'name as path', 'imageable_id']);
            }
        ])->find($productId);

        if (!$product) {
            throw new NotFoundException('Product not found');
        }

        $product = $product->toArray();

        $product['user']['avatar'] = $product['user']['image']['name'];
        unset($product['user']['image']);
        unset($product['user']['id']);

        return $product;
    }
}
