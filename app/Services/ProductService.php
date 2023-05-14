<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\NotAuthorizedException;
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
            throw new NotFoundException('Product');
        }

        $product = $product->toArray();

        $product['user']['avatar'] = $product['user']['image']['name'];
        unset($product['user']['image']);
        unset($product['user']['id']);

        return $product;
    }

    /**
     * @throws NotFoundException
     * @throws NotAuthorizedException
     */
    public function delete(string $productId): bool
    {
        $product = Product::find($productId);

        if (!$product) {
            throw new NotFoundException('Product');
        }
        if ($product->user_id !== auth()->user()->id) {
            throw new NotAuthorizedException('Product');
        }

        $product->delete();

        return true;
    }

    /**
     * @throws Throwable
     * @throws NotFoundException
     */
    public function update(array $filters, string $productId): bool
    {
        $product = Product::find($productId);
        if (!$product) {
            throw new NotFoundException('Product');
        }
        if ($product->user_id !== auth()->user()->id) {
            throw new NotAuthorizedException('Product');
        }
        DB::beginTransaction();
        try {
            $product->name = $filters['name'] ?? $product->name;
            $product->description = $filters['description'] ?? $product->description;
            $product->price = $filters['price'] ?? $product->price;
            $product->is_new = $filters['is_new'] ?? $product->is_new;
            $product->accept_trade = $filters['accept_trade'] ?? $product->accept_trade;
            $product->is_active = $filters['is_active'] ?? $product->is_active;

            $product->save();

            if (isset($filters['payment_methods']) && !!count($filters['payment_methods'])) {
                $paymentMethodService = new PaymentMethodService();
                $paymentMethodsIds = $paymentMethodService->getIdsByKeys($filters['payment_methods']);

                $product->paymentMethods()->sync($paymentMethodsIds);
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function listNotMyProducts(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('user_id', '!=', auth()->user()->id)
            ->where('is_active', true)
            ->where(function (Builder $query) use ($filters) {
                if (isset($filters['is_new'])) {
                    $query->where('is_new', $filters['is_new']);
                }
                if (isset($filters['accept_trade'])) {
                    $query->where('accept_trade', $filters['accept_trade']);
                }
                if (isset($filters['query'])) {
                    $query->where('name', 'LIKE', '%' . $filters['query'] . '%');
                }
            })
            ->whereHas('paymentMethods', function (Builder $paymentsQuery) use ($filters) {
                $hasPaymentMethodsFilter = isset($filters['payment_methods']) && !!count($filters['payment_methods']);
                if ($hasPaymentMethodsFilter) {
                    $paymentsQuery->whereIn('key', $filters['payment_methods']);
                }
            })
            ->with([
                'user:id,name,tel',
                'paymentMethods:key,name',
                'user.image:imageable_id,name',
                'productImages' => function ($query) {
                    return $query->select(['id', 'name as path', 'imageable_id']);
                }
            ])
            ->get(['id', 'name', 'price', 'is_new', 'accept_trade', 'user_id']);
    }

    public function getMyProducts(array $filters)
    {
        return Product::where('user_id', auth()->user()->id)
            ->where(function (Builder $query) use ($filters) {
                if (isset($filters['is_active'])) {
                    $query->where('is_active', $filters['is_active']);
                }
            })
            ->with([
                'paymentMethods:key,name',
                'productImages' => function ($query) {
                    return $query->select(['id', 'name as path', 'imageable_id']);
                }
            ])
            ->get();
    }

    /**
     * @throws Throwable
     */
    public function saveProductImages(array $filters)
    {
        $productId = $filters['product_id'];
        $images = $filters['images'];

        DB::beginTransaction();
        try {
            $product = Product::find($productId);
            if (!$product) {
                throw new NotFoundException('Product');
            }
            if ($product->user_id !== auth()->user()->id) {
                throw new NotAuthorizedException('Product');
            }

            $imageService = new ImageService();

            $imagesObjects = [];
            foreach ($images as $image) {
                $imageObject = $imageService->storeImage($image, 'products');
                $imagesObjects[] = new Image($imageObject);
            }

            $product->productImages()->saveMany($imagesObjects);

            DB::commit();
            return Image::where('imageable_id', $productId)->get();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
