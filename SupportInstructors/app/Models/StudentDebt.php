<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDebt extends Model
{
    use HasFactory;

    protected $table = 'student_debts';

    // Khai báo các cột có trong bảng student_debts
    protected $fillable = [
        'batch_id',
        'student_id',
        'semester_id',
        'course_code',
        'course_name',
        'credits',
        'score',
        'status', // 'owed' (đang nợ) hoặc 'cleared' (đã trả)
        'note',
    ];

    /**
     * Thuộc về một Sinh viên
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Môn nợ này thuộc Học kỳ nào
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Môn nợ này được import vào từ Đợt import nào
     */
    public function importBatch()
    {
        return $this->belongsTo(ImportBatch::class, 'batch_id');
    }
}
