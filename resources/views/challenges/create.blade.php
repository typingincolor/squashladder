@extends('layouts.app')

@section('title', 'Challenge a Player - ' . config('app.name'))

@section('content')
<div class="form-page">
    <h2>Challenge a Player</h2>

    @if($selectedOpponent)
    <p>Challenging: <strong>{{ $selectedOpponent->full_name }}</strong> (Rank {{ $selectedOpponent->rank }})</p>
    @endif

    <form method="POST" action="{{ route('challenges.store') }}" class="form">
        @csrf

        <div class="form-group">
            <label for="opponent_id">Select Opponent</label>
            <select id="opponent_id" name="opponent_id" required class="form-control">
                <option value="">Select opponent</option>
                @foreach($players as $p)
                    <option value="{{ $p->id }}"
                        {{ (old('opponent_id') == $p->id || ($selectedOpponent && $selectedOpponent->id == $p->id)) ? 'selected' : '' }}>
                        {{ $p->full_name }} (Rank: {{ $p->rank }})
                    </option>
                @endforeach
            </select>
            @error('opponent_id')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="message">Challenge Message</label>
            <textarea
                id="message"
                name="message"
                rows="5"
                required
                class="form-control"
                placeholder="Enter your challenge message..."
                maxlength="500"
            >{{ old('message') }}</textarea>
            @error('message')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Send Challenge</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
