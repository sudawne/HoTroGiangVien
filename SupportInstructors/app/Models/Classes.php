<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'department_id',
        'advisor_id',
        'monitor_id',
        'secretary_id', // Thêm trường này
        'code',
        'name',
        'academic_year'
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function advisor()
    {
        return $this->belongsTo(Lecturer::class, 'advisor_id');
    }

    public function monitor()
    {
        return $this->belongsTo(Student::class, 'monitor_id');
    }

    public function secretary()
    {
        return $this->belongsTo(Student::class, 'secretary_id');
    }
}
