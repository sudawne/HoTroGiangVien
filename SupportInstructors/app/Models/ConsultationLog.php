<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationLog extends Model
{
    use HasFactory;

    protected $table = 'consultation_logs';

    protected $fillable = [
        'student_id',
        'advisor_id',
        'semester_id',
        'topic',
        'content',
        'solution',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function advisor()
    {
        return $this->belongsTo(Lecturer::class, 'advisor_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
