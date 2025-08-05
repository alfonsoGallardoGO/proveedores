<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierPurchaseOrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'supplier_purchase_orders_items';

    protected $primaryKey = 'id';


    public $timestamps = false;

    protected $fillable = [
        'supplier_purchase_order_id',
        'article_order_id',
        'description',
        'quantity',
        'amount',
        'class',
        'rate_tax',
        'department',
        'location',
        'account',
        'categoria',
        'memo',
        'type',
    ];

    public function order()
    {
        return $this->belongsTo(SupplierPurchaseOrder::class, 'supplier_purchase_order_id', 'id');
    }


    public function deliveries()
    {
        return $this->hasMany(SupplierPurchaseOrdersItemsDelivery::class, 'supplier_purchase_orders_item_id');
    }

    public function getCantidadEntregadaAttribute()
    {
        return $this->deliveries()->sum('amount');
    }
}
