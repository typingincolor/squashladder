<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'forename',
        'surname',
        'email',
        'rank',
    ];

    /**
     * Get all results where this player is player 1
     */
    public function resultsAsPlayer1(): HasMany
    {
        return $this->hasMany(Result::class, 'player1_id');
    }

    /**
     * Get all results where this player is player 2
     */
    public function resultsAsPlayer2(): HasMany
    {
        return $this->hasMany(Result::class, 'player2_id');
    }

    /**
     * Get the user account associated with this player
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all challenges sent by this player
     */
    public function challengesSent(): HasMany
    {
        return $this->hasMany(Challenge::class, 'challenger_id');
    }

    /**
     * Get all challenges received by this player
     */
    public function challengesReceived(): HasMany
    {
        return $this->hasMany(Challenge::class, 'opponent_id');
    }

    /**
     * Get all results for this player (either as player 1 or player 2)
     */
    public function allResults()
    {
        return Result::where('player1_id', $this->id)
            ->orWhere('player2_id', $this->id)
            ->orderBy('match_date', 'desc')
            ->get();
    }

    /**
     * Get the player's full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->forename} {$this->surname}";
    }

    /**
     * Swap ranks with another player
     */
    public function swapRankWith(Player $otherPlayer): void
    {
        \DB::transaction(function () use ($otherPlayer) {
            $tempRank = $this->rank;
            $otherRank = $otherPlayer->rank;

            // Use negative values temporarily to avoid unique constraint
            $this->update(['rank' => -1]);
            $otherPlayer->update(['rank' => -2]);

            // Now set the final values
            $this->update(['rank' => $otherRank]);
            $otherPlayer->update(['rank' => $tempRank]);
        });

        // Refresh models to get updated values
        $this->refresh();
        $otherPlayer->refresh();
    }

    /**
     * Get all players ordered by rank
     */
    public static function ladder()
    {
        return static::orderBy('rank')->get();
    }

    /**
     * Scope a query to order by rank
     */
    public function scopeByRank($query)
    {
        return $query->orderBy('rank');
    }
}
