document.getElementById('btn-medias').addEventListener('click', function() {
    const boxMedias = document.getElementById('box-medias');
    const btnLoadMedia = document.getElementById('btn-medias');
    
    if (boxMedias.classList.contains('d-flex')) {
        boxMedias.classList.remove('d-flex');
        boxMedias.classList.add('d-none');
        btnLoadMedia.innerText = "Afficher médias";
    } else {
        boxMedias.classList.remove('d-none');
        boxMedias.classList.add('d-flex');
        btnLoadMedia.innerText = "Masquer médias";
    }
});






