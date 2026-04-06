<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reservation;
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

        // Fetch overlapping reservations for the day globally across all capsters
        $reservationsOnDate = Reservation::whereDate('reservation_time', $this->selectedDate)
            ->whereIn('status', ['pending', 'arrived'])
            ->get();

        $slots = [];
        $startTime = $dateObj->copy()->setTime(9, 0, 0);
        $endTime = $dateObj->copy()->setTime(21, 0, 0);

        while ($startTime <= $endTime) {
            $timeString = $startTime->format('H:i');

            $isBooked = $reservationsOnDate->contains(function ($res) use ($startTime) {
                return $res->reservation_time->format('H:i') === $startTime->format('H:i');
            });

            // Make past slots today unavailable
            $isPast = $this->selectedDate === now()->format('Y-m-d') && $startTime->isPast();

            $slots[] = [
                'time' => $timeString,
                'available' => !$isBooked && !$isPast,
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
