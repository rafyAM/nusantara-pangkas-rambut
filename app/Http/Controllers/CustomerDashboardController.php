<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Reservation;
use Carbon\Carbon;

class CustomerDashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = auth()->guard('customer')->user();

        $selectedDate = $request->date ?? now()->format('Y-m-d');

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
            ->take(5)
            ->get();

        $branches = Branch::all();

        $availableDays = [];

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i);

            $availableDays[] = [
                'date' => $date->format('Y-m-d'),
                'dayName' => $date->translatedFormat('D'),
                'shortDate' => $date->format('d')
            ];
        }

        $slots = [];

        $start = Carbon::parse($selectedDate . ' 08:00');
        $end = Carbon::parse($selectedDate . ' 21:00');

        while ($start < $end) {

            $datetime = $start->format('Y-m-d H:i:s');

            // cek apakah slot sudah dibooking
            $exists = Reservation::where('reservation_time', $datetime)
                ->whereIn('status', ['pending','arrived'])
                ->exists();

            $slots[] = [
                'time' => $start->format('H:i'),
                'datetime' => $datetime,
                'available' => !$exists
            ];

            $start->addMinutes(30);
        }

        $services = Service::where('is_active', true)->get();
        // Load capsters, mapping to user name
        $capsters = Employee::with('user')->where('is_active', true)->get();
        $branches = Branch::all();

        return view('dashboard', compact(
            'upcomingReservations',
            'recentHistory',
            'services',
            'capsters',
            'branches',
            'availableDays',
            'selectedDate',
            'slots',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reservation_time' => 'required|date',
            'service_id' => 'required|exists:services,id',
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        /** @var \App\Models\Customer $customer */
        $customer = auth()->guard('customer')->user();

        $reservation = Reservation::create([
            'customer_id' => $customer->id,
            'branch_id' => $request->branch_id,
            'employee_id' => $request->employee_id,
            'reservation_time' => $request->reservation_time,
            'status' => 'pending',
        ]);

        $reservation->services()->attach($request->service_id);

        return redirect()->route('dashboard')->with('success', 'Booking berhasil dibuat!');
    }
}
