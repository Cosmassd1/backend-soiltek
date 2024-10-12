<?php

namespace App\Providers;

class SoilParameterChecker
{
    protected $sensor;
    protected $measurementValue;
    protected $daysSincePlanting;

    public function __construct($sensor, $measurementValue, $daysSincePlanting)
    {
        $this->sensor = $sensor;
        $this->measurementValue = $measurementValue;
        $this->daysSincePlanting = $daysSincePlanting;
    }

    public function checkParameters()
    {
        $sensorType = $this->sensor->sensor_type;
        $status = 'normal';
        $threshold = 'off';
        $alertMessage = '';

        switch ($sensorType) {
            case 'temperature':
                return $this->checkTemperature();
            case 'phosphorus':
                return $this->checkPhosphorus();
            case 'potassium':
                return $this->checkPotassium();
            case 'nitrogen':
                return $this->checkNitrogen();
            case 'soilph':
                return $this->checkSoilPH();
            case 'moisture':
                return $this->checkMoisture();
            default:
                return compact('status', 'threshold', 'alertMessage');
        }
    }

    private function checkTemperature()
    {
        if ($this->daysSincePlanting > 30) {
            if ($this->measurementValue < 15) {
                return $this->generateAlert('low', 'Low soil temperature on your farm.');
            } elseif ($this->measurementValue > 32) {
                return $this->generateAlert('high', 'High soil temperature on your farm.');
            }
        } else {
            if ($this->measurementValue < 17) {
                return $this->generateAlert('low', 'Low soil temperature on your farm.');
            } elseif ($this->measurementValue > 25) {
                return $this->generateAlert('high', 'High soil temperature on your farm.');
            }
        }

        return $this->generateNormal();
    }

    // Define similar functions for other parameters (phosphorus, potassium, nitrogen, etc.)
    private function checkPhosphorus()
    {
        // Define phosphorus threshold logic here
        return $this->generateNormal(); // Or generateAlert() based on logic
    }

    private function checkPotassium()
    {
        // Define potassium threshold logic here
        return $this->generateNormal(); // Or generateAlert() based on logic
    }

    private function checkNitrogen()
    {
        // Define nitrogen threshold logic here
        return $this->generateNormal(); // Or generateAlert() based on logic
    }

    private function checkSoilPH()
    {
        // Define soil pH threshold logic here
        return $this->generateNormal(); // Or generateAlert() based on logic
    }

    private function checkMoisture()
    {
        // Define moisture threshold logic here
        return $this->generateNormal(); // Or generateAlert() based on logic
    }

    private function generateAlert($status, $message)
    {
        return [
            'threshold' => 'on',
            'status' => $status,
            'alert_message' => $message,
        ];
    }

    private function generateNormal()
    {
        return [
            'threshold' => 'off',
            'status' => 'normal',
            'alert_message' => '',
        ];
    }
}
