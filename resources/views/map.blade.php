@extends('layouts.app')

@section('title', 'Supervision Mission')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <style>
        .page-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .panel {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 6px 16px rgba(16, 24, 40, 0.06);
        }

        .panel .panel-title {
            font-size: 1.05rem;
            margin: 0;
        }

        .panel .panel-subtitle {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        #map {
            height: 560px;
            width: 100%;
            border-radius: 18px;
            margin-top: 12px;
            z-index: 1;
            box-shadow: 0 6px 16px rgba(16, 24, 40, 0.08);
        }

        .controls {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        body.ui-mobile #map {
            height: 52vh;
        }
    </style>
@endsection

@section('content')
    <div class="page-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <span aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6"></path>
                </svg>
            </span>
            Tableau de bord
        </a>
    </div>

    <!-- Flux Vid&eacute;o & IA -->
    @include('components.video-stream')

    <div class="row g-3 mt-2">
        <div class="col-12 col-lg-5">
            <div class="card panel">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <h3 class="panel-title">IA</h3>
                            <p class="panel-subtitle">Lancer / arrêter la détection</p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <select id="ia_choice" class="form-select" style="max-width: 240px;">
                            <option value="">— Sélectionner un modèle —</option>
                            <option value="yolov5">Yolov5</option>
                            <option value="yolobestpt">Yolobestpt</option>
                        </select>

                        <button type="button" onclick="launchIA()" class="btn btn-success">
                            Lancer
                        </button>
                        <button type="button" onclick="stopAllIA()" class="btn btn-outline-secondary">
                            Stop
                        </button>
                    </div>
                </div>
            </div>

            <div class="card panel mt-3">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <h3 class="panel-title">Contrôle mission</h3>
                            <p class="panel-subtitle">
                                Mission en cours :
                                <strong>{{ optional($mission)->nom ?? 'Aucune' }}</strong>
                            </p>
                        </div>
                        <div style="min-width: 240px;">
                            <select class="form-select"
                                onchange="window.location.href='?mission_id=' + this.value">
                                <option value="">— Charger une mission —</option>
                                @foreach($availableMissions as $avMission)
                                    <option value="{{ $avMission->id }}" {{ optional($mission)->id == $avMission->id ? 'selected' : '' }}>
                                        {{ $avMission->nom }} ({{ $avMission->statut }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="controls">
                        @if(optional($mission)->statut === 'ongoing')
                            <button type="button" onclick="updateStatus('paused')" class="btn btn-outline-secondary">
                                Pause
                            </button>
                            <button type="button" onclick="updateStatus('completed')" class="btn btn-outline-danger">
                                Terminer
                            </button>
                        @else
                            <button type="button" onclick="updateStatus('ongoing')" class="btn btn-success">
                                Démarrer / Reprendre
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div id="map"></div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script>
        // Init Map
        var map = L.map('map').setView([48.8566, 2.3522], 18);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '  OpenStreetMap'
        }).addTo(map);

        var roverMarker = L.marker([48.8566, 2.3522]).addTo(map).bindPopup('Rover RTK');
        var pathGroup = L.layerGroup().addTo(map);
        var detectionGroup = L.layerGroup().addTo(map);
        var pathLine = null; // Pour tracer la ligne du trajet
        var lastPointCount = 0; // Pour suivre le nombre de points
        var workingZone = null; // Variable pour stocker le polygone de la zone de travail
        var editableLayers = new L.FeatureGroup();
        map.addLayer(editableLayers);

        // Options de dessin
        var drawOptions = {
            position: 'topright',
            draw: {
                polyline: false,
                polygon: {
                    allowIntersection: false,
                    drawError: {
                        color: '#e1e100',
                        message: '<strong>Erreur:</strong> vous ne pouvez pas croiser les lignes!'
                    },
                    shapeOptions: {
                        color: '#97009c'
                    }
                },
                circle: false,
                rectangle: true,
                marker: false,
                circlemarker: false
            },
            edit: {
                featureGroup: editableLayers,
                remove: true
            }
        };

        var drawControl = new L.Control.Draw(drawOptions);
        map.addControl(drawControl);

        // Gestion de la cr ation de zone
        map.on(L.Draw.Event.CREATED, function(e) {
            var type = e.layerType,
                layer = e.layer;

            if (type === 'polygon' || type === 'rectangle') {
                // Supprimer l'ancienne zone s'il y en a une (on en veut une seule pour l'instant)
                editableLayers.clearLayers();
                editableLayers.addLayer(layer);
                workingZone = layer;
                console.log("Nouvelle zone de travail d finie");
                // Rafraichir les couleurs des points existants
                // On peut relancer loadGPSPath pour recolorier
                // loadGPSPath(); // Pas id al car fetch async, on verra
            }
        });

        map.on(L.Draw.Event.DELETED, function(e) {
            workingZone = null;
            console.log("Zone de travail supprim e");
        });

        // Algorithme Ray-Casting
        function isPointInPolygon(latlng, polygonLayer) {
            if (!polygonLayer) return false;

            // Leaflet Draw polygon can be complex. Typically getLatLngs()[0] is the outer ring.
            var poly = polygonLayer.getLatLngs();
            if (Array.isArray(poly) && poly.length > 0 && Array.isArray(poly[0])) {
                poly = poly[0]; // Outer ring
            } else {
                // Fallback direct array (Rectangle/Simple Polygon)
            }

            // Safety check
            if (!Array.isArray(poly)) return false;

            var x = latlng.lat,
                y = latlng.lng;
            var inside = false;

            for (var i = 0, j = poly.length - 1; i < poly.length; j = i++) {
                var xi = poly[i].lat,
                    yi = poly[i].lng;
                var xj = poly[j].lat,
                    yj = poly[j].lng;

                var intersect = ((yi > y) != (yj > y)) &&
                    (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                if (intersect) inside = !inside;
            }

            return inside;
        }

        // Fonction pour charger/rafra chir les points GPS
        function loadGPSPath() {
            var missionId = "{{ optional($mission)->id }}";
            if (!missionId) return;

            fetch('/mission/' + missionId + '/path')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.points.length > 0) {
                        var points = data.points;

                        // On redessine tout   chaque fois pour simplifier la coloration dynamique
                        // Optimisation: faire un diff si les perfs chutent
                        if (true) {
                            pathGroup.clearLayers();
                            if (pathLine) {
                                map.removeLayer(pathLine);
                                pathLine = null;
                            }

                            var previousLatLng = null;

                            points.forEach(function(point) {
                                var latLng = L.latLng(parseFloat(point.latitude), parseFloat(point.longitude));

                                // Déterminer la couleur (Vert = dedans, Rouge = dehors, Gris = pas de zone)
                                var color = '#6c757d';
                                if (workingZone) {
                                    if (isPointInPolygon(latLng, workingZone)) {
                                        color = '#198754'; // success
                                    } else {
                                        color = '#dc3545'; // danger
                                    }
                                }

                                // Point
                                L.circleMarker(latLng, {
                                    radius: 3,
                                    color: color,
                                    fillColor: color,
                                    fillOpacity: 0.8
                                }).addTo(pathGroup);

                                // Ligne (segment)
                                if (previousLatLng) {
                                    L.polyline([previousLatLng, latLng], {
                                        color: color, // La ligne prend la couleur du point de destination
                                        weight: 2,
                                        opacity: 0.8
                                    }).addTo(pathGroup);
                                }
                                previousLatLng = latLng;
                            });

                            // Centrer si nouveaux points
                            if (points.length > lastPointCount) {
                                var lastPoint = points[points.length - 1];
                                var ln = L.latLng(parseFloat(lastPoint.latitude), parseFloat(lastPoint.longitude));
                                map.panTo(ln);
                                roverMarker.setLatLng(ln);
                                lastPointCount = points.length;
                                console.log('Mis a jour: ' + points.length + ' points');
                            }
                        }
                    }
                })
                .catch(err => console.error('Erreur chargement points GPS:', err));
        }

        // Charger les points au d marrage
        loadGPSPath();

        // Rafra chir automatiquement toutes les 3 secondes
        setInterval(loadGPSPath, 3000);

        // Pour le dev local, on tente de se connecter sur l'hote actuel, ou sinon mettre l'IP manuellement
        var wsUrl = "ws://" + location.hostname + ":8765";
        var ws = new WebSocket(wsUrl);

        ws.onopen = function() {
            console.log("Connect\u00e9 au Rover via WS");
        };

        ws.onmessage = function(event) {
            try {
                var data = JSON.parse(event.data);

                // Telemetry: { lat, lon, speed, battery, tank, fix }
                if (data.lat && data.lon) {
                    var newLn = new L.LatLng(data.lat, data.lon);
                    roverMarker.setLatLng(newLn);
                    map.panTo(newLn); // Suivre le robot

                    // Trace (optionnel)
                    L.circleMarker(newLn, {
                        radius: 2,
                        color: 'blue'
                    }).addTo(pathGroup);
                }
            } catch (e) {
                console.error("Erreur parsing WS", e);
            }
        };

        ws.onerror = function() {
            console.warn("WS non disponible. Mode d\u00e9mo ?");
        };

        // Fonction API pour changer le statut
        function updateStatus(newStatus) {
            var missionId = "{{ optional($mission)->id }}";
            if (!missionId) {
                alert("Aucune mission charg\u00e9e !");
                return;
            }

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('/mission/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...(csrf ? {
                            'X-CSRF-TOKEN': csrf
                        } : {})
                    },
                    body: JSON.stringify({
                        mission_id: missionId,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload(); // Recharger pour mettre à jour l'UI
                    } else {
                        alert("Erreur: " + data.message);
                    }
                })
                .catch(err => console.error(err));
        }
    </script>

    <script>
        function launchIA() {
            const ia = document.getElementById('ia_choice').value;
            if (!ia) {
                alert("Choisis une IA avant de lancer.");
                return;
            }

            fetch("/start-ia/" + ia, {
                    method: "GET"
                })
                .catch(err => {
                    alert("Erreur lors du lancement de l'IA");
                    console.error(err);
                });
        }

        function stopAllIA() {
            fetch("/stop-ia/yolov5", {
                method: "GET"
            }).catch(() => {});
            fetch("/stop-ia/yolobestpt", {
                method: "GET"
            }).catch(() => {});
        }
    </script>

@endsection