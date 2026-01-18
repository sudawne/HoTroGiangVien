<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportBatch extends Model
{
    protected $fillable = ['semester_id', 'imported_by', 'name', 'type', 'file_url', 'total_records', 'status'];

    public function importer()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
