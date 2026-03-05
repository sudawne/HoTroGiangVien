<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationComment extends Model
{
    protected $fillable = ['notification_id', 'user_id', 'content', 'parent_id'];

    // Lấy người dùng đã bình luận
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Lấy bài viết (thông báo) chứa bình luận này
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function parent()
    {
        return $this->belongsTo(NotificationComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(NotificationComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}
