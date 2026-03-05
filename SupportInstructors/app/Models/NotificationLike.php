<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLike extends Model
{
    protected $fillable = ['notification_id', 'user_id'];

    // Lấy người dùng đã thả tim
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Lấy bài viết (thông báo) được thả tim
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
