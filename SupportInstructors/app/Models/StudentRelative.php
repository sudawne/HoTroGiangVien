<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRelative extends Model
{
    use HasFactory;

    // Chỉ định bảng (tùy chọn)
    protected $table = 'student_relatives';

    // Các trường được phép thêm/sửa hàng loạt
    protected $fillable = [
        'student_id',
        'fullname',
        'relationship',
        'phone',
        'address',
        'is_emergency_contact',
    ];

    // Ép kiểu dữ liệu (từ boolean/tinyint trong DB sang dạng true/false trong code)
    protected $casts = [
        'is_emergency_contact' => 'boolean',
    ];

    /**
     * Mối quan hệ: Thông tin người thân này thuộc về 1 sinh viên
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
