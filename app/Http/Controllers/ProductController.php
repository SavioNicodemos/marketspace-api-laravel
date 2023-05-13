<?php

namespace App\Http\Controllers;

use App\Http\Requests\{ListNotMyProductsRequest, StoreProductRequest, UpdateProductRequest};
use App\Models\Product;
use App\Services\ProductService;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Throwable;

class ProductController extends Controller
{
    use ApiResponser;

    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListNotMyProductsRequest $request)
    {
        return $this->successResponse($this->productService->listNotMyProducts($request->validated()));
    }

    /**
     * Store a newly created resource in storage.
     * @throws Throwable
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        return response()->json($this->productService->create($request), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        return $this->successResponse($this->productService->findOneById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $productId): JsonResponse
    {
        return $this->successResponse($this->productService->delete($productId), 204);
    }
}
