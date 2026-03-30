<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Carbon\Carbon;

class ReservationCancelled extends Notification
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
        return (new WebPushMessage)
            ->title('Reservasi Hangus/Dibatalkan!')
            ->icon('/favicon.ico')
            ->body("Mohon maaf {$notifiable->name}, reservasi potong rambutmu pada jam {$time} telah dibatalkan atau hangus karena kamu tidak hadir di waktu yang ditentukan.")
            ->action('Buat Jadwal Baru', url('/dashboard'));
    }
}
