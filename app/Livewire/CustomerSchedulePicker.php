<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\Employee;
use Carbon\Carbon;

class CustomerSchedulePicker extends Component
{
    public $selectedDate;
    public $availableDays = [];
    public $slots = [];

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->generateDays();
    }

    public function generateDays()
    {
        $this->availableDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = now()->addDays($i);
            $this->availableDays[] = [
                'date' => $day->format('Y-m-d'),
                'dayName' => $i === 0 ? 'Hari Ini' : ($i === 1 ? 'Besok' : $day->translatedFormat('l')),
                'shortDate' => $day->format('d/m'),
            ];
        }
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
    }

    public function generateSlots()
    {
        $dateObj = Carbon::parse($this->selectedDate);

        // Hitung total capster aktif (tanpa filter cabang karena komponen ini tidak punya context cabang)
        $totalCapsters = Employee::where('is_active', true)
            ->where('position', 'barber')
            ->count();

        // Batch-load semua reservasi pada tanggal yang dipilih
        $reservationsOnDate = Reservation::whereDate('reservation_time', $this->selectedDate)
            ->whereIn('status', ['pending', 'arrived'])
            ->get(['reservation_time', 'employee_id']);

        // Bangun lookup map per slot waktu
        $bookedMap = [];
        $nullBookedCount = [];

        foreach ($reservationsOnDate as $res) {
            $timeKey = Carbon::parse($res->reservation_time)->format('H:i');
            if ($res->employee_id !== null) {
                $bookedMap[$timeKey][] = $res->employee_id;
            } else {
                $nullBookedCount[$timeKey] = ($nullBookedCount[$timeKey] ?? 0) + 1;
            }
        }

        $slots = [];
        $startTime = $dateObj->copy()->setTime(9, 0, 0);
        $endTime = $dateObj->copy()->setTime(21, 0, 0);

        while ($startTime <= $endTime) {
            $timeString = $startTime->format('H:i');

            // Slot penuh hanya jika SEMUA capster sudah terbooking
            $explicitlyBooked = $bookedMap[$timeString] ?? [];
            $nullCount = $nullBookedCount[$timeString] ?? 0;
            $allBooked = $totalCapsters > 0
                ? (count($explicitlyBooked) + $nullCount) >= $totalCapsters
                : true;

            // Make past slots today unavailable
            $isPast = $this->selectedDate === now()->format('Y-m-d') && $startTime->isPast();

            $slots[] = [
                'time' => $timeString,
                'available' => !$allBooked && !$isPast,
                'datetime' => $startTime->format('Y-m-d H:i:s')
            ];

            $startTime->addMinutes(30);
        }

        $this->slots = $slots;
    }

    public function render()
    {
        $this->generateSlots();
        return view('livewire.customer-schedule-picker');
    }
}
