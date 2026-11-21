// Olhinho: mostra ou esconde a senha
const campoSenha = document.getElementById('senha');
const botaoOlho = document.getElementById('olho');

botaoOlho.addEventListener('click', function () {

    const mostrando = campoSenha.type === 'text';

    campoSenha.type = mostrando ? 'password' : 'text';

    botaoOlho.setAttribute('aria-pressed', !mostrando);
    botaoOlho.setAttribute('aria-label', mostrando ? 'Mostrar senha' : 'Esconder senha');

});
