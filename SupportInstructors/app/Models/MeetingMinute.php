<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingMinute extends Model
{
    use HasFactory;

    // 1. Khai báo các cột được phép lưu
    protected $fillable = [
        'class_id',
        'semester_id',
        'created_by',
        'title',
        'held_at',
        'ended_at',
        'location',
        'monitor_id',   
        'secretary_id',
        'content_discussions',
        'content_conclusion',
        'content_requests',
        'attendees_count',
        'absent_list',
        'file_url',
        'status'
    ];

    // 2. Ép kiểu dữ liệu
    protected $casts = [
        'held_at' => 'datetime',
        'ended_at' => 'datetime',
        'absent_list' => 'array', 
    ];

    // --- CÁC MỐI QUAN HỆ (RELATIONSHIPS) ---

    // [QUAN TRỌNG] Hàm này đang bị thiếu gây ra lỗi
    public function studentClass() {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    // Quan hệ với Học kỳ
    public function semester() {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    // Quan hệ với Người tạo (Giảng viên/Cán bộ)
    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Quan hệ với Lớp trưởng (Chủ trì)
    public function monitor() {
        return $this->belongsTo(Student::class, 'monitor_id');
    }

    // Quan hệ với Thư ký
    public function secretary() {
        return $this->belongsTo(Student::class, 'secretary_id');
    }
}