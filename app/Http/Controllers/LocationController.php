<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all();
        return response()->json($locations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'additional_info' => 'nullable|string',
        ]);
        $location = Location::create($request->all());
        return response()->json($location);
    }

    public function update(Location $location, Request $request)
    {
        if ($location) {
            $location->update($request->all());
            return response()->json($location);
        } else {
            return response()->json(['message' => 'Location not found'], 404);
        }
    }

    public function destroy(Location $location)
    {
        if ($location) {
            $location->delete();
            return response()->json(['message' => 'Location deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Location not found'], 404);
        }
    }
}
