<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportBatch extends Model
{
    protected $fillable = ['semester_id', 'imported_by', 'name', 'type', 'file_url', 'total_records', 'status'];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function importer()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
    public function academicWarnings()
    {
        return $this->hasMany(AcademicWarning::class, 'batch_id');
    }
    public function academicResults()
    {
        return $this->hasMany(AcademicResult::class, 'batch_id');
    }
}
