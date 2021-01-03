<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgotNotification extends Notification
{
    use Queueable;
    public  $new_password;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(String $new_password)
    {
        $this->new_password=$new_password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
            return (new MailMessage)->view('emails.forgot_email',["password" => $this->new_password]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $array= "password của bạn đã được thay đổi , vui lòng xem chi tiết trong email: " . $this->locale;
        return [
            $array
        ];
    }
}
