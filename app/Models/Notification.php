<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    const TYPE_LEAD = 'lead';
    const TYPE_TRANSACTION = 'transaction';
    const TYPE_TASK = 'task';
    const TYPE_DOCUMENT = 'document';
    const TYPE_DOCUMENT_FOLDER = 'document_folder';
    const TYPE_PAYMENT = 'payment';
    const TYPE_REMINDER = 'reminder';
    const TYPE_CANCELLATION = 'cancellation';
    const TYPE_PROMOTION = 'promotion';
    const TYPE_GENERAL = 'general';

    protected $fillable = [
        'user_id',
        'order_id',
        'slug',
        'type',
        'message',
        'is_read',
        'status',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    public function markAsUnread(): void
    {
        if ($this->is_read) {
            $this->update(['is_read' => false]);
        }
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getIconAttribute(): string
    {
        $icons = [
            self::TYPE_LEAD => '👤',
            self::TYPE_TRANSACTION => '💸',
            self::TYPE_TASK => '📋',
            self::TYPE_DOCUMENT => '📄',
            self::TYPE_DOCUMENT_FOLDER => '📁',
            self::TYPE_PAYMENT => '💳',
            self::TYPE_REMINDER => '⏰',
            self::TYPE_CANCELLATION => '✖',
            self::TYPE_PROMOTION => '🎁',
            self::TYPE_GENERAL => 'ℹ',
        ];

        return $icons[$this->type] ?? '•';
    }

    public function getPriorityAttribute(): int
    {
        $priorities = [
            self::TYPE_CANCELLATION => 1,
            self::TYPE_PAYMENT => 2,
            self::TYPE_REMINDER => 3,
            self::TYPE_TASK => 4,
            self::TYPE_TRANSACTION => 5,
            self::TYPE_LEAD => 6,
            self::TYPE_DOCUMENT => 7,
            self::TYPE_DOCUMENT_FOLDER => 8,
            self::TYPE_PROMOTION => 9,
            self::TYPE_GENERAL => 10,
        ];

        return $priorities[$this->type] ?? 999;
    }

    public static function getAllTypes(): array
    {
        return [
            self::TYPE_LEAD,
            self::TYPE_TRANSACTION,
            self::TYPE_TASK,
            self::TYPE_DOCUMENT,
            self::TYPE_DOCUMENT_FOLDER,
            self::TYPE_PAYMENT,
            self::TYPE_REMINDER,
            self::TYPE_CANCELLATION,
            self::TYPE_PROMOTION,
            self::TYPE_GENERAL,
        ];
    }

    public static function getTypesForUser(): array
    {
        return [
            self::TYPE_PAYMENT,
            self::TYPE_REMINDER,
            self::TYPE_CANCELLATION,
            self::TYPE_PROMOTION,
            self::TYPE_TRANSACTION,
            self::TYPE_GENERAL,
        ];
    }

    public static function getTypesForAgent(): array
    {
        return [
            self::TYPE_LEAD,
            self::TYPE_TASK,
            self::TYPE_DOCUMENT,
            self::TYPE_DOCUMENT_FOLDER,
            self::TYPE_TRANSACTION,
            self::TYPE_PAYMENT,
            self::TYPE_REMINDER,
            self::TYPE_CANCELLATION,
            self::TYPE_GENERAL,
        ];
    }

    public static function getTypesForAdmin(): array
    {
        return self::getAllTypes();
    }
}
