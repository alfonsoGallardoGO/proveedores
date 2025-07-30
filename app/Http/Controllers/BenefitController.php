<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $benefits = Benefit::all();
        return Inertia::render('Catalogo/Prestaciones/Index', [
            'benefits' => $benefits,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'conditioned' => 'boolean',
            'each' => 'boolean',
            'type' => 'required|string',
            'conditioned_efficiency' => 'boolean',
            'conditioned_seniority' => 'boolean',
            'efficiency_rules' => 'nullable|array',
            'day_cutoff' => 'nullable|integer',
        ]);

        Benefit::create($request->all());
        
        return redirect()->route('catalogo.prestaciones.index')->with('success', 'Benefit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Benefit $benefit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Benefit $benefit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Benefit $benefit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Benefit $benefit)
    {
        //
    }
}
