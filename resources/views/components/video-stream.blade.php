<div class="video-container position-relative bg-dark rounded overflow-hidden shadow-lg w-100" style="min-height: 400px; display: flex; align-items: center; justify-content: center; background-color: #000;">
    <!-- Utilisation de l'IP fournie par l'utilisateur -->
    <img id="video-stream" src="http://172.16.152.17:8080/video" alt="Flux Vid&eacute;o Robot" class="w-100" style="height: auto; min-height: 400px; object-fit: cover;"
        onerror="this.onerror=null; this.src='https://placehold.co/640x480?text=Signal+Perdu';">
    
    <div class="position-absolute top-0 start-0 m-2 bg-danger text-white px-2 py-1 rounded small placeholder-glow" style="z-index: 10;">
        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
        LIVE
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var img = document.getElementById('video-stream');
        
        // Fallback: si l'IP hardcodée ne marche pas (ex: accès depuis localhost), on tente le hostname
        img.onerror = function() {
            // Si l'image source était déjà le hostname, on arrête pour éviter une boucle infinie
            if (this.src.includes(window.location.hostname)) {
                 this.src='https://placehold.co/640x480?text=Signal+Perdu';
                 return;
            }
            
            console.log("Tentative de connexion via hostname...");
            this.src = "http://" + window.location.hostname + ":8080/video";
        };
    });
</script>