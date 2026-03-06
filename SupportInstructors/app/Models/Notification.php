<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'related_batch_id',
        'title',
        'message',
        'type',
        'attachment_url',
        'attachment_name',
        'target_audience',
        'status',
        'allow_comments',
    ];

    // Người gửi thông báo
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_notification', 'notification_id', 'class_id');
    }

    // Người đã Like
    public function likes()
    {
        return $this->hasMany(NotificationLike::class);
    }

    // Các bình luận
    public function comments()
    {
        return $this->hasMany(NotificationComment::class)->orderBy('created_at', 'desc');
    }

    // Kiểm tra xem User hiện tại đã like chưa
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}
