const form = document.getElementById('formCadastro');
const senha = document.getElementById('senha');
const confirmar = document.getElementById('confirmar');




// Não deixa enviar se as duas senhas forem diferentes
form.addEventListener('submit', function (evento) {
    const quantidadeDeDigitos = telefone.value.replace(/\D/g, '').length;
    
    if (quantidadeDeDigitos !==11) {
    evento.preventDefault();
    alert('O número de telefone deve ter 11 dígitos!');
    telefone.focus();
    return;
    }

    if (senha.value !== confirmar.value) {
        evento.preventDefault();
        alert('As senhas não são iguais!');
        confirmar.focus();
        return;
    }


});
