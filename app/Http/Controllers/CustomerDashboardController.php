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

        $selectedDate = $request->date ?? session('booking_date');
        $branches = Branch::all();
        $selectedBranchId = $request->branch_id ?? session('booking_branch_id');
        $selectedEmployeeId = $request->employee_id ?? session('booking_employee_id');

        session([
            'booking_date' => $selectedDate,
            'booking_branch_id' => $selectedBranchId,
            'booking_employee_id' => $selectedEmployeeId,
        ]);

        $upcomingReservations = $customer->reservations()
            ->with(['employee.user', 'branch', 'services'])
            ->whereIn('status', ['pending', 'arrived'])
            ->where('reservation_time', '>=', now()->subHours(1))
            ->orderBy('reservation_time', 'asc')
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

        $services = Service::where('is_active', true)->get();
        
        $capsters = collect();
        if ($selectedBranchId) {
            $capsters = Employee::with('user')
                ->where('is_active', true)
                ->where('branch_id', $selectedBranchId)
                ->where('position', 'barber')
                ->get();
        }

        $slots = [];

        // Hanya hitung slots HARI INI jika Kapster (employee_id) dan Tanggal (date) sudah dipilih.
        if ($selectedEmployeeId && $selectedDate) {
            $reservationsOnDate = Reservation::where('branch_id', $selectedBranchId)
                ->where('employee_id', $selectedEmployeeId)
                ->whereDate('reservation_time', $selectedDate)
                ->whereIn('status', ['pending', 'arrived'])
                ->get(['reservation_time', 'employee_id']);

            // jam buka dan tutup barbershop
            $start = Carbon::parse($selectedDate . ' 08:00');
            $end = Carbon::parse($selectedDate . ' 23:30');

            while ($start < $end) {
                $datetime = $start->format('Y-m-d H:i:s');

                // Cek apakah slot waktu sudah lewat (realtime)
                $isPast = $start->isPast();

                // Cek khusus untuk kapster ini:
                $isBooked = false;
                foreach ($reservationsOnDate as $res) {
                    $dt = Carbon::parse($res->reservation_time)->format('Y-m-d H:i:s');
                    if ($dt === $datetime) {
                        $isBooked = true;
                        break;
                    }
                }

                $slots[] = [
                    'time' => $start->format('H:i'),
                    'datetime' => $datetime,
                    'available' => !$isBooked && !$isPast
                ];

                $start->addMinutes(10); // jarak waktu antar slot
            }
        } // Tutup pengecekan if ($selectedEmployeeId)

        return view('dashboard', compact(
            'upcomingReservations',
            'services',
            'capsters',
            'branches',
            'availableDays',
            'selectedDate',
            'selectedBranchId',
            'selectedEmployeeId',
            'slots',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reservation_time' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'service_id' => 'nullable|exists:services,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        /** @var \App\Models\Customer $customer */
        $customer = auth()->guard('customer')->user();

        // Cek apakah capster spesifik sudah terbooking di waktu tersebut
        $capsterConflict = Reservation::where('reservation_time', $request->reservation_time)
            ->where('branch_id', $request->branch_id)
            ->where('employee_id', $request->employee_id)
            ->whereIn('status', ['pending', 'arrived'])
            ->exists();

        if ($capsterConflict) {
            return back()->withErrors(['error' => 'Kapster ini sudah memiliki booking pada waktu tersebut. Silakan pilih waktu/kapster lain.']);
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

        session() -> forget(['booking_date', 'booking_branch_id', 'booking_employee_id']);  

        return redirect()->route('dashboard')->with('success', 'Booking berhasil dibuat!');
    }

    public function history(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = auth()->guard('customer')->user();

        $history = $customer->reservations()
            ->with(['employee.user', 'branch', 'services'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderBy('reservation_time', 'desc')
            ->take(15)
            ->get();

        return view('history', compact('history'));
    }
}
