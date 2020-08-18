<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;
    public $token;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
       
        $this->token = $token;
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
        $forgotPasswordUrl = config('frontend.reset_passowrd_url')."?token={$this->token}";
        return (new MailMessage)
                    ->subject('Solicitud de restablecimiento de contraseña')
                    ->greeting('Hola')
                    ->line('Recibes este correo porque se ha solicitado un restablecimiento de contraseña.')
                    ->action('Restablecer Contraseña', $forgotPasswordUrl)
                    ->line('Si no realizaste esta solicitud, solo ignora este mensaje')
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
