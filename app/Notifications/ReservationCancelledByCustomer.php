<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Carbon\Carbon;

class ReservationCancelledByCustomer extends Notification
{
    use Queueable;

    protected $reservation;

    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $time = Carbon::parse($this->reservation->reservation_time)->format('H:i');
        $customerName = $this->reservation->customer->name ?? 'Pelanggan';

        return (new WebPushMessage)
            ->title('Reservasi Dibatalkan!')
            ->icon('/favicon.ico')
            ->body("Pelanggan {$customerName} telah membatalkan jadwalnya pada jam {$time}. Slot sekarang kembali kosong!")
            ->action('Cek Jadwal', url('/kasir/pos'))
            ->options(['TTL' => 3600]);
    }
}
