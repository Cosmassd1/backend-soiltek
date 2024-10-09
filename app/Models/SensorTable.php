<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorTable extends Model
{
    use HasFactory;

    protected $table ='sensor_table';
    protected $primaryKey = 'sensor_id';
    public $timestamps = true;

    protected $fillable = [
    'location_id', 
    'sensor_type', 
    'sensor_location', 
    'installation_date'
];
}
