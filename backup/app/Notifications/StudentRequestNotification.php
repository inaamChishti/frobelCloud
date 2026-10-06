<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StudentRequestNotification extends Notification
{
    use Queueable;

    protected $studentRequest;

    public function __construct($studentRequest)
    {
        $this->studentRequest = $studentRequest;
    }

    public function via($notifiable)
    {
        return ['database']; // Use 'database' channel to store in notifications table
    }

    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->studentRequest->id,
            'message' => 'A new student request has been submitted.',
            'data' => $this->studentRequest->base64_data,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'request_id' => $this->studentRequest->id,
            'message' => 'A new student request has been submitted.',
        ];
    }
}
