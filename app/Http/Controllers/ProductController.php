<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductStoreRequest;
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
        $request->validated();
        DB::beginTransaction();
        try {
            $product = Product::create($request->validated());
            foreach ($product->units as $unit) {
                $unit = ProductUnit::create($unit);
            }
            if ($request->hasFile('image')) {
                $product->addMedia($request->file('image'))
                    ->toMediaCollection('product_images');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->apiError("Product not created");
        }

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
    public function show($id)
    {
        $product = Product::find($id);
        if ($product) {
            return $this->apiSuccess("Product found successfully", $product);
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
}
