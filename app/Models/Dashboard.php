<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    protected $table = 'supplier_purchase_orders';

    protected $fillable = [
        'id',
        'supplier_external_id',
        'rfc',
        'status',
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
        'purchase_order_id',
        'purchase_order',
        'total',
        'subtotal',
        'impuesto'
    ];
}
