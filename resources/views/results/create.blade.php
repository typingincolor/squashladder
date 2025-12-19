@extends('layouts.app')

@section('title', 'Enter Match Result - ' . config('app.name'))

@section('content')
<div class="form-page">
    <h2>Enter Match Result</h2>

    @if(isset($challenge) && $challenge)
    <div class="alert alert-info" style="background: #e3f2fd; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
        <strong>Challenge Result:</strong> {{ $challenge->challenger->full_name }} vs {{ $challenge->opponent->full_name }}
    </div>
    @endif

    <form method="POST" action="{{ route('results.store') }}" class="form">
        @csrf

        @if(isset($challenge) && $challenge)
            <input type="hidden" name="challenge_id" value="{{ $challenge->id }}">
        @endif

        <div class="form-group">
            <label for="match_date">Match Date</label>
            <input
                type="date"
                id="match_date"
                name="match_date"
                value="{{ old('match_date', date('Y-m-d')) }}"
                max="{{ date('Y-m-d') }}"
                required
                class="form-control"
            >
            @error('match_date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="player1_id">Player 1</label>
            @if(isset($challenge) && $challenge)
                <input type="text" class="form-control" value="{{ $challenge->challenger->full_name }} (Rank: {{ $challenge->challenger->rank }})" readonly style="background-color: #f5f5f5;">
                <input type="hidden" name="player1_id" value="{{ $challenge->challenger_id }}">
            @else
                <select id="player1_id" name="player1_id" required class="form-control">
                    <option value="">Select Player 1</option>
                    @foreach($players as $player)
                        <option value="{{ $player->id }}" {{ old('player1_id') == $player->id ? 'selected' : '' }}>
                            {{ $player->full_name }} (Rank: {{ $player->rank }})
                        </option>
                    @endforeach
                </select>
            @endif
            @error('player1_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="result_description_id">Result</label>
            <select id="result_description_id" name="result_description_id" required class="form-control">
                <option value="">Select Result</option>
                @foreach($resultDescriptions as $desc)
                    <option value="{{ $desc->id }}" {{ old('result_description_id') == $desc->id ? 'selected' : '' }}>
                        {{ $desc->description }}
                    </option>
                @endforeach
            </select>
            @error('result_description_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="player2_id">Player 2</label>
            @if(isset($challenge) && $challenge)
                <input type="text" class="form-control" value="{{ $challenge->opponent->full_name }} (Rank: {{ $challenge->opponent->rank }})" readonly style="background-color: #f5f5f5;">
                <input type="hidden" name="player2_id" value="{{ $challenge->opponent_id }}">
            @else
                <select id="player2_id" name="player2_id" required class="form-control">
                    <option value="">Select Player 2</option>
                    @foreach($players as $player)
                        <option value="{{ $player->id }}" {{ old('player2_id') == $player->id ? 'selected' : '' }}>
                            {{ $player->full_name }} (Rank: {{ $player->rank }})
                        </option>
                    @endforeach
                </select>
            @endif
            @error('player2_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Submit Result</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
