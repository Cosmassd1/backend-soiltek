<?php

namespace App\Http\Controllers;

use App\Models\LocationTable;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all users from the 'user_table'
        $location = LocationTable::all();

        if ($location->isEmpty()) {
            return response()->json(['message' => 'location not found.'], 404);
        }

        // Return a JSON response
        return response()->json($location);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|string|max:25',
            'district' => 'required|string|max:65',
            'physical_address' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'region' => 'required|string|max:20'
        ]);

        // Create the user
        $location = locationTable::create($validatedData);

        // Return a JSON response with the created user
        return response()->json($location, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
