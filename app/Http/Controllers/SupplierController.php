<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierListResource;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $supplier = Supplier::all();
        $data = SupplierListResource::collection($supplier);
        return response()->json($data, 200);
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
        return response()->json($supplier, 201);
    }

    public function update(Supplier $supplier, Request $request)
    {
        if ($supplier) {
            $supplier->update($request->all());
            return response()->json($supplier, 200);
        } else {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier) {
            $supplier->delete();
            return response()->json(['message' => 'Supplier deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
    }
}
