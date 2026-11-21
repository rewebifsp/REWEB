// O campo de endereço só aparece quando a pessoa escolhe "entrega"
const opcoes = document.querySelectorAll('input[name="forma_entrega"]');
const campoEndereco = document.getElementById('campoEndereco');

function mostrarEndereco() {
    const escolhida = document.querySelector('input[name="forma_entrega"]:checked').value;

    if (escolhida == 'entrega') {
        campoEndereco.style.display = 'block';
    } else {
        campoEndereco.style.display = 'none';
    }
}

opcoes.forEach(function (opcao) {
    opcao.addEventListener('change', mostrarEndereco);
});

mostrarEndereco();
