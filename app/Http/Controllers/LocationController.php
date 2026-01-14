<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $locations = Location::all();
        return $this->apiSuccess("Location list", $locations);
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
        return $this->apiSuccess("Location created successfully", $location);
    }

    public function update(Location $location, Request $request)
    {
        if ($location) {
            $location->update($request->all());
            return $this->apiSuccess("Location updated successfully", $location);
        } else {
            return $this->apiError("Location not found");
        }
    }

    public function destroy(Location $location)
    {
        if ($location) {
            $location->delete();
            return $this->apiSuccess("Location deleted successfully");
        } else {
            return $this->apiError("Location not found");
        }
    }
}
