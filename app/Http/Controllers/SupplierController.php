<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierListResource;
use App\Models\Supplier;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $supplier = Supplier::all();
        $data = SupplierListResource::collection($supplier);
        return $this->apiSuccess("Supplier list", $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);
        $supplier = Supplier::create($request->all());
        return $this->apiSuccess("Supplier created successfully", $supplier);
    }

    public function update(Supplier $supplier, Request $request)
    {
        if ($supplier) {
            $supplier->update($request->all());
            return $this->apiSuccess("Supplier updated successfully", $supplier);
        } else {
            return $this->apiError("Supplier not found");
        }
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier) {
            $supplier->delete();
            return $this->apiSuccess("Supplier deleted successfully");
        } else {
            return $this->apiError("Supplier not found");
        }
    }
}
