@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@section('content')
<div class="dashboard">
    <h2>Welcome, {{ $player->full_name }}!</h2>

    <!-- Current Ladder Standings -->
    <div class="section">
        <h3>Current Ladder Standings</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ladder as $ladderPlayer)
                <tr class="{{ $ladderPlayer->id === $player->id ? 'highlight' : '' }}">
                    <td>{{ $ladderPlayer->rank }}</td>
                    <td>{{ $ladderPlayer->full_name }}</td>
                    <td>{{ $ladderPlayer->email }}</td>
                    <td>
                        @if($ladderPlayer->id !== $player->id)
                            <a href="{{ route('challenges.create', ['opponent_id' => $ladderPlayer->id]) }}" class="btn btn-sm btn-primary">Challenge</a>
                        @else
                            <span class="text-muted">You</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Challenges Received -->
    <div class="section">
        <h3>Challenges Received</h3>
        @if($receivedChallenges->count() > 0)
            <div class="challenges-grid">
                @foreach($receivedChallenges as $challenge)
                <div class="card challenge-card">
                    <div class="challenge-header">
                        <strong>{{ $challenge->challenger->full_name }}</strong>
                        <span class="badge badge-{{ $challenge->status }}">{{ ucfirst($challenge->status) }}</span>
                    </div>
                    <div class="challenge-message">
                        <p>{{ $challenge->message }}</p>
                    </div>
                    <div class="challenge-meta">
                        <small>Received: {{ $challenge->created_at->diffForHumans() }}</small>
                    </div>
                    @if($challenge->status === 'pending')
                    <div class="challenge-actions">
                        <form method="POST" action="{{ route('challenges.accept', $challenge) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm">Accept</button>
                        </form>
                        <form method="POST" action="{{ route('challenges.reject', $challenge) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    </div>
                    @elseif($challenge->status === 'accepted')
                    <div class="challenge-info">
                        <p class="text-muted">Waiting for {{ $challenge->challenger->full_name }} to enter the result...</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">No pending challenges.</p>
        @endif
    </div>

    <!-- Challenges Sent -->
    <div class="section">
        <h3>Challenges Sent</h3>
        @if($sentChallenges->count() > 0)
            <div class="challenges-grid">
                @foreach($sentChallenges as $challenge)
                <div class="card challenge-card">
                    <div class="challenge-header">
                        <strong>To: {{ $challenge->opponent->full_name }}</strong>
                        <span class="badge badge-{{ $challenge->status }}">{{ ucfirst($challenge->status) }}</span>
                    </div>
                    <div class="challenge-message">
                        <p>{{ $challenge->message }}</p>
                    </div>
                    <div class="challenge-meta">
                        <small>Sent: {{ $challenge->created_at->diffForHumans() }}</small>
                    </div>
                    @if($challenge->status === 'accepted')
                    <div class="challenge-actions">
                        <a href="{{ route('results.create', ['challenge_id' => $challenge->id]) }}" class="btn btn-primary btn-sm">Enter Result</a>
                    </div>
                    @elseif($challenge->status === 'rejected')
                    <div class="challenge-actions">
                        <form method="POST" action="{{ route('challenges.destroy', $challenge) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this challenge?')">Delete</button>
                        </form>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">No sent challenges.</p>
        @endif
    </div>

    <!-- Recent Results -->
    <div class="section">
        <h3>Recent Results</h3>
        @if($recentResults->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Match Result</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentResults as $result)
                    <tr>
                        <td>{{ $result->match_date->format('d M Y') }}</td>
                        <td>
                            {{ $result->player1->full_name }}
                            {{ $result->resultDescription->description }}
                            {{ $result->player2->full_name }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">No recent results.</p>
        @endif
    </div>
</div>
@endsection
