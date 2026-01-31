<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Bỏ "implements ShouldQueue" để gửi trực tiếp
class StudentAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $username;
    public $password;

    public function __construct($studentName, $username, $password)
    {
        $this->studentName = $studentName;
        $this->username = $username;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Thông báo Tài khoản Hệ thống Cố vấn Học tập')
            ->view('emails.student_account');
    }
}
