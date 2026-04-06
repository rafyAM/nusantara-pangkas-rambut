<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Carbon\Carbon;

class ReservationReminder extends Notification
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
            ->title('Waktunya Cukur!')
            ->icon('/favicon.ico')
            ->body("Halo {$notifiable->name}, reservasi potong rambutmu akan dimulai dalam 20 menit (Jam {$time}). Segera siap-siap ke Pangkas Nusantara ya!")
            ->action('Cek Jadwal', url('/dashboard'));
    }
}
