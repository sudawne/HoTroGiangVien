<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDebt extends Model
{
    use HasFactory;

    protected $table = 'student_debts';
    protected $fillable = [
        'batch_id',
        'student_id',
        'semester_id',
        'course_code',
        'course_name',
        'credits',
        'score',
        'status', 
        'note',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function importBatch()
    {
        return $this->belongsTo(ImportBatch::class, 'batch_id');
    }
}
