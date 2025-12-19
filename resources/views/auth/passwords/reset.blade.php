@extends('layouts.app')

@section('title', 'Reset Password - ' . config('app.name'))

@section('content')
<div class="form-page">
    <h2>Reset Password</h2>

    <form method="POST" action="{{ route('password.update') }}" class="form">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ $email ?? old('email') }}"
                required
                autofocus
                class="form-control @error('email') is-invalid @enderror"
            >
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">New Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                class="form-control @error('password') is-invalid @enderror"
            >
            @error('password')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
                class="form-control"
            >
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </div>

        <div style="text-align: center; margin-top: 1rem;">
            <a href="{{ route('login') }}" style="color: var(--primary-color);">Back to Login</a>
        </div>
    </form>
</div>
@endsection
