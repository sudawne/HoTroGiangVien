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
        'class_id',
        'status',
        'allow_comments',
    ];

    // Người gửi thông báo
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Gửi cho lớp nào
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
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
