@extends('layouts.app')

@section('title', 'Reset Password - ' . config('app.name'))

@section('content')
<div class="form-page">
    <h2>Reset Password</h2>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <p style="text-align: center; color: var(--text-secondary); margin-bottom: 2rem;">Enter your email address and we'll send you a link to reset your password.</p>

    <form method="POST" action="{{ route('password.email') }}" class="form">
        @csrf

        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="form-control @error('email') is-invalid @enderror"
            >
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </div>

        <div style="text-align: center; margin-top: 1rem;">
            <a href="{{ route('login') }}" style="color: var(--primary-color);">Back to Login</a>
        </div>
    </form>
</div>
@endsection
