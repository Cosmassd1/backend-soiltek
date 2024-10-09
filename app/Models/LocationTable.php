<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationTable extends Model
{
    use HasFactory;
    // Specify the table name
    protected $table = 'location_table';

    // Specify the primary key
    protected $primaryKey = 'location_id';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'district',
        'physical_address',
        'description',
        'region'
    ];



    public function user()
    {
        return $this->belongsTo(UserTable::class, 'user_id', 'user_id');
    }
}
