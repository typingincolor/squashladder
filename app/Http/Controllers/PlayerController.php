<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerController extends Controller
{
    /**
     * Show the form for editing the authenticated player's profile
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $player = $user->player;

        if (!$player) {
            abort(403, 'No player profile associated with your account.');
        }

        return view('profile.edit', compact('player'));
    }

    /**
     * Update the authenticated player's profile
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $player = $user->player;

        if (!$player) {
            abort(403, 'No player profile associated with your account.');
        }

        $validated = $request->validate([
            'forename' => 'required|string|max:20',
            'surname' => 'required|string|max:30',
            'email' => 'required|email|max:60|unique:players,email,' . $player->id,
        ]);

        // Update player
        $player->update($validated);

        // Also update user's email if it changed
        if ($validated['email'] !== $user->email) {
            $user->update(['email' => $validated['email']]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profile updated successfully!');
    }
}
