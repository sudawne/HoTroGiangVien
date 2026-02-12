<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Student extends Model
{
    // [QUAN TRỌNG] Sử dụng trait SoftDeletes để kích hoạt xóa mềm
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

    // Ép kiểu dữ liệu để xử lý ngày tháng dễ hơn
    protected $casts = [
        'dob' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        // Lưu ý: Model lớp học của bạn tên là "Classes" (số nhiều),
        // nếu tên file model là Class.php thì sửa thành Class::class
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

    public function academicResults()
    {
        return $this->hasMany(AcademicResult::class);
    }
}
