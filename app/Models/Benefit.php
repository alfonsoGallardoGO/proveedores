<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Benefit extends Model
{
    use HasFactory;

    protected $table = 'benefits'; 

    protected $fillable = [
        'id',
        'name',
        'description',
        'conditioned',
        'each',
        'type',
        'conditioned_efficiency',
        'conditioned_seniority',
        'efficiency_rules',
        'day_cutoff',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'conditioned' => 'boolean',
        'each' => 'boolean',
        'conditioned_efficiency' => 'boolean',
        'conditioned_seniority' => 'boolean',
        'efficiency_rules' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'day_cutoff' => 'integer',
    ];


}
