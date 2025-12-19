<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_date',
        'player1_id',
        'player2_id',
        'result_description_id',
    ];

    protected $casts = [
        'match_date' => 'date',
    ];

    /**
     * Get the first player
     */
    public function player1(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player1_id');
    }

    /**
     * Get the second player
     */
    public function player2(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player2_id');
    }

    /**
     * Get the result description
     */
    public function resultDescription(): BelongsTo
    {
        return $this->belongsTo(ResultDescription::class);
    }

    /**
     * Check if player1 won
     */
    public function player1Won(): bool
    {
        return $this->result_description_id === 1; // 'beat'
    }

    /**
     * Check if the match was a draw
     */
    public function isDraw(): bool
    {
        return $this->result_description_id === 2; // 'drew with'
    }

    /**
     * Get the winner of the match (null if draw)
     */
    public function getWinner(): ?Player
    {
        if ($this->isDraw()) {
            return null;
        }

        return $this->player1Won() ? $this->player1 : $this->player2;
    }

    /**
     * Get the loser of the match (null if draw)
     */
    public function getLoser(): ?Player
    {
        if ($this->isDraw()) {
            return null;
        }

        return $this->player1Won() ? $this->player2 : $this->player1;
    }

    /**
     * Process the result and update player rankings
     */
    public function processRankings(): void
    {
        // Only swap ranks if player1 won and player2 had a higher rank (lower number)
        if ($this->player1Won() && $this->player2->rank < $this->player1->rank) {
            $this->player1->swapRankWith($this->player2);
        }
        // If player2 won and player1 had a higher rank
        elseif (!$this->player1Won() && !$this->isDraw() && $this->player1->rank < $this->player2->rank) {
            $this->player2->swapRankWith($this->player1);
        }
    }

    /**
     * Scope for recent results (last N days)
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('match_date', '>=', now()->subDays($days))
            ->orderBy('match_date', 'desc');
    }
}
