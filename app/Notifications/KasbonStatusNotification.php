<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KasbonStatusNotification extends Notification
{
    use Queueable;

    public $kasbon;
    public $status;

    public function __construct($kasbon, $status)
    {
        $this->kasbon = $kasbon;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        // bisa via 'database', 'mail', atau keduanya
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Kasbon sebesar Rp " . number_format($this->kasbon->jumlah, 0, ',', '.') . " telah {$this->status}.",
            'kasbon_id' => $this->kasbon->id,
        ];
    }
}
