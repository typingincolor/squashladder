@extends('layouts.app')

@section('title', 'Current Ladder Standings')

@section('content')
<div class="ladder-page">
    <section class="ladder-section">
        <h2>Current Ladder Standings</h2>
        <div class="table-responsive">
            <table class="table ladder-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Player</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($players as $player)
                        <tr>
                            <td class="rank">{{ $player->rank }}</td>
                            <td class="player-name">{{ $player->full_name }}</td>
                            <td class="email">{{ $player->email }}</td>
                            <td>
                                <a href="{{ route('players.edit', $player) }}" class="btn btn-small">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">No players in the ladder yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="recent-results-section">
        <h2>Recent Results (Last 7 Days)</h2>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentResults as $result)
                        <tr>
                            <td class="date">{{ $result->match_date->format('d/m/Y') }}</td>
                            <td class="result-text">
                                <strong>{{ $result->player1->full_name }}</strong>
                                {{ $result->resultDescription->description }}
                                <strong>{{ $result->player2->full_name }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="empty-state">No recent results.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
