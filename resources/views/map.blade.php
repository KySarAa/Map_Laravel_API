<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Carte RTK</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body>
<div id="map" style="height: 100vh;"></div>

<script>
    // Carte
    var map = L.map('map').setView([48.8566, 2.3522], 17);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    // Marqueur RTK
    var marker = L.marker([48.8566, 2.3522]).addTo(map);

    // WebSocket vers le Raspberry Pi
    var ws = new WebSocket("ws://192.168.1.50:8765");

    ws.onmessage = function(event) {
        var data = JSON.parse(event.data);

        // data = { lat, lon, fix }
        marker.setLatLng([data.lat, data.lon]);
        map.setView([data.lat, data.lon]);

        // Optionnel : afficher la qualité RTK
        if (data.fix === "RTK_FIXED") {
            marker.bindPopup("RTK FIXED (cm)").openPopup();
        }
    };
</script>
</body>
</html>

