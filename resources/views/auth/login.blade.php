@extends('layouts.app')

@section('title', 'Login - ' . config('app.name'))

@section('content')
<div class="form-page">
    <h2>Login</h2>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="form">
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

        <div class="form-group">
            <label for="password">Password</label>
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
            <label class="checkbox-label">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>Remember Me</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>

        <div style="text-align: center; margin-top: 1rem;">
            <a href="{{ route('password.request') }}" style="color: var(--primary-color);">Forgot Your Password?</a>
        </div>
    </form>
</div>
@endsection
