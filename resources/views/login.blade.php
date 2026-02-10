@extends('layouts.app')

@section('title', 'Connexion')
@section('no-header', true)

@section('content')
    <div class="d-flex flex-column justify-content-center min-vh-100">
        <div class="text-center mb-4">
            <h1 class="display-4 fw-bold text-success mb-0">AgrIA</h1>
            <p class="text-muted">Système de Pulvérisation RTK</p>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ url('/login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="votre@email.com"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Mot de passe</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••"
                            required>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                        Se connecter
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection