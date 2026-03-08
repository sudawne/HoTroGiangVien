<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'semester_id',
        'subject_code',
        'subject_name',
        'reason',
        'debt_amount',
    ];

    // Quan hệ với Sinh viên
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Quan hệ với Học kỳ
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}