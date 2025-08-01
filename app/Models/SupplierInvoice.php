<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $table = 'suppliers_invoices';
    protected $fillable = [
        'supplier_id',
        'supplier_purchase_order_id',
        'pdf_route',
        'xml_route',
    ];


    public function invoices()
    {
        return $this->hasMany(SupplierInvoice::class, 'supplier_purchase_order_id');
    }
}
