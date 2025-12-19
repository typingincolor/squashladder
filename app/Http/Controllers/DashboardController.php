<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Result;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with ladder, challenges, and recent results
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $player = $user->player;

        if (!$player) {
            abort(403, 'No player profile associated with your account.');
        }

        // Get ladder standings
        $ladder = Player::byRank()->get();

        // Get challenges received (pending or accepted)
        $receivedChallenges = $player->challengesReceived()
            ->whereIn('status', ['pending', 'accepted'])
            ->with('challenger')
            ->latest()
            ->get();

        // Get challenges sent (all statuses for tracking)
        $sentChallenges = $player->challengesSent()
            ->whereIn('status', ['pending', 'accepted', 'rejected'])
            ->with('opponent')
            ->latest()
            ->get();

        // Get recent results (last 10)
        $recentResults = Result::with(['player1', 'player2', 'resultDescription'])
            ->orderBy('match_date', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'player',
            'ladder',
            'receivedChallenges',
            'sentChallenges',
            'recentResults'
        ));
    }
}
