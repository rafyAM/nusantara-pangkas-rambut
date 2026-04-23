<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Reservation;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\Customer $customer */
        $customer = $request->user('sanctum');

        $reservations = $customer->reservations()
            ->with(['employee', 'branch', 'services'])
            ->orderBy('reservation_time', 'desc')
            ->paginate(15);

        return response()->json($reservations);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'branch_id'        => 'required|exists:branches,id',
            'reservation_time' => 'required|date|after:now',
            'employee_id'      => 'nullable|exists:employees,id',
            'service_ids'      => 'nullable|array',
            'service_ids.*'    => 'exists:services,id',
        ]);

        /** @var \App\Models\Customer $customer */
        $customer = $request->user('sanctum');

        // Validasi konflik kapster
        if ($request->employee_id) {
            $conflict = Reservation::where('reservation_time', $request->reservation_time)
                ->where('branch_id', $request->branch_id)
                ->where('employee_id', $request->employee_id)
                ->whereIn('status', ['pending', 'arrived'])
                ->exists();

            if ($conflict) {
                return response()->json([
                    'message' => 'Kapster sudah terbooking pada waktu tersebut.',
                ], 422);
            }
        } else {
            $totalCapsters = Employee::where('is_active', true)
                ->where('branch_id', $request->branch_id)
                ->where('position', 'barber')
                ->count();

            $bookedCount = Reservation::where('reservation_time', $request->reservation_time)
                ->where('branch_id', $request->branch_id)
                ->whereIn('status', ['pending', 'arrived'])
                ->count();

            if ($bookedCount >= $totalCapsters) {
                return response()->json([
                    'message' => 'Semua kapster sudah penuh pada waktu tersebut.',
                ], 422);
            }
        }

        $reservation = Reservation::create([
            'customer_id'      => $customer->id,
            'branch_id'        => $request->branch_id,
            'employee_id'      => $request->employee_id,
            'reservation_time' => $request->reservation_time,
            'status'           => 'pending',
        ]);

        if ($request->service_ids) {
            $reservation->services()->attach($request->service_ids);
        }

        $reservation->load(['employee', 'branch', 'services']);

        return response()->json([
            'message'     => 'Reservasi berhasil dibuat.',
            'reservation' => $reservation,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $reservation = $request->user('sanctum')
            ->reservations()
            ->with(['employee', 'branch', 'services'])
            ->findOrFail($id);

        return response()->json($reservation);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $reservation = $request->user('sanctum')
            ->reservations()
            ->where('status', 'pending')
            ->findOrFail($id);

        $reservation->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Reservasi dibatalkan.']);
    }

    public function availability(Request $request): JsonResponse
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'date'      => 'required|date',
        ]);

        $branchId      = $request->branch_id;
        $selectedDate  = $request->date;
        $totalCapsters = Employee::where('is_active', true)
            ->where('branch_id', $branchId)
            ->where('position', 'barber')
            ->count();

        $reservations = Reservation::withoutGlobalScopes()
            ->where('branch_id', $branchId)
            ->whereDate('reservation_time', $selectedDate)
            ->whereIn('status', ['pending', 'arrived'])
            ->get(['reservation_time', 'employee_id']);

        $bookedMap      = [];
        $nullBookedCount = [];
        foreach ($reservations as $res) {
            $dt = Carbon::parse($res->reservation_time)->format('Y-m-d H:i:s');
            if ($res->employee_id !== null) {
                $bookedMap[$dt][] = $res->employee_id;
            } else {
                $nullBookedCount[$dt] = ($nullBookedCount[$dt] ?? 0) + 1;
            }
        }

        $slots = [];
        $start = Carbon::parse($selectedDate . ' 08:00');
        $end   = Carbon::parse($selectedDate . ' 21:30');

        while ($start < $end) {
            $datetime        = $start->format('Y-m-d H:i:s');
            $explicitlyBooked = $bookedMap[$datetime] ?? [];
            $nullCount       = $nullBookedCount[$datetime] ?? 0;
            $allBooked       = $totalCapsters > 0
                ? (count($explicitlyBooked) + $nullCount) >= $totalCapsters
                : true;

            $slots[] = [
                'time'      => $start->format('H:i'),
                'datetime'  => $datetime,
                'available' => !$allBooked && !$start->isPast(),
            ];
            $start->addMinutes(10);
        }

        return response()->json([
            'date'   => $selectedDate,
            'slots'  => $slots,
        ]);
    }
}
