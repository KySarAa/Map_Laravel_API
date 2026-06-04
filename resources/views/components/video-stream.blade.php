<div class="video-container position-relative bg-dark rounded overflow-hidden shadow-lg w-100" style="min-height: 400px; display: flex; align-items: center; justify-content: center; background-color: #000;">
    <img id="video-stream" src="http://100.101.230.51:5000/video" alt="Flux Vidéo Robot" class="w-100" style="height: auto; min-height: 400px; object-fit: cover;"
        onerror="this.onerror=null; this.src='https://placehold.co/640x480?text=Signal+Perdu';">
    
    <div class="position-absolute top-0 start-0 m-2 bg-danger text-white px-2 py-1 rounded small placeholder-glow" style="z-index: 10;">
        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
        LIVE
    </div>
</div>