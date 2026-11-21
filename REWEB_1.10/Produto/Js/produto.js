// Troca a foto grande quando clica numa foto pequena
const fotoGrande = document.getElementById('fotoGrande');
const miniaturas = document.querySelectorAll('.miniatura');

miniaturas.forEach(function (miniatura) {
    miniatura.addEventListener('click', function () {
        fotoGrande.src = miniatura.querySelector('img').src;
    });
});
