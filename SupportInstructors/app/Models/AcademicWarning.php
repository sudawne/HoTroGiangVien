<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicWarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',         // ID đợt import (để biết import lúc nào)
        'student_id',       // ID sinh viên
        'semester_id',      // ID học kỳ
        'warning_level',    // Mức cảnh báo (1, 2, 3)
        'gpa_term',         // ĐTB Học kỳ
        'gpa_cumulative',   // ĐTB Tích lũy
        'credits_owed',     // Số tín chỉ nợ/rớt
        'warning_count',    // Số lần cảnh báo (dữ liệu thô từ excel)
        'reason',           // Lý do bị cảnh báo
        'status',           // Trạng thái (pending, processed...)
        'advisor_note'      // Ghi chú của cố vấn
    ];

    // Quan hệ: Cảnh báo thuộc về 1 sinh viên
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Quan hệ: Cảnh báo thuộc về 1 học kỳ
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