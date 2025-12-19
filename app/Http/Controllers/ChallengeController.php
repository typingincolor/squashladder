<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ChallengeController extends Controller
{
    /**
     * Show the form for challenging a player
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $player = $user->player;

        if (!$player) {
            abort(403, 'No player profile associated with your account.');
        }

        // Get all players except the current player
        $players = Player::byRank()
            ->where('id', '!=', $player->id)
            ->get();

        // Pre-select opponent if specified
        $selectedOpponentId = $request->input('opponent_id');
        $selectedOpponent = null;

        if ($selectedOpponentId) {
            $selectedOpponent = Player::find($selectedOpponentId);
            // Validate the selected opponent exists and is not the current player
            if (!$selectedOpponent || $selectedOpponent->id === $player->id) {
                $selectedOpponent = null;
            }
        }

        return view('challenges.create', compact('players', 'player', 'selectedOpponent'));
    }

    /**
     * Create a challenge and send notification email
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $player = $user->player;

        if (!$player) {
            abort(403, 'No player profile associated with your account.');
        }

        $validated = $request->validate([
            'opponent_id' => 'required|exists:players,id|different:challenger_id',
            'message' => 'required|string|max:500',
        ]);

        $opponent = Player::findOrFail($validated['opponent_id']);

        // Create challenge in database
        $challenge = Challenge::create([
            'challenger_id' => $player->id,
            'opponent_id' => $validated['opponent_id'],
            'message' => $validated['message'],
            'status' => Challenge::STATUS_PENDING,
        ]);

        // Send email notification to opponent
        try {
            Mail::raw(
                "You have been challenged by {$player->full_name} (Rank: {$player->rank}).\n\n" .
                "Message: {$validated['message']}\n\n" .
                "Log in to your account to accept or reject this challenge.",
                function ($mail) use ($opponent, $player) {
                    $mail->to($opponent->email)
                        ->subject('Squash Ladder Challenge from ' . $player->full_name)
                        ->replyTo($player->email);
                }
            );
        } catch (\Exception $e) {
            // Log the error but don't fail the request
        }

        return redirect()
            ->route('dashboard')
            ->with('success', "Challenge sent to {$opponent->full_name}!");
    }

    /**
     * Accept a challenge
     */
    public function accept(Request $request, Challenge $challenge): RedirectResponse
    {
        $user = $request->user();
        $player = $user->player;

        // Only the opponent can accept
        if ($challenge->opponent_id !== $player->id) {
            abort(403, 'You are not authorized to accept this challenge.');
        }

        if ($challenge->status !== Challenge::STATUS_PENDING) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'This challenge has already been responded to.');
        }

        $challenge->accept();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Challenge accepted! Waiting for the challenger to enter the result.');
    }

    /**
     * Reject a challenge
     */
    public function reject(Request $request, Challenge $challenge): RedirectResponse
    {
        $user = $request->user();
        $player = $user->player;

        // Only the opponent can reject
        if ($challenge->opponent_id !== $player->id) {
            abort(403, 'You are not authorized to reject this challenge.');
        }

        if ($challenge->status !== Challenge::STATUS_PENDING) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'This challenge has already been responded to.');
        }

        $challenge->reject();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Challenge rejected.');
    }

    /**
     * Delete a rejected challenge
     */
    public function destroy(Request $request, Challenge $challenge): RedirectResponse
    {
        $user = $request->user();
        $player = $user->player;

        // Only challenger or opponent can delete
        if ($challenge->challenger_id !== $player->id && $challenge->opponent_id !== $player->id) {
            abort(403, 'You are not authorized to delete this challenge.');
        }

        // Only rejected challenges can be deleted
        if (!$challenge->canBeDeleted()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Only rejected challenges can be deleted.');
        }

        $challenge->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Challenge deleted.');
    }
}
