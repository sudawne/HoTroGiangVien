<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',      
        'name',           
        'academic_year',  
        'start_date',     
        'end_date',
        'is_current'  
    ];

    public function academicWarnings()
    {
        return $this->hasMany(AcademicWarning::class);
    }
}