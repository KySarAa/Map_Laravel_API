@extends('layouts.app')

@section('title', 'Missions')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold">En cours</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('missions.create') }}" class="btn btn-success shadow-sm rounded-3 px-3">+ Nouvelle Mission</a>
            <a href="{{ url('/map') }}" class="btn btn-outline-success shadow-sm rounded-3 px-3">Voir Carte</a>
        </div>
    </div>

    @forelse($missions as $mission)
        <div class="position-relative mb-3">
            <a href="{{ url('/missions/' . $mission->id) }}" class="text-decoration-none text-dark d-block">
                <div class="card shadow-sm border-0 rounded-4 pe-5 border-start border-success border-4">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">{{ $mission->nom }}</h5>
                            <div class="small text-muted">
                                🌿 {{ $mission->culture }} <span class="mx-1">|</span> 🚜
                                {{ $mission->operator->name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="text-end">
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 mb-1">
                                {{ $mission->statut }}
                            </span>
                            <div class="small text-muted opacity-75">
                                {{ $mission->date_mission }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <form action="{{ route('missions.destroy', $mission->id) }}" method="POST"
                class="position-absolute end-0 top-50 translate-middle-y me-3"
                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette mission ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-link text-danger p-2" title="Supprimer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </button>
            </form>
        </div>
    @empty
        <div class="card shadow-sm border-0 rounded-4 text-center p-5 text-muted">
            <div class="display-1 mb-3">📭</div>
            Aucune mission enregistrée.
        </div>
    @endforelse

    <div class="d-grid mt-4 shadow-sm rounded-3 overflow-hidden">
        <a href="{{ url('/history') }}" class="btn btn-outline-success py-2 fw-bold">
            Voir l'historique complet
        </a>
    </div>
@endsection