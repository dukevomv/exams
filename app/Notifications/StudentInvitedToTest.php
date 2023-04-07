<?php

namespace App\Notifications;

use App\Models\Test;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StudentInvitedToTest extends Notification
{
//    use Queueable;

    /**
     * @var Test
     */
    private $test;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Test $test)
    {
        $this->test = $test;
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
        return (new MailMessage)
                    ->subject('Exam Invitation')
                    ->greeting('Hello Student,')
                    ->line('You have been invited to join an online examination.')
                    ->line('Course: '.$this->test->lesson->name)
                    ->line('Examination Date: '.$this->test->scheduled_at->toDateTimeString())
                    ->action('Accept Invitation', route('test.invitation.preview', [
                        'testId' => $this->test->id,
                        'inviteUuid'=>$notifiable->uuid
                    ]))
                    ->line('Thank you for using '.config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $notifiable->toArray();
    }
}
