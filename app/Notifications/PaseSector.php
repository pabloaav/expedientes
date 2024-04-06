<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Organismossector;

class PaseSector extends Notification
{
    use Queueable;
    public $sector;
    public $referencia;
    

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($referencia, $sector, $sectorOrigen, $comentarioPase)
    {
        $this->sector = $sector;
        $this->referencia = $referencia;
        $this->sectorOrigen = $sectorOrigen;
        $this->comentarioPase = $comentarioPase;
       
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->referencia)
            ->greeting('Asignaron un documento al sector ' .$this->sector. ' con este correo asociado' )
            ->line('Desde el sector: ' .$this->sectorOrigen )
            ->line('Comentarios: "' .$this->comentarioPase. '"')
            ->action('Ver tus notificaciones', url('/'))
            ->salutation('Saludos');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
