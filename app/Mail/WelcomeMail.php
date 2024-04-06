<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

// use App\Ciudadano;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($ciudadano, $url)
    {


        $this->ciudadano = $ciudadano;
        $this->url = $url;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.welcome', ['ciudadano' => $this->ciudadano, 'url' => $this->url ])
            ->from('no-reply@nuestrodominio.com.ar')
            ->subject('Bienvenido!');

    }
}
