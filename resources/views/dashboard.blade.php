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
            width: 48px;
            height: 48px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            background: rgba(25, 135, 84, 0.08);
            color: #198754;
        }
    </style>
@endsection

@section('content')
    <div class="row g-4 pt-2">
        <div class="col-6 col-md-4">
            <a href="{{ route('map') }}"
                class="card db-card h-100 text-decoration-none text-dark shadow-sm text-center p-3">
                <div class="db-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21 3 6"></polygon>
                        <line x1="9" y1="3" x2="9" y2="18"></line>
                        <line x1="15" y1="6" x2="15" y2="21"></line>
                    </svg>
                </div>
                <div class="fw-bold">Supervision Live</div>
            </a>
        </div>

        <div class="col-6 col-md-4">
            <a href="{{ route('missions') }}"
                class="card db-card h-100 text-decoration-none text-dark shadow-sm text-center p-3">
                <div class="db-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                        <path d="M9 3h6v4H9z"></path>
                        <line x1="8" y1="11" x2="16" y2="11"></line>
                        <line x1="8" y1="15" x2="13" y2="15"></line>
                    </svg>
                </div>
                <div class="fw-bold">Planification</div>
            </a>
        </div>

        <div class="col-6 col-md-4">
            <a href="{{ route('history') }}"
                class="card db-card h-100 text-decoration-none text-dark shadow-sm text-center p-3">
                <div class="db-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 3h11a2 2 0 0 1 2 2v14l-4-3-4 3-4-3-3 2V5a2 2 0 0 1 2-2z"></path>
                        <line x1="8" y1="7" x2="15" y2="7"></line>
                        <line x1="8" y1="11" x2="13" y2="11"></line>
                    </svg>
                </div>
                <div class="fw-bold">Historique</div>
            </a>
        </div>

        <div class="col-6 col-md-4">
            <a href="{{ route('statistics') }}"
                class="card db-card h-100 text-decoration-none text-dark shadow-sm text-center p-3">
                <div class="db-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <div class="fw-bold">Statistiques</div>
            </a>
        </div>

        <div class="col-6 col-md-4">
            <a href="{{ url('/options') }}"
               class="card db-card h-100 text-decoration-none text-dark shadow-sm text-center p-3">
                <div class="db-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path
                            d="M19.4 15A1.65 1.65 0 0 0 20 13.6a1.65 1.65 0 0 0-.33-1l2-3.46a1 1 0 0 0-.37-1.37l-2.12-1.22a1 1 0 0 0-1.45.36l-2 3.46a1.7 1.7 0 0 0-.95 0l-2-3.46a1 1 0 0 0-1.45-.36L4.7 7.8a1 1 0 0 0-.37 1.37l2 3.46a1.65 1.65 0 0 0 0 2l-2 3.46a1 1 0 0 0 .37 1.37l2.12 1.22a1 1 0 0 0 1.45-.36l2-3.46a1.7 1.7 0 0 0 .95 0l2 3.46a1 1 0 0 0 1.45.36l2.12-1.22a1 1 0 0 0 .37-1.37Z">
                        </path>
                    </svg>
                </div>
                <div class="fw-bold">Paramètres</div>
            </a>
        </div>
    </div>
@endsection