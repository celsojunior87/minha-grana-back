<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailResetPasswordNotification extends Notification
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
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $link = env('APP_URL_FRONT') . '/password/reset/' . $this->token . '?email=' . $notifiable->email;

        return (new MailMessage)
            ->view('mail.reset',
                [
                    'link' => $link,
                    'logo' => [
                        'path' => 'https://i.imgur.com/3Jzs3qL.png',
                        'width' => '100px',
                        'height' => '100px'
                    ],
                    'colors' => [
                        'highlight' => '#6FCF97',
                        'button'    => '#6FCF97',
                    ],
                ]
            )
            ->from(env('MAIL_FROM_ADDRESS'))
            ->subject('Recuperação de senha')
            ->line("Hey, We've successfully changed the text ")
            ->action('Reset Password', $link)
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
