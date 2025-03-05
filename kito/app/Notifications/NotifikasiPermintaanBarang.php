<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifikasiPermintaanBarang extends Notification
{
    use Queueable;
    protected $pesannama;
    protected $pesanbarang;
    protected $pesanstok;

    /**
     * Create a new notification instance.
     */
    public function __construct($pesannama, $pesanbarang, $pesanstok)
    {
        $this->pesannama = $pesannama;
        $this->pesanbarang = $pesanbarang;
        $this->pesanstok = $pesanstok;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->view('siminbar.notifikasiemail', [
            'pesannama' => $this->pesannama,
            'pesanbarang' => $this->pesanbarang,
            'pesanstok' => $this->pesanstok
        ]);
        // return (new MailMessage)
        // ->line("Halo, {$this->pesannama} mengajukan permintaan.")
        // ->line("Barang yang diminta: {$this->pesanbarang}")
        // ->line("Jumlah stok yang diminta: {$this->pesanstok}")
        // ->action('Lihat Detail Permintaan', url('/'))
        // ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
