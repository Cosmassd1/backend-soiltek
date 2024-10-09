<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorTable;

class SensorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    
        $sensors = SensorTable::all();

        if ($sensors->isEmpty()) {
            return response()->json(['message' => 'No sensor found.'], 404);
        }

        // Return a JSON response
        return response()->json($sensors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
