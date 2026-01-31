<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',           // Mã HK (VD: 2025_HK1)
        'name',           // Tên hiển thị (VD: Học kỳ 1)
        'academic_year',  // Năm học (VD: 2025-2026)
        'start_date',     
        'end_date',
        'is_current'      // Đánh dấu học kỳ hiện tại
    ];

    public function academicWarnings()
    {
        return $this->hasMany(AcademicWarning::class);
    }
}