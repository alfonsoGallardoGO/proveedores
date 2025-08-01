<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseOrdersItemsDelivery extends Model
{
    use HasFactory;

    protected $table = 'supplier_purchase_orders_items_deliveries';

    protected $fillable = [
        'supplier_purchase_orders_item_id',
        'amount',
    ];
}
