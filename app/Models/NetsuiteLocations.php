<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetsuiteLocations extends Model
{
    protected $table = 'netsuite_locations';
    // protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'external_id',
        'name',
        'city',
        'estate',
        'country',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
