<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierPurchaseOrder extends Model
{
    use SoftDeletes;
    protected $table = 'supplier_purchase_orders';
    protected $primaryKey = 'id';

    //campos cambiados

    protected $fillable = [
        'supplier_external_id',
        'rfc',
        'status',
        'date',
        'data' => 'array',
        'purchase_order_id',
        'purchase_order',
        'total',
        'subtotal',
        'impuesto'
    ];

    protected $dates = ['deleted_at'];


    public function items()
    {
        return $this->hasMany(SupplierPurchaseOrderItem::class, 'supplier_purchase_order_id', 'id');
    }

}
