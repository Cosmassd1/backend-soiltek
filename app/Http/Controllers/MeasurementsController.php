<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MeasurementsTable;
use App\Models\SensorTable;
use App\Providers\SoilParameterChecker;

class MeasurementsController extends Controller
{
   
    public function getUserSoilData()
    {
        $data = DB::select("
            SELECT 
                u.user_id, 
                l.district, 
                MAX(CASE WHEN s.sensor_type = 'soilph' THEN m.measurement_value END) as soilph,
                MAX(CASE WHEN s.sensor_type = 'temperature' THEN m.measurement_value END) as soiltemperature,
                MAX(CASE WHEN s.sensor_type = 'moisture' THEN m.measurement_value END) as soilmoisture,
                MAX(CASE WHEN s.sensor_type = 'nitrogen' THEN m.measurement_value END) as soilnitrogen,
                MAX(CASE WHEN s.sensor_type = 'phosphorus' THEN m.measurement_value END) as soilphosphorus,
                MAX(CASE WHEN s.sensor_type = 'potassium' THEN m.measurement_value END) as soilpotassium
            FROM user_table as u
            INNER JOIN location_table as l ON u.user_id = l.user_id
            INNER JOIN sensor_table as s ON l.location_id = s.location_id
            INNER JOIN (
                SELECT sensor_id, measurement_value, created_at
                FROM measurements_table
                WHERE (sensor_id, created_at) IN (
                    SELECT sensor_id, MAX(created_at)
                    FROM measurements_table
                    GROUP BY sensor_id
                )
            ) as m ON s.sensor_id = m.sensor_id
            GROUP BY u.user_id, l.district
        ");
    
        return response()->json($data);
    }

    public function store(Request $request)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'sensor_id' => 'required|string|max:25',
        'measurement_value' => 'required|numeric',
    ]);

    // Fetch the sensor from the sensor table
    $sensor = SensorTable::where('sensor_id', $validatedData['sensor_id'])->first();

    if (!$sensor) {
        return response()->json(['error' => 'Sensor not found'], 404);
    }

    // Get days since planting
    $daysSincePlanting = DB::table('view_days_since_planting')
                            ->where('location_id', $sensor->location_id)
                            ->value('days_since_planting');

    // Check for threshold and status based on sensor type
    $parameterChecker = new SoilParameterChecker($sensor, $validatedData['measurement_value'], $daysSincePlanting);
    $result = $parameterChecker->checkParameters();

    // Append the threshold, status, and alert message to the validated data
    $validatedData = array_merge($validatedData, $result);

    // Create the measurement record in the database
    $measurement = MeasurementsTable::create($validatedData);

    // Return the created measurement with the alert message as a JSON response
    return response()->json($measurement, 201);
}


}
