<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class LecturerAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $rawPassword;

    public function __construct(User $user, $rawPassword)
    {
        $this->user = $user;
        $this->rawPassword = $rawPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo tài khoản Giảng viên - Hệ thống Cố vấn học tập',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lecturer_account', // Chúng ta sẽ tạo file view này ở bước 2
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
