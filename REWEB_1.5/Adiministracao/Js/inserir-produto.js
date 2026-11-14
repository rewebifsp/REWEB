const formProduto = document.getElementById('formProduto');
const nomeProduto = document.getElementById('nomeProduto');
const valorProduto = document.getElementById('valorProduto');
const campoEspecificacoes = document.querySelector('[name="especificacoes"]');
const imagemProduto = document.getElementById('imagemProduto');

nomeProduto?.addEventListener('input', () => {
    document.getElementById('previaNome').textContent = nomeProduto.value.trim() || 'NOME DO PRODUTO';
});

valorProduto?.addEventListener('input', () => {
    const valor = Number(valorProduto.value) || 0;
    document.getElementById('previaValor').textContent = valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
});

campoEspecificacoes?.addEventListener('input', () => {
    document.getElementById('previaEspecificacoes').textContent = campoEspecificacoes.value.trim() || 'ESPECIFICAÇÕES';
});

imagemProduto?.addEventListener('change', () => {
    const arquivo = imagemProduto.files?.[0];
    if (arquivo) document.getElementById('previaImagem').src = URL.createObjectURL(arquivo);
});

formProduto?.addEventListener('submit', (evento) => {
    evento.preventDefault();
    alert('Demonstração: o produto ainda não foi salvo no banco.');
});
