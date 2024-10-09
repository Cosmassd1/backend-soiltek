<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTable extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'user_table';

    // Specify the primary key
    protected $primaryKey = 'user_id';

    public $timestamps = true; // Default

    // Specify mass assignable fields
    protected $fillable = [
        'national_id',
        'first_name',
        'middle_name',
        'surname',
        'phone_number',
        'email',
        'password',
    ];

    public function location()
    {
        return $this->hasOne(LocationTable::class, 'user_id', 'user_id');
    }
}

