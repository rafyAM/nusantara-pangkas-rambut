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
        $branches = Branch::all();
        $selectedBranchId = $request->branch_id ?? ($branches->first()->id ?? null);

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

        // jam buka dan tutup barbershop
        $start = Carbon::parse($selectedDate . ' 08:00');
        $end = Carbon::parse($selectedDate . ' 21:30');

        while ($start < $end) {

            $datetime = $start->format('Y-m-d H:i:s');

            // Cek apakah slot waktu sudah lewat (realtime)
            $isPast = $start->isPast();

            // cek apakah slot sudah dibooking di cabang spesifik
            $exists = Reservation::where('reservation_time', $datetime)
                ->where('branch_id', $selectedBranchId)
                ->whereIn('status', ['pending', 'arrived'])
                ->exists();

            $slots[] = [
                'time' => $start->format('H:i'),
                'datetime' => $datetime,
                'available' => !$exists && !$isPast
            ];

            $start->addMinutes(30); // jarak waktu antar slot
        }

        $services = Service::where('is_active', true)->get();
        $capsters = Employee::with('user')->where('is_active', true)->where('branch_id', $selectedBranchId)->get();

        return view('dashboard', compact(
            'upcomingReservations',
            'recentHistory',
            'services',
            'capsters',
            'branches',
            'availableDays',
            'selectedDate',
            'selectedBranchId',
            'slots',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reservation_time' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'service_id' => 'nullable|exists:services,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        /** @var \App\Models\Customer $customer */
        $customer = auth()->guard('customer')->user();

        // Validasi ekstra menghindari bentrok jam (Race condition / Post manipulation)
        $exists = Reservation::where('reservation_time', $request->reservation_time)
            ->where('branch_id', $request->branch_id)
            ->whereIn('status', ['pending', 'arrived'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Mohon maaf, waktu ini baru saja dibooking oleh pelanggan lain di cabang yang sama. Silakan pilih waktu lain.']);
        }

        $reservation = Reservation::create([
            'customer_id' => $customer->id,
            'branch_id' => $request->branch_id,
            'employee_id' => $request->employee_id,
            'reservation_time' => $request->reservation_time,
            'status' => 'pending',
        ]);

        if ($request->service_id) {
            $reservation->services()->attach($request->service_id);
        }

        return redirect()->route('dashboard')->with('success', 'Booking berhasil dibuat!');
    }
}
