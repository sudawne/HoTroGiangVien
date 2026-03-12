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
        'subject_id', 
        'reason',
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function semester() {
        return $this->belongsTo(Semester::class);
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }
}