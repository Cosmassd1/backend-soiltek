<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasurementsTable extends Model
{
    use HasFactory;
    protected $table = 'measurements_table';
    protected $primaryKey = 'measurements_id';
    public $timestamps = true;

    protected $fillable = [
          'sensor_id',
          'threshold',
           'measurement_value',  
            'status'
        ];
}