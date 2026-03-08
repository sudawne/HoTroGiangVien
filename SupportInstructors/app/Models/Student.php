<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    // Kích hoạt xóa mềm
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'class_id',
        'student_code',
        'fullname',
        'dob',
        'pob',
        'status',
        'enrollment_year'
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    // --- CÁC MỐI QUAN HỆ (RELATIONSHIPS) ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function relatives()
    {
        return $this->hasMany(StudentRelative::class);
    }

    public function debts()
    {
        return $this->hasMany(StudentDebt::class);
    }

    // Đổi tên từ academicResults -> academic_results để khớp với Controller
    public function academic_results()
    {
        return $this->hasMany(AcademicResult::class);
    }

    // Thêm quan hệ lấy Cảnh báo học vụ
    public function academic_warnings()
    {
        return $this->hasMany(AcademicWarning::class);
    }
    // Thêm quan hệ lấy Lịch sử tư vấn
    public function consultation_logs()
    {
        return $this->hasMany(ConsultationLog::class);
    }
    public function studentClass()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'id');
    }

    public function trainingPoints() {
        return $this->hasMany(TrainingPoint::class, 'student_id', 'id');
    }
    public function academicWarnings(): HasMany
    {
        return $this->hasMany(AcademicWarning::class, 'student_id', 'id');
    }
}
