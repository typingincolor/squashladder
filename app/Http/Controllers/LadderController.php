<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Result;
use Illuminate\View\View;

class LadderController extends Controller
{
    /**
     * Display the ladder standings and recent results
     */
    public function index(): View
    {
        $players = Player::byRank()->get();
        $recentResults = Result::with(['player1', 'player2', 'resultDescription'])
            ->recent(7)
            ->get();

        return view('ladder.index', compact('players', 'recentResults'));
    }
}
