<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierPurchaseOrdersReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'supplier_purchase_orders_receipt';
    protected $fillable = [
        'external_id',
        'tranid',
        'date',
        'vendor',
        'purchase_order',
        'status',
        'quantity',
        'url',
    ];

    protected $casts = [
        'external_id' => 'integer',
        'date'        => 'date:Y-m-d',
        'quantity'    => 'decimal:2',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

}
