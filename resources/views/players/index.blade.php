@extends('layouts.app')

@section('title', 'All Players')

@section('content')
<div class="players-page">
    <h2>All Players</h2>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($players as $player)
                    <tr>
                        <td>{{ $player->rank }}</td>
                        <td>{{ $player->full_name }}</td>
                        <td>{{ $player->email }}</td>
                        <td>
                            <a href="{{ route('players.edit', $player) }}" class="btn btn-small">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">No players found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
