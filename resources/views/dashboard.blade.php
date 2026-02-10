@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('styles')
    <style>
        .db-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 16px;
        }

        .db-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .db-icon {
            font-size: 3.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="row g-4 pt-2">
        <div class="col-6 col-md-4">
            <a href="{{ route('map') }}"
                class="card db-card h-100 text-decoration-none text-dark shadow-sm text-center p-3">
                <div class="db-icon">🗺️</div>
                <div class="fw-bold">Supervision Live</div>
            </a>
        </div>

        <div class="col-6 col-md-4">
            <a href="{{ route('missions') }}"
                class="card db-card h-100 text-decoration-none text-dark shadow-sm text-center p-3">
                <div class="db-icon">📋</div>
                <div class="fw-bold">Planification</div>
            </a>
        </div>

        <div class="col-6 col-md-4">
            <a href="{{ route('history') }}"
                class="card db-card h-100 text-decoration-none text-dark shadow-sm text-center p-3">
                <div class="db-icon">📜</div>
                <div class="fw-bold">Historique</div>
            </a>
        </div>

        <div class="col-6 col-md-4">
            <div class="card db-card h-100 shadow-sm text-center p-3 opacity-50" style="cursor: not-allowed">
                <div class="db-icon">⚙️</div>
                <div class="fw-bold text-muted">Paramètres</div>
            </div>
        </div>
    </div>
@endsection