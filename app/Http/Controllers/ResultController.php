<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Player;
use App\Models\Result;
use App\Models\ResultDescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    /**
     * Show the form for entering a new result
     */
    public function create(Request $request): View
    {
        $players = Player::byRank()->get();
        $resultDescriptions = ResultDescription::all();
        $challenge = null;
        $user = $request->user();
        $player = $user ? $user->player : null;

        // If coming from an accepted challenge
        if ($request->has('challenge_id')) {
            $challenge = Challenge::with(['challenger', 'opponent'])
                ->findOrFail($request->challenge_id);

            // Verify the user is the challenger
            if ($player && $challenge->challenger_id !== $player->id) {
                abort(403, 'Only the challenger can enter the result.');
            }

            // Verify the challenge is accepted
            if ($challenge->status !== Challenge::STATUS_ACCEPTED) {
                abort(400, 'This challenge has not been accepted yet.');
            }
        }

        return view('results.create', compact('players', 'resultDescriptions', 'challenge'));
    }

    /**
     * Store a newly created result
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'match_date' => 'required|date|date_format:Y-m-d|before_or_equal:today',
            'player1_id' => 'required|exists:players,id',
            'player2_id' => 'required|exists:players,id|different:player1_id',
            'result_description_id' => 'required|exists:result_descriptions,id',
            'challenge_id' => 'nullable|exists:challenges,id',
        ]);

        // Create the result
        $result = Result::create($validated);

        // Load player relationships for ranking processing
        $result->load(['player1', 'player2']);

        // If this result is from a challenge, mark the challenge as completed
        if (isset($validated['challenge_id'])) {
            $challenge = Challenge::findOrFail($validated['challenge_id']);

            // Verify the user is the challenger
            $user = $request->user();
            $player = $user->player;

            if ($challenge->challenger_id !== $player->id) {
                abort(403, 'Only the challenger can enter the result.');
            }

            // Verify the challenge is accepted
            if ($challenge->status !== Challenge::STATUS_ACCEPTED) {
                abort(400, 'This challenge has not been accepted.');
            }

            // Mark challenge as completed
            $challenge->markCompleted($result);
        }

        // Process rankings based on the result
        $result->processRankings();

        $redirectRoute = $request->user() ? 'dashboard' : 'ladder.index';

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Result entered successfully and rankings updated!');
    }

    /**
     * Display all results
     */
    public function index(): View
    {
        $results = Result::with(['player1', 'player2', 'resultDescription'])
            ->orderBy('match_date', 'desc')
            ->paginate(20);

        return view('results.index', compact('results'));
    }
}
