@extends('layouts.app')

@section('title', 'Supervision Mission')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <style>
        #map {
            height: 400px;
            /* Taille fixe pour laisser de la place au reste sur mobile */
            width: 100%;
            border-radius: 12px;
            margin-top: 15px;
            z-index: 1;
            /* S'assurer qu'il est sous le header */
        }

        .controls {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('content')
    <a href="{{ route('dashboard') }}" class="btn btn-outline"
        style="margin-bottom: 15px; width: auto; display: inline-block;">&#8592; Tableau de bord</a>

    <!-- Flux Vid&eacute;o & IA -->
    @include('components.video-stream')



    <!-- Contrôles -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h3 style="margin:0">Contr&ocirc;le Mission</h3>
            <div>
                <select onchange="window.location.href='?mission_id=' + this.value"
                    style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.9rem;">
                    <option value="">-- Charger une mission --</option>
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
                <button onclick="updateStatus('paused')" class="btn btn-outline"
                    style="border-color: #f57c00; color: #f57c00;">&#10074;&#10074; Pause</button>
                <button onclick="updateStatus('completed')" class="btn btn-outline"
                    style="border-color: #d32f2f; color: #d32f2f;">&#9724; Terminer</button>
            @else
                <button onclick="updateStatus('ongoing')" class="btn btn-primary">&#9654; D&eacute;marrer / Reprendre</button>
            @endif
        </div>
        <p><strong>Mission en cours:</strong> {{ optional($mission)->nom ?? 'Aucune mission active' }}</p>
    </div>

    <!-- Carte Interactive -->
    <div id="map"></div>
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
            attribution: '© OpenStreetMap'
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

        // Gestion de la création de zone
        map.on(L.Draw.Event.CREATED, function (e) {
            var type = e.layerType,
                layer = e.layer;

            if (type === 'polygon' || type === 'rectangle') {
                // Supprimer l'ancienne zone s'il y en a une (on en veut une seule pour l'instant)
                editableLayers.clearLayers();
                editableLayers.addLayer(layer);
                workingZone = layer;
                console.log("Nouvelle zone de travail définie");
                // Rafraichir les couleurs des points existants
                // On peut relancer loadGPSPath pour recolorier
                // loadGPSPath(); // Pas idéal car fetch async, on verra
            }
        });

        map.on(L.Draw.Event.DELETED, function (e) {
            workingZone = null;
            console.log("Zone de travail supprimée");
        });

        // Algorithme Ray-Casting
        function isPointInPolygon(latlng, polygonLayer) {
            if (!polygonLayer) return false;
            
            // Leaflet Draw polygon can be complex. Typically getLatLngs()[0] is the outer ring.
            var poly = polygonLayer.getLatLngs();
            if(Array.isArray(poly) && poly.length > 0 && Array.isArray(poly[0])) {
                poly = poly[0]; // Outer ring
            } else {
                // Fallback direct array (Rectangle/Simple Polygon)
            }

            // Safety check
            if (!Array.isArray(poly)) return false;

            var x = latlng.lat, y = latlng.lng;
            var inside = false;
            
            for (var i = 0, j = poly.length - 1; i < poly.length; j = i++) {
                var xi = poly[i].lat, yi = poly[i].lng;
                var xj = poly[j].lat, yj = poly[j].lng;

                var intersect = ((yi > y) != (yj > y)) &&
                    (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                if (intersect) inside = !inside;
            }

            return inside;
        }

        // Fonction pour charger/rafraîchir les points GPS
        function loadGPSPath() {
            var missionId = "{{ optional($mission)->id }}";
            if (!missionId) return;

            fetch('/api/mission/' + missionId + '/path')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.points.length > 0) {
                        var points = data.points;

                        // On redessine tout à chaque fois pour simplifier la coloration dynamique
                        // Optimisation: faire un diff si les perfs chutent
                        if (true) { 
                            pathGroup.clearLayers();
                            if (pathLine) {
                                map.removeLayer(pathLine);
                                pathLine = null;
                            }

                            var previousLatLng = null;

                            points.forEach(function (point) {
                                var latLng = L.latLng(parseFloat(point.latitude), parseFloat(point.longitude));
                                
                                // Determiner la couleur (Vert = Dedans, Rouge = Dehors, Bleu = Pas de zone)
                                var color = '#2196F3';
                                if (workingZone) {
                                    if (isPointInPolygon(latLng, workingZone)) {
                                        color = '#00ff00'; // Green
                                    } else {
                                        color = '#ff0000'; // Red
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

        // Charger les points au démarrage
        loadGPSPath();

        // Rafraîchir automatiquement toutes les 3 secondes
        setInterval(loadGPSPath, 3000);

        // Connexion WebSocket (Simulation si echec)
        // Connexion WebSocket
        // REMPLACEZ PAR L'IP DE VOTRE ROBOT SI DIFFERENT
        // var wsUrl = "ws://192.168.1.50:8765"; 

        // Pour le dev local, on tente de se connecter sur l'ote actuel, ou sinon mettre l'IP manuellement
        var wsUrl = "ws://" + location.hostname + ":8765";
        var ws = new WebSocket(wsUrl);

        ws.onopen = function () {
            console.log("Connect\u00e9 au Rover via WS");
        };

        ws.onmessage = function (event) {
            try {
                var data = JSON.parse(event.data);

                // Telemetry: { lat, lon, speed, battery, tank, fix }
                if (data.lat && data.lon) {
                    var newLn = new L.LatLng(data.lat, data.lon);
                    roverMarker.setLatLng(newLn);
                    map.panTo(newLn); // Suivre le robot



                    // Trace (optionnel)
                    L.circleMarker(newLn, { radius: 2, color: 'blue' }).addTo(pathGroup);
                }
            } catch (e) {
                console.error("Erreur parsing WS", e);
            }
        };

        ws.onerror = function () {
            console.warn("WS non disponible. Mode d\u00e9mo ?");
        };

        // Fonction API pour changer le statut
        function updateStatus(newStatus) {
            var missionId = "{{ optional($mission)->id }}";
            if (!missionId) {
                alert("Aucune mission charg\u00e9e !");
                return;
            }

            fetch('/api/mission/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
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

        // Fonction pour charger les détections
        function loadDetections() {
            var missionId = "{{ optional($mission)->id }}";
            if (!missionId) return;

            // Note: On pourrait créer un endpoint /api/mission/{id}/detections
            // Pour l'instant on utilise un fallback ou on fetch tout
            fetch('/api/history') // Ou un nouvel endpoint si existant
                .then(response => response.json())
                .catch(err => console.log("En attente d'un endpoint de liste des détections"));
        }

        // Rafraîchir les détections
        // loadDetections();
        // setInterval(loadDetections, 5000);

    </script>
@endsection