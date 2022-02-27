<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Activity;
use App\ActivityUser;

class InviteFriendsToActivity extends Notification
{
    use Queueable;
    public $activity;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
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
                    ->line(auth('api')->user()->username.' has invited you to join activity: ')
                    ->action($this->activity->title, url('http://doodfy.ch/activity/'.$this->activity->id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the datebase representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'user' => auth('api')->user()->only(['id', 'username', 'profile_image']),
            'activity' => $this->activity->only(['id', 'title', 'image']),
        ];
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
