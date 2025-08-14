<?php

namespace App\Http\Controllers;
use App\Models\Dashboard;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = Dashboard::all();
        return Inertia::render('Dashboard', [
            'orders' => $orders
        ]);
    }
}
