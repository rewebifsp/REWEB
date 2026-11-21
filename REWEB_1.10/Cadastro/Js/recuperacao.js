const formSolicitarCodigo = document.getElementById('formSolicitarCodigo');
const formVerificarCodigo = document.getElementById('formVerificarCodigo');
const formRedefinirSenha = document.getElementById('formRedefinirSenha');

if (formSolicitarCodigo) {
    const contato = document.getElementById('contato');

    formSolicitarCodigo.addEventListener('submit', function (evento) {
        evento.preventDefault();

        const valor = contato.value.trim();
        const digitos = valor.replace(/\D/g, '');
        const telefoneValido =
            /^[\d\s()+-]+$/.test(valor) && digitos.length === 11;
        const emailValido =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor);

        if (!telefoneValido && !emailValido) {
            alert('Digite um e-mail válido ou um telefone com DDD e 11 dígitos.');
            contato.focus();
            return;
        }

        window.location.href = 'codigo-senha.html';
    });
}

if (formVerificarCodigo) {
    formVerificarCodigo.addEventListener('submit', function (evento) {
        evento.preventDefault();
        window.location.href = 'redefinir-senha.html';
    });
}

if (formRedefinirSenha) {
    const novaSenha = document.getElementById('novaSenha');
    const confirmarSenha = document.getElementById('confirmarSenha');
    const erroSenha = document.getElementById('erroSenha');
    const mensagemSucesso = document.getElementById('mensagemSucesso');

    formRedefinirSenha.addEventListener('submit', function (evento) {
        evento.preventDefault();

        if (novaSenha.value !== confirmarSenha.value) {
            erroSenha.hidden = false;
            confirmarSenha.focus();
            return;
        }

        erroSenha.hidden = true;
        formRedefinirSenha.hidden = true;
        mensagemSucesso.hidden = false;
    });

    confirmarSenha.addEventListener('input', function () {
        erroSenha.hidden = true;
    });
}
