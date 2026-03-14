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

        // Booking Data
        $selectedDate = $request->input('date', now()->format('Y-m-d'));
        $dateObj = Carbon::parse($selectedDate);

        // Fetch overlapping reservations for the day globally across all capsters (Basic setup)
        $reservationsOnDate = Reservation::whereDate('reservation_time', $selectedDate)
            ->whereIn('status', ['pending', 'arrived'])
            ->get();

        // Generate Time Slots from 09:00 to 21:00 (30 mins interval)
        $slots = [];
        $startTime = $dateObj->copy()->setTime(9, 0, 0);
        $endTime = $dateObj->copy()->setTime(21, 0, 0);

        while ($startTime <= $endTime) {
            $timeString = $startTime->format('H:i');

            // Check if this slot is already booked
            // (Note: For a more advanced system, we'd check per capster, but for now we check globally or simply define a simple overlap)
            $isBooked = $reservationsOnDate->contains(function ($res) use ($startTime) {
                return $res->reservation_time->format('H:i') === $startTime->format('H:i');
            });

            // Make past slots today unavailable
            $isPast = $selectedDate === now()->format('Y-m-d') && $startTime->isPast();

            $slots[] = [
                'time' => $timeString,
                'available' => !$isBooked && !$isPast,
                'datetime' => $startTime->format('Y-m-d H:i:s')
            ];

            $startTime->addMinutes(30);
        }

        $services = Service::where('is_active', true)->get();
        // Load capsters, mapping to user name
        $capsters = Employee::with('user')->where('is_active', true)->get();
        $branches = Branch::all();

        // Generate 7 days for the date picker
        $availableDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = now()->addDays($i);
            $availableDays[] = [
                'date' => $day->format('Y-m-d'),
                'dayName' => $i === 0 ? 'Hari Ini' : ($i === 1 ? 'Besok' : $day->translatedFormat('l')),
                'shortDate' => $day->format('d/m'),
            ];
        }

        return view('dashboard', compact(
            'upcomingReservations',
            'recentHistory',
            'slots',
            'services',
            'capsters',
            'branches',
            'selectedDate',
            'availableDays'
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
