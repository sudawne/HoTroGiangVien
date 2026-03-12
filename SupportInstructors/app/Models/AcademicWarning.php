<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicWarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',     
        'student_id',       
        'semester_id',      
        'warning_level',   
        'gpa_term',      
        'gpa_cumulative',   
        'credits_owed',    
        'warning_count',    
        'reason',      
        'status',          
        'advisor_note'    
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    
    public function importBatch()
    {
        return $this->belongsTo(ImportBatch::class, 'batch_id');
    }
}