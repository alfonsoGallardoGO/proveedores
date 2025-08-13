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

    // public function create()
    // {
    //     return Inertia::render('Suppliers/Suppliers/Create');
    // }

    public function store(Request $request)
    {
        $data = $request->validate([
            'external_id' => 'nullable|integer',
            'uid' => 'nullable|string',
            'name' => 'nullable|string|max:255', //required|string|max:255
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
            'name' => 'nullable|string|max:255', //required|string|max:255
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

        // return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado correctamente.');

        return response()->json(['message' => 'Proveedor actualizado correctamente.']);
        // return response()->noContent();
    }

    // Eliminar supplier (soft delete)
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado correctamente.');
    }

    public function storeSupliers(Request $request)
    {
        $data = $request->all(); 
        $externalId = $data['id'] ?? null;
        $uid = $data['nameorig'] ?? null;
        $name = $data['firstname'] . " " . ($data['lastname'] ?? null);
        $companyName = $data['companyname'] ?? null;
        $legalNameCompany = $data['legalname'] ?? null;
        $isIndividual = $data['tipoEntidad'] === 'individual' ? 'Si' : 'No';
        $type = $data['type'] ?? null;
        $phone = $data['phone'] ?? null;
        $category = $data['inpt_category'] ?? null;
        $tax = $data['custentity_mx_rfc'] ?? null;
        $email = $data['email'] ?? null;
        $currency = null;
        $defaultAccountsPayable = $data['accountnumber'] ?? null;
        $paymentTerms = null;
        $balance = $data['balance'] ?? null;
        $inactive = $data['isinactive'] ?? null;
        $mainSubsidiary = $data['subsidiary'] ?? null;
        $address = $data['shipaddressee'] ?? null;
        $prepaidBalance = null;
        $unbilledOrders = $data['unbilledorders'] ?? null;
        $creditLimit = $data['creditlimit'] ?? null;
        $filepath = null;

        $newSupplier = [
            'external_id' => $externalId,
            'uid' => $uid,
            'name' => $name,
            'company_name' => $companyName,
            'legal_name_company' => $legalNameCompany,
            'is_individual' => $isIndividual,
            'type' => $type,
            'phone' => $phone,
            'category' => $category,
            'tax' => $tax,
            'email' => $email,
            'currency' => $currency,
            'default_accounts_payable' => $defaultAccountsPayable,
            'payment_terms' => $paymentTerms,
            'balance' => $balance,
            'inactive' => $inactive,
            'main_subsidiary' => $mainSubsidiary,
            'address' => $address,
            'prepaid_balance' => $prepaidBalance,
            'unbilled_orders' => $unbilledOrders,
            'credit_limit' => $creditLimit,
            'filepath' => $filepath
        ];

        $supplier = Supplier::where('external_id', $externalId)->first();

        if(!empty($supplier)){
            $supplier->update($newSupplier);
        } else {
            Supplier::create($newSupplier);
        }


        return response()->json([
            'message' => 'Proveedor creado o actualizado correctamente.',
            'supplier' => $newSupplier
        ], 201);

    }
}
