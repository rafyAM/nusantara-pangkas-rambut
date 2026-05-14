<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.kasir')]
class KasirReservation extends Component
{
    public string $search = '';

    public function approveReservation($id)
    {
        $reservation = Reservation::find($id);
        if ($reservation && $reservation->status === 'pending') {
            $reservation->update(['status' => 'arrived']);
        }
    }

    public function cancelReservation($id)
    {
        $reservation = Reservation::with('customer')->find($id);
        if ($reservation && $reservation->status === 'pending') {
            $reservation->update(['status' => 'cancelled']);
            if ($reservation->customer) {
                $reservation->customer->notify(new \App\Notifications\ReservationCancelled($reservation));
            }
        }
    }

    public function processToKasir($id)
    {
        return redirect()->route('kasir.pos', ['reservation_id' => $id]);
    }

    private function getReservationsList()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        $branchId = $user->employee?->branch_id ?? $user->branches()->first()?->id;

        $query = Reservation::with(['customer', 'employee', 'services'])
            ->whereIn('status', ['pending', 'arrived'])
            ->orderBy('reservation_time', 'asc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if (!empty($this->search)) {
            $query->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.kasir-reservation', [
            'reservationsData' => $this->getReservationsList()
        ]);
    }
}
