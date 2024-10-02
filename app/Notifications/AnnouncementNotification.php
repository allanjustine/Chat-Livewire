<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification
{
    use Queueable;

    protected $poster;
    protected $posterId;
    protected $announcement;

    /**
     * Create a new notification instance.
     */
    public function __construct($announcement)
    {
        $user = auth()->user();

        $this->poster = $user;
        $this->posterId = $user->id;
        $this->announcement = $announcement;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'poster_name'                       =>              $this->poster->name,
            'poster_username'                   =>              $this->poster->username,
            'poster_id'                         =>              $this->posterId,
            'poster_profile_picture'            =>              $this->poster->profile_picture,
            'post_title'                        =>              $this->announcement->post_title,
            'post_created_at'                   =>              $this->announcement->created_at,
            'post_body'                         =>              'posted an updates'
        ];
    }
}
