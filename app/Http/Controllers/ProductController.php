<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUnitStoreRequest;
use App\Http\Resources\ProductListResource;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Traits\PaginationTrait;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ResponseTrait;
    use PaginationTrait;
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="Get all products",
     * tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     */

    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $product = Product::where('name', 'like', "%{$search}%")->paginate($this->perPage($request));
        } else {
            $product = Product::paginate($this->perPage($request));
        }
        if (!$product->isEmpty()) {
            $data = ProductListResource::collection($product);
            return $this->apiSuccess("Product list", $data);
        } else {
            return $this->apiError("Product not found");
        }
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create a new product",
     * tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="image",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="file")
     *     ),
     *     @OA\Parameter(
     *         name="brand_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="base_unit_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sku",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *     )
     * )
     */

    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $product = Product::create($data);

            if ($request->hasFile('image')) {
                $product->addMedia($request->file('image'))
                    ->toMediaCollection('product_images');
            }
            foreach ($data['units'] as $unit) {
                $unit['product_id'] = $product->id;
                $unit = ProductUnit::create($unit);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->apiError("Product not created");
        }
        $product = ProductListResource::make($product);
        return $this->apiSuccess("Product created successfully", $product);
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Get a product by ID",
     * tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product found successfully",
     *     )
     * )
     */
    public function show(Product $product)
    {
        if ($product) {
            $data = ProductListResource::make($product);
            return $this->apiSuccess("Product found successfully", $data);
        } else {
            return $this->apiError("Product not found");
        }
    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     summary="Update a product by ID",
     * tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="image",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="file")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *     )
     * )
     */
    public function update(Product $product, Request $request)
    {
        if ($product) {
            $product->update($request->all());
            return $this->apiSuccess("Product updated successfully", $product);
        } else {
            return $this->apiError("Product not found");
        }
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     summary="Delete a product by ID",
     * tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *     )
     * )
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return $this->apiSuccess("Product deleted successfully");
        } else {
            return $this->apiError("Product not found");
        }
    }
    public function getStock($id)
    {
        $product = Product::find($id);
        if ($product) {
            $stock = $product->stock;
            return $this->apiSuccess("Product found successfully", $stock);
        } else {
            return $this->apiError("Product not found");
        }
    }

    public function storeUnit(ProductUnitStoreRequest $request, Product $product)
    {
        // return $product;
        $data = $request->validated();
        $data['product_id'] = $product->id;
        return $product->units()->create($data);
        return $this->apiSuccess("Unit added successfully");
    }

    public function updateUnits(Request $request, Product $product, $unit)
    {
        return $request->all();
        $unit = $product->units()->find($unit);
        if (request()->has('name')) {
            $unit = $unit->update($request->validated());
        } elseif (request()->has('conversion_factor')) {
            $unit = $unit->update($request->validated());
        } elseif (request()->has('cost_price')) {
            $unit = $unit->update($request->validated());
        } elseif (request()->has('selling_price')) {
            $unit = $unit->update($request->validated());
        } else {
            return $this->apiError("Invalid request");
        }
        return $this->apiSuccess("Unit updated successfully", $unit);
    }

    public function destroyUnit(Product $product, $unit)
    {
        $unit = $product->units()->find($unit);
        if ($unit) {
            $unit->delete();
            return $this->apiSuccess("Unit deleted successfully");
        } else {
            return $this->apiError("Unit not found");
        }
    }
}
