<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\SupplierWhatsapp;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SupplierWhatsappController extends Controller
{
    public function index()
    {
        $supplierId = Auth::user()->supplier_id;
        $supplier = Supplier::where('external_id', $supplierId)->first();
        //dd($supplierId);

        $supplierWhatsapps = SupplierWhatsapp::where('external_supplier_id', $supplierId)->get();
        return Inertia::render('Whatsapp/Index', [
            'supplierWhatsapps' => $supplierWhatsapps,
            'supplierId' => $supplierId,
            'supplierName' => $supplier?->company_name,
        ]);
    }
}
