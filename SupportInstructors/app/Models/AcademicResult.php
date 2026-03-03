<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'student_id',
        'semester_id',
        'gpa_10',
        'gpa_4',
        'training_point',
        'classification',
        'accumulated_gpa_4',
        'accumulated_credits',
        'is_warning',
    ];

    // Quan hệ: Thuộc về 1 sinh viên
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Quan hệ: Thuộc về 1 học kỳ (Rất quan trọng để hiển thị tên học kỳ trên Blade)
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    // Quan hệ: Thuộc đợt import nào
    public function importBatch()
    {
        return $this->belongsTo(ImportBatch::class, 'batch_id');
    }
}
