<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingPoint extends Model
{
    protected $fillable = ['student_id', 'semester_id', 'self_score', 'class_score', 'advisor_score', 'final_score', 'note'];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function semester() {
        return $this->belongsTo(Semester::class);
    }
}
