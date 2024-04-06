<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\User;
use App\Organismossector;

class PaseExpediente extends Notification
{
    use Queueable;
    public $fromUser;
    public $referencia;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $referencia, $usersession_name, $sectorOrigen, $comentarioPase)
    {
        $this->fromUser = $user;
        $this->referencia = $referencia;
        $this->usersession_name = $usersession_name;
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
            ->greeting('Te asignaron un documento' )
            ->line('El usuario ' .$this->usersession_name. ' pasó el documento desde el sector ' .$this->sectorOrigen)
            ->line('Comentarios: "' .$this->comentarioPase. '"')
            ->action('Ver tus notificaciones', url('/notificaciones'))
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
