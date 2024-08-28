<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountVerificationNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $otp;

    /**
     * Create a new notification instance.
     *
     * @param object $user
     * @param string $otp
     */
    public function __construct(object $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
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
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Account Verification OTP')
            ->line($this->user->name . ',')
            ->line('Your OTP for account verification is: ' . $this->otp)
            ->line('This OTP will expire in 5 minutes.')
            ->line('Please do not share this OTP with anyone.')
            ->action('Verify Account', url('/verify-otp'))
            ->line('Thank you for using our application!');
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
