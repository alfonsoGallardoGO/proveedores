<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $table = 'supplier_purchase_orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'supplier_external_id',
        'rfc',
        'status',
        'date',
        'data' => 'array',
        'purchase_order_id'
    ];


    public function invoices()
    {
        return $this->hasMany(SupplierInvoice::class, 'supplier_purchase_order_id', 'id');
    }
}
