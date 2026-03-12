<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRelative extends Model
{
    use HasFactory;

    protected $table = 'student_relatives';

    protected $fillable = [
        'student_id',
        'fullname',
        'relationship',
        'phone',
        'address',
        'is_emergency_contact',
    ];

    protected $casts = [
        'is_emergency_contact' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
