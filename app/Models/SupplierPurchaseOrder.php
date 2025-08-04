<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseOrder extends Model
{
    protected $table = 'supplier_purchase_orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'supplier_external_id',
        'rfc',
        'status',
        'date',
        'data' => 'array',
        'purchase_order_id',
        'purchase_order',
    ];

    protected $dates = ['deleted_at'];


    public function items()
    {
        return $this->hasMany(SupplierPurchaseOrderItem::class, 'supplier_purchase_order_id', 'id');
    }

}
