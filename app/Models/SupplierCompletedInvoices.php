<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierCompletedInvoices extends Model
{
    protected $table = 'supplier_invoices';
    protected $primaryKey = 'id';

    protected $fillable = [
        'supplier_id',
        'data' => 'array',
        'pdf_route',
        'xml_route'
    ];

    protected $dates = ['deleted_at'];


    // public function items()
    // {
    //     return $this->hasMany(SupplierPurchaseOrderItem::class, 'supplier_purchase_order_id', 'id');
    // }

}
