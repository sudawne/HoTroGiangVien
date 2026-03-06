<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicResult extends Model
{
    protected $fillable = [
        'student_id', 
        'semester_id', 
        'gpa_10', 
        'gpa_4', 
        'classification'
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function semester() {
        return $this->belongsTo(Semester::class);
    }
}