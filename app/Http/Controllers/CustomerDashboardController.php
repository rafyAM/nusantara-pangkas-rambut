<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\Customer $customer */
        $customer = auth()->guard('customer')->user();
        
        $upcomingReservations = $customer->reservations()
            ->with(['employee.user', 'branch', 'services'])
            ->whereIn('status', ['pending', 'arrived'])
            ->where('reservation_time', '>=', now()->subHours(1))
            ->orderBy('reservation_time', 'asc')
            ->get();

        $recentHistory = $customer->reservations()
            ->with(['employee.user', 'branch', 'services'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderBy('reservation_time', 'desc')
            ->take(3)
            ->get();

        return view('dashboard', compact('upcomingReservations', 'recentHistory'));
    }
}
