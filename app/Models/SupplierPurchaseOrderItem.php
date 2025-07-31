<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseOrderItem extends Model
{
    use HasFactory;

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
    ];

    public function order()
    {
        return $this->belongsTo(SupplierPurchaseOrder::class, 'supplier_purchase_order_id', 'id');
    }
}
