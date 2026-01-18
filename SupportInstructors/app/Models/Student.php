<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'class_id', 'student_code', 'fullname', 'dob', 'pob', 'status', 'enrollment_year'];

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

    public function academicResults()
    {
        return $this->hasMany(AcademicResult::class);
    }
}
