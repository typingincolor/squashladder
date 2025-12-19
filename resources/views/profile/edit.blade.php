@extends('layouts.app')

@section('title', 'Edit Profile - ' . config('app.name'))

@section('content')
<div class="form-page">
    <h2>Edit My Profile</h2>

    <form method="POST" action="{{ route('profile.update') }}" class="form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="forename">First Name</label>
            <input
                type="text"
                id="forename"
                name="forename"
                value="{{ old('forename', $player->forename) }}"
                required
                maxlength="20"
                class="form-control"
            >
            @error('forename')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="surname">Last Name</label>
            <input
                type="text"
                id="surname"
                name="surname"
                value="{{ old('surname', $player->surname) }}"
                required
                maxlength="30"
                class="form-control"
            >
            @error('surname')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $player->email) }}"
                required
                maxlength="60"
                class="form-control"
            >
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-info">
            <p><strong>Current Rank:</strong> {{ $player->rank }}</p>
            <p class="info-text">Note: Rank cannot be changed directly. Win matches to improve your rank!</p>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
