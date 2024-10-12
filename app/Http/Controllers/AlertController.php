<?php

namespace App\Http\Controllers;

use App\Models\SensorTable;
use App\Models\MeasurementsTable;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function getAlert($user_id)
    {
        // Fetch all sensors associated with the user's locations
        $sensors = SensorTable::whereHas('location', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        })->get();

        if ($sensors->isEmpty()) {
            return response()->json(['error' => 'No sensors found for this user'], 404);
        }

        $alerts = [];

        // Loop through each sensor and fetch the latest measurement
        foreach ($sensors as $sensor) {
            $measurement = MeasurementsTable::where('sensor_id', $sensor->sensor_id)
                                            ->orderBy('created_at', 'desc')
                                            ->first();

            if (!$measurement) {
                continue;
            }

            // Check the status for alerts
            if ($measurement->status !== 'normal') {
                $alerts[] = [
                    'sensor_id' => $sensor->sensor_id,
                    'alert' => ucfirst($measurement->status) . " soil " . $sensor->sensor_type . " on your farm.",
                ];
            }
        }

        if (empty($alerts)) {
            return response()->json(['alert' => 'No alerts. All soil parameters are normal.'], 200);
        }

        return response()->json(['alerts' => $alerts], 200);
    }
}

