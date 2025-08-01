<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
   
    public function index()
    {
        $suppliers = Supplier::orderBy('id', 'desc')
        // ->limit(5)
        ->get();
        return Inertia::render('Suppliers/Suppliers/Index', [
            'suppliers' => $suppliers
        ]);
    }

    public function create()
    {
        return Inertia::render('Suppliers/Suppliers/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'external_id' => 'nullable|integer',
            'uid' => 'nullable|string',
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'legal_name_company' => 'nullable|string|max:255',
            'is_individual' => 'nullable|string|max:10',
            'type' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'tax' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'currency' => 'nullable|string|max:255',
            'default_accounts_payable' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'balance' => 'nullable|string|max:255',
            'inactive' => 'nullable|string|max:255',
            'main_subsidiary' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'prepaid_balance' => 'nullable|string|max:255',
            'unbilled_orders' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|string|max:255',
            'filepath' => 'nullable|string|max:100',
        ]);

        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('success', 'Proveedor creado correctamente.');
    }

    // Editar supplier
    public function edit(Supplier $supplier)
    {
        return Inertia::render('Suppliers/Suppliers/Edit', [
            'supplier' => $supplier
        ]);
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json($supplier);
    }


    // Actualizar supplier
    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'external_id' => 'nullable|integer',
            'uid' => 'nullable|string',
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'legal_name_company' => 'nullable|string|max:255',
            'is_individual' => 'nullable|string|max:10',
            'type' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'tax' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'currency' => 'nullable|string|max:255',
            'default_accounts_payable' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'balance' => 'nullable|string|max:255',
            'inactive' => 'nullable|string|max:255',
            'main_subsidiary' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'prepaid_balance' => 'nullable|string|max:255',
            'unbilled_orders' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|string|max:255',
            'filepath' => 'nullable|string|max:100',
        ]);

        $supplier->update($data);

        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    // Eliminar supplier (soft delete)
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado correctamente.');
    }
}
