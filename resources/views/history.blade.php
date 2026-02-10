@extends('layouts.app')

@section('title', 'Historique')

@section('content')
    <div style="margin-bottom: 20px;">
        <h3 style="margin:0">Missions terminées</h3>
    </div>

    @forelse($missions as $mission)
        <a href="{{ url('/missions/' . $mission->id) }}" style="text-decoration: none; color: inherit;">
            <div class="card" style="display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="font-size: 1.1rem;">{{ $mission->nom }}</strong>
                    <div style="display: flex; gap: 5px; align-items: center;">
                        {{-- @if($mission->trashed())
                        <span
                            style="display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; background: #ffebee; color: #c62828;">
                            Supprimée
                        </span>
                        @endif --}}
                        <span style="font-size: 0.8rem; color: #999;">{{ $mission->date_mission }}</span>
                    </div>
                </div>

                <div style="font-size: 0.9rem; color: #666;">
                    🌿 {{ $mission->culture }} | 🚜 {{ $mission->operator->name ?? 'N/A' }}
                </div>

                <div
                    style="display: flex; gap: 15px; font-size: 0.85rem; border-top: 1px solid #eee; padding-top: 8px; margin-top: 5px;">
                    <span>📍 {{ $mission->points_trajet_count }} points</span>
                    <span>⚠️ {{ $mission->detections_count ?? 0 }} détections</span>
                </div>
            </div>
        </a>
    @empty
        <div class="card" style="text-align: center; color: #999;">
            Aucun historique disponible.
        </div>
    @endforelse

    <a href="{{ url('/missions') }}" class="btn btn-outline" style="margin-top: 20px;">
        Retour aux missions
    </a>
@endsection