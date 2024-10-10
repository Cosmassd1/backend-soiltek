<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MeasurementsTable;
use App\Models\SensorTable;

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

    // Fetch the sensor type from the sensor table
    $sensor = SensorTable::where('sensor_id', $validatedData['sensor_id'])->first();

    if (!$sensor) {
        return response()->json(['error' => 'Sensor not found'], 404);
    }

    $sensorType = $sensor->sensor_type;
    $measurementValue = $validatedData['measurement_value'];

    // Initialize status and threshold
    $status = 'normal';
    $threshold = 'off'; // Default threshold is off
    $alertMessage = '';

    // Check if the sensor type is temperature
    if ($sensorType === 'temperature') {
        // Get days since planting
        $daysSincePlanting = DB::table('view_days_since_planting')
                                ->where('location_id', $validatedData['sensor_id']) // Adjust if needed
                                ->value('days_since_planting'); // Ensure your view returns the days

        // Set temperature ranges based on planting days
        if ($daysSincePlanting > 30) {
            if ($measurementValue < 15) {
                $status = 'low';
                $threshold = 'on';
                $alertMessage = 'Low soil temperature on your farm.';
            } elseif ($measurementValue > 32) {
                $status = 'high';
                $threshold = 'on';
                $alertMessage = 'High soil temperature on your farm.';
            }
        } else {
            if ($measurementValue < 17) {
                $status = 'low';
                $threshold = 'on';
                $alertMessage = 'Low soil temperature on your farm.';
            } elseif ($measurementValue > 25) {
                $status = 'high';
                $threshold = 'on';
                $alertMessage = 'High soil temperature on your farm.';
            }
        }
    }

    // Append the threshold, status, and alert message to the validated data
    $validatedData['threshold'] = $threshold;
    $validatedData['status'] = $status;
    $validatedData['alert_message'] = $alertMessage;

    // Create the measurement record in the database
    $measurement = MeasurementsTable::create($validatedData);

    // Return the created measurement with the alert message as a JSON response
    return response()->json($measurement, 201);
}

public function getAlert($user_id)
{
    // Fetch all the sensors associated with the user's locations
    $sensors = SensorTable::whereHas('location', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id); // Adjust based on actual column name
                    })->get();

    if ($sensors->isEmpty()) {
        return response()->json(['error' => 'No sensors found for this user'], 404);
    }

    // Initialize an array to store alerts
    $alerts = [];

    // Loop through each sensor and check for alerts in measurements
    foreach ($sensors as $sensor) {
        // Fetch the latest measurement for each sensor
        $measurement = MeasurementsTable::where('sensor_id', $sensor->sensor_id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();

        // If there are no measurements for this sensor, skip
        if (!$measurement) {
            continue;
        }

        // Check the status and create an alert message accordingly
        if ($measurement->status === 'high') {
            $alerts[] = [
                'sensor_id' => $sensor->sensor_id,
                'alert' => 'High soil temperature on your farm.'
            ];
        } elseif ($measurement->status === 'low') {
            $alerts[] = [
                'sensor_id' => $sensor->sensor_id,
                'alert' => 'Low soil temperature on your farm.'
            ];
        }
    }

    // If no alerts are found
    if (empty($alerts)) {
        return response()->json(['alert' => 'No alerts. Soil temperature is normal for all sensors.'], 200);
    }

    // Return all alerts
    return response()->json(['alerts' => $alerts], 200);
}



    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
