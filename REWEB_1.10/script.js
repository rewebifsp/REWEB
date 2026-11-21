
// Carrossel
const carrossel = document.getElementById('carrossel');
const voltar = document.getElementById('voltar');
const avancar = document.getElementById('avancar');
const indicadores = document.querySelector('.indicadores');

let posicao = 0;
let animando = false;

// Quantidade de bolinhas (uma para cada produto)
const totalBolinhas = indicadores.children.length;


// Atualiza as bolinhas
function atualizarBolinhas() {

    for (let i = 0; i < indicadores.children.length; i++) {

        indicadores.children[i].classList.toggle(
            'ativo',
            i === posicao
        );

    }

}


// Calcula o tamanho do movimento
function calcularPasso() {

    const produto = carrossel.querySelector('.produto-card');
    const espaco = 12;

    return produto.offsetWidth + espaco;

}


// Avançar
function avancarProduto() {

    if (animando) return;

    animando = true;

    const produtos = carrossel.querySelectorAll('.produto-card');
    const passo = calcularPasso();

    produtos.forEach(function (produto) {
        produto.classList.add('animando');
        produto.style.transform = `translateX(-${passo}px)`;
    });

    produtos[0].addEventListener('transitionend', function () {

        carrossel.appendChild(carrossel.firstElementChild);

        produtos.forEach(function (produto) {
            produto.style.transform = 'translateX(0)';
            produto.classList.remove('animando');
        });

        posicao = (posicao + 1) % totalBolinhas;

        atualizarBolinhas();

        animando = false;

    }, { once: true });

}


// Voltar
function voltarProduto() {

    if (animando) return;

    animando = true;

    const ultimo = carrossel.lastElementChild;
    const passo = calcularPasso();

    carrossel.prepend(ultimo);

    const produtos = carrossel.querySelectorAll('.produto-card');

    produtos.forEach(function (produto) {
        produto.style.transition = 'none';
        produto.style.transform = `translateX(-${passo}px)`;
    });

    carrossel.offsetHeight;

    produtos.forEach(function (produto) {
        produto.style.transition = 'transform 0.35s ease';
        produto.style.transform = 'translateX(0)';
    });

    produtos[0].addEventListener('transitionend', function () {

        produtos.forEach(function (produto) {
            produto.style.transition = '';
            produto.style.transform = '';
        });

        posicao = (posicao + totalBolinhas - 1) % totalBolinhas;

        atualizarBolinhas();

        animando = false;

    }, { once: true });

}


// Cliques nas setas
avancar.addEventListener('click', avancarProduto);
voltar.addEventListener('click', voltarProduto);


// Inicia as bolinhas
atualizarBolinhas();



