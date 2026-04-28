@extends('layouts.app')

@section('title', 'Statistiques')

@section('content')
    <div class="row g-4 pt-2">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h4 class="card-title fw-bold text-success mb-4 d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" class="me-2"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        Tableau des Mauvaises Herbes
                        <span class="badge bg-success ms-auto rounded-pill">{{ $weedsCount }} total</span>
                    </h4>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Mission</th>
                                    <th scope="col">Date & Heure</th>
                                    <th scope="col">Position (Lat, Lng)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($weeds as $weed)
                                    <tr>
                                        <td>#{{ $weed->id }}</td>
                                        <td>{{ $weed->mission->nom ?? 'N/A' }}</td>
                                        <td>{{ $weed->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ number_format($weed->latitude, 6) }}, {{ number_format($weed->longitude, 6) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Aucune mauvaise herbe détectée pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    Retour au Tableau de bord
                </a>
            </div>
        </div>
    </div>
@endsection
