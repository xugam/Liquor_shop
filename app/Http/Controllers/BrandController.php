<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandListResource;
use App\Http\Resources\ProductListResource;
use App\Models\Brand;
use App\Models\Product;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use ResponseTrait;
    /**
     * @OA\Get(
     *     path="/brands",
     *     summary="Get all brands",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brands retrieved successfully"
     *     )
     * )
     */
    public function index()
    {
        $brand = Brand::all();
        $data = BrandListResource::collection($brand);
        return $this->apiSuccess("Brand list", $data);
    }

    /**
     * @OA\Get(
     *     path="/brands/{id}",
     *     summary="Get a specific brand",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand retrieved successfully"
     *     )
     * )
     */
    public function show($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            $collection = ProductListResource::collection(Product::where('brand_id', $id)->get());
            $data = $collection->toArray(request());
            foreach ($data as &$item) {
                unset($item['brand']);
            }
            return $this->apiSuccess("Brand found successfully", $data);
        } else {
            return $this->apiError("Brand not found");
        }
    }

    /**
     * @OA\Post(
     *     path="/brands",
     *     summary="Create a new brand",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Brand created successfully"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // return $request->all();
        $request->validate([
            'name' => 'required|string|max:30',
        ]);
        $brand = Brand::create($request->all());
        if ($request->hasFile('image')) {
            $brand
                ->addMedia($request->file('image'))
                ->toMediaCollection('brand_images');
        }
        return $this->apiSuccess("Brand created successfully", $brand);
    }

    /**
     * @OA\Put(
     *     path="/brands/{id}",
     *     summary="Update a brand",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
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
     *     @OA\Response(
     *         response=200,
     *         description="Brand updated successfully"
     *     )
     * )
     */
    public function update(Brand $brand, Request $request)
    {
        if ($brand) {
            $brand->update($request->all());
            if ($request->hasFile('image')) {
                $brand->clearMediaCollection('brand_images');
                $brand
                    ->addMedia($request->file('image'))
                    ->toMediaCollection('brand_images');
            }
            return $this->apiSuccess("Brand updated successfully", $brand);
        } else {
            return $this->apiError("Brand not found");
        }
    }

    /**
     * @OA\Delete(
     *     path="/brands/{id}",
     *     summary="Delete a brand",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            $brand->delete();
            return $this->apiSuccess("Brand deleted successfully");
        } else {
            return $this->apiError("Brand not found");
        }
    }
}
