<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierWhatsapp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'supplier_whatsapp';

    protected $primaryKey = 'id';

    protected $fillable = [
        'reception_id',
        'external_supplier_id',
        'supplier_name',
        'date',
        'phone',
        'pdf_base_64',
        'pdf_rute',
    ];

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
