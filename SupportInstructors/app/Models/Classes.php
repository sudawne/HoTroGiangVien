<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes'; // Định danh rõ bảng vì tên Model số nhiều
    protected $fillable = ['department_id', 'advisor_id', 'monitor_id', 'code', 'name', 'academic_year'];

    // Quan hệ: 1 Lớp có nhiều Sinh viên
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }



    // Quan hệ: Lớp thuộc về 1 Cố vấn (Giảng viên)
    public function advisor()
    {
        return $this->belongsTo(Lecturer::class, 'advisor_id');
    }

    // Quan hệ: Lớp có 1 Lớp trưởng (Sinh viên)
    public function monitor()
    {
        return $this->belongsTo(Student::class, 'monitor_id');
    }
}
