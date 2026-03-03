<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingMinute extends Model
{
    protected $fillable = [
        'class_id',
        'semester_id',
        'created_by',
        'title',
        'location',         // Mới
        'held_at',          // Mới
        'ended_at',         // Mới
        'monitor_id',       // Mới
        'secretary_id',     // Mới
        'content_discussions', // Mới
        'content_conclusion',  // Mới
        'content_requests',    // Mới
        'attendees_count',
        'absent_list',
        'file_url',
        'status'
    ];

    protected $casts = [
        'held_at' => 'datetime',
        'ended_at' => 'datetime',
        'absent_list' => 'array', 
    ];
    public function monitor() {
        return $this->belongsTo(Student::class, 'monitor_id');
    }

    public function secretary() {
        return $this->belongsTo(Student::class, 'secretary_id');
    }
}
