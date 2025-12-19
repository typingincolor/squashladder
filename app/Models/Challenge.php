<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Challenge extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'challenger_id',
        'opponent_id',
        'message',
        'status',
        'accepted_at',
        'rejected_at',
        'result_id',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the player who sent the challenge
     */
    public function challenger(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'challenger_id');
    }

    /**
     * Get the player who received the challenge
     */
    public function opponent(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'opponent_id');
    }

    /**
     * Get the result associated with this challenge
     */
    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class);
    }

    /**
     * Scope a query to only include pending challenges
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include accepted challenges
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Scope a query to only include rejected challenges
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include completed challenges
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Accept the challenge
     */
    public function accept(): void
    {
        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);
    }

    /**
     * Reject the challenge
     */
    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
        ]);
    }

    /**
     * Mark the challenge as completed with a result
     */
    public function markCompleted(Result $result): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'result_id' => $result->id,
        ]);
    }

    /**
     * Check if the challenge can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
