const formEncomenda = document.getElementById('formEncomenda');

formEncomenda?.addEventListener('submit', (evento) => {
    evento.preventDefault();
    alert('Demonstração: a encomenda ainda não foi salva no banco.');
});
