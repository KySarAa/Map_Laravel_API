@extends('layouts.app')

@section('title', 'Détails Mission')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 350px;
            width: 100%;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection

@section('content')
    <div id="map"></div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3 text-success">Statistiques</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                            <span class="fw-semibold text-muted">Nom</span>
                            <span class="fw-bold">{{ $mission->nom }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                            <span class="fw-semibold text-muted">Statut</span>
                            <span class="badge bg-success rounded-pill px-3">{{ $mission->statut }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                            <span class="fw-semibold text-muted">Culture</span>
                            <span>{{ $mission->culture ?? 'N/A' }}</span>
                        </li>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom-0">
                            <span class="fw-semibold text-muted">Opérateur</span>
                            <span>{{ $mission->operator->name ?? 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3 text-success">Données Récoltées</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                            <span class="fw-semibold text-muted">Points Trajet</span>
                            <span id="points-count"
                                class="badge bg-light text-dark shadow-sm px-3 border">{{ count($mission->pointsTrajet) }}
                                pts</span>
                        </li>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom-0">
                            <span class="fw-semibold text-muted">Détections IA</span>
                            <span id="detections-count"
                                class="badge bg-danger rounded-pill px-3">{{ count($mission->detections) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="d-grid shadow-sm rounded-3 overflow-hidden">
        <a href="{{ url('/missions') }}" class="btn btn-outline-success py-2 fw-bold">
            Retour à la liste
        </a>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        var pathGroup = L.layerGroup().addTo(map);
        var detectionGroup = L.layerGroup().addTo(map);
        var pathLine = null;
        var lastPointCount = 0;
        var missionId = "{{ $mission->id }}";

        function refreshMissionData() {
            fetch('/api/mission/' + missionId + '/path')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        var points = data.points;

                        if (points.length > lastPointCount) {
                            pathGroup.clearLayers();
                            var latLngs = points.map(p => [parseFloat(p.latitude), parseFloat(p.longitude)]);

                            if (pathLine) map.removeLayer(pathLine);

                            pathLine = L.polyline(latLngs, { color: '#198754', weight: 5 }).addTo(map);

                            if (lastPointCount === 0) {
                                map.fitBounds(pathLine.getBounds(), { padding: [20, 20] });
                            }

                            document.getElementById('points-count').innerText = points.length + " pts";
                            lastPointCount = points.length;
                        }
                    }
                })
                .catch(err => console.error('Erreur rafraîchissement mission:', err));
        }

        refreshMissionData();
        setInterval(refreshMissionData, 3000);

        var detections = {!! json_encode($mission->detections, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) !!};
        detections.forEach(function (d) {
            L.circleMarker([d.latitude, d.longitude], {
                radius: 6,
                color: '#dc3545',
                fillColor: '#dc3545',
                fillOpacity: 0.8,
                weight: 2
            }).addTo(detectionGroup).bindPopup("IA: " + d.class_ia + " (" + d.confidence + "%)");
        });
    </script>
@endsection