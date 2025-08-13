<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetsuiteDepartments extends Model
{
    protected $table = 'netsuite_departments';
    // protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'external_id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}
