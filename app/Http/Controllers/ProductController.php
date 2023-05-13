<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
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
    public function index()
    {
        //
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
        try {
            return $this->successResponse($this->productService->findOneById($id));
        } catch (Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
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
    public function destroy(Product $product)
    {
        //
    }
}
