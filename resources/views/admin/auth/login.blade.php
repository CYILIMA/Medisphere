@extends('admin.layouts.app')

@section('title', 'Administrator sign in')

@section('content')
    <section class="admin-auth-panel">
        <span class="admin-eyebrow">MediSphere / Admin</span>
        <h1>Welcome back.</h1>
        <p>Sign in to manage medicine information and doctor contacts.</p>
        <form method="POST" action="{{ route('admin.login.store') }}" class="admin-form">
            @csrf
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" autocomplete="username" value="{{ old('email') }}" required autofocus>
            @error('email') <p class="admin-error">{{ $message }}</p> @enderror
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            @error('password') <p class="admin-error">{{ $message }}</p> @enderror
            <label class="admin-check"><input name="remember" type="checkbox" value="1"> Keep me signed in</label>
            <button class="admin-button" type="submit">Sign in <span aria-hidden="true">→</span></button>
        </form>
        <a class="admin-back-link" href="{{ route('home') }}">← Back to MediSphere</a>
    </section>
@endsection
