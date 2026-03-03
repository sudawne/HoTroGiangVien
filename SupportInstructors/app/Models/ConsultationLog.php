<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationLog extends Model
{
    use HasFactory;

    // Chỉ định tên bảng (tuỳ chọn, nhưng nên có để chắc chắn)
    protected $table = 'consultation_logs';

    protected $fillable = [
        'student_id',
        'advisor_id',
        'semester_id',
        'topic',
        'content',
        'solution',
    ];

    /**
     * Nhật ký này thuộc về một Sinh viên
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Nhật ký này được tạo bởi một Cố vấn học tập (Giảng viên)
     * Dựa theo DB, advisor_id liên kết với bảng lecturers
     */
    public function advisor()
    {
        return $this->belongsTo(Lecturer::class, 'advisor_id');
    }

    /**
     * Nhật ký này thuộc về Học kỳ nào
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
