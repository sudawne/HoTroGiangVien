<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public function build()
    {
        return $this->subject('🔔 THÔNG BÁO MỚI: ' . $this->notification->title)
            ->view('emails.student_notification');
    }
}
