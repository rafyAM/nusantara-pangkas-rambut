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

        $services = Service::where('is_active', true)->get();
        $capsters = Employee::with('user')
            ->where('is_active', true)
            ->where('branch_id', $selectedBranchId)
            ->where('position', 'barber')
            ->get();

        $totalCapsters = $capsters->count();

        // Batch-load semua reservasi pada tanggal & cabang yang dipilih (1 query, bukan N+1)
        $reservationsOnDate = Reservation::where('branch_id', $selectedBranchId)
            ->whereDate('reservation_time', $selectedDate)
            ->whereIn('status', ['pending', 'arrived'])
            ->get(['reservation_time', 'employee_id']);

        // Bangun lookup map: datetime => [employee_ids] dan datetime => jumlah reservasi tanpa capster
        $bookedMap = [];
        $nullBookedCount = [];

        foreach ($reservationsOnDate as $res) {
            $dt = Carbon::parse($res->reservation_time)->format('Y-m-d H:i:s');
            if ($res->employee_id !== null) {
                $bookedMap[$dt][] = $res->employee_id;
            } else {
                $nullBookedCount[$dt] = ($nullBookedCount[$dt] ?? 0) + 1;
            }
        }

        $slots = [];
        $slotBookedCapsters = [];

        // jam buka dan tutup barbershop
        $start = Carbon::parse($selectedDate . ' 08:00');
        $end = Carbon::parse($selectedDate . ' 21:30');

        while ($start < $end) {

            $datetime = $start->format('Y-m-d H:i:s');

            // Cek apakah slot waktu sudah lewat (realtime)
            $isPast = $start->isPast();

            // Cek per-capster: slot hanya penuh jika SEMUA capster sudah terbooking
            $explicitlyBooked = $bookedMap[$datetime] ?? [];
            $nullCount = $nullBookedCount[$datetime] ?? 0;
            $allBooked = $totalCapsters > 0
                ? (count($explicitlyBooked) + $nullCount) >= $totalCapsters
                : true; // Jika tidak ada capster, slot tidak tersedia

            $slots[] = [
                'time' => $start->format('H:i'),
                'datetime' => $datetime,
                'available' => !$allBooked && !$isPast
            ];

            // Simpan mapping capster yang sudah terbooking per slot (untuk frontend filtering)
            if (!empty($explicitlyBooked)) {
                $slotBookedCapsters[$datetime] = $explicitlyBooked;
            }

            $start->addMinutes(10); // jarak waktu antar slot
        }

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
            'slotBookedCapsters',
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

        // Validasi per-capster menghindari bentrok jam (Race condition / Post manipulation)
        if ($request->employee_id) {
            // Cek apakah capster spesifik sudah terbooking di waktu tersebut
            $capsterConflict = Reservation::where('reservation_time', $request->reservation_time)
                ->where('branch_id', $request->branch_id)
                ->where('employee_id', $request->employee_id)
                ->whereIn('status', ['pending', 'arrived'])
                ->exists();

            if ($capsterConflict) {
                return back()->withErrors(['error' => 'Kapster ini sudah memiliki booking pada waktu tersebut. Silakan pilih kapster lain.']);
            }
        } else {
            // "Siapa saja" — cek apakah semua capster sudah penuh
            $totalCapsters = Employee::where('is_active', true)
                ->where('branch_id', $request->branch_id)
                ->where('position', 'barber')
                ->count();

            $bookedCount = Reservation::where('reservation_time', $request->reservation_time)
                ->where('branch_id', $request->branch_id)
                ->whereIn('status', ['pending', 'arrived'])
                ->count();

            if ($bookedCount >= $totalCapsters) {
                return back()->withErrors(['error' => 'Semua kapster sudah terbooking pada waktu tersebut. Silakan pilih waktu lain.']);
            }
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
