<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    // Khai báo các cột được phép thêm dữ liệu
    protected $fillable = ['code', 'name'];

    // Quan hệ: 1 Khoa có nhiều Lớp
    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

    // Quan hệ: 1 Khoa có nhiều Giảng viên
    public function lecturers()
    {
        return $this->hasMany(Lecturer::class);
    }
}
