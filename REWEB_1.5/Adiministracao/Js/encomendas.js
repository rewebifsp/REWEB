const tabelaEncomendas = document.getElementById('tabelaEncomendas');
const buscaEncomendas = document.getElementById('buscaEncomendas');
let filtroEncomendas = '';

function filtrarEncomendas() {
    const termo = buscaEncomendas.value.trim().toLocaleLowerCase('pt-BR');
    tabelaEncomendas.querySelectorAll('tbody tr').forEach((linha) => {
        const correspondeBusca = linha.textContent.toLocaleLowerCase('pt-BR').includes(termo);
        const correspondeFiltro = !filtroEncomendas ||
            (filtroEncomendas === 'aguardando' && linha.dataset.pagou === 'nao') ||
            (filtroEncomendas === 'pronto' && linha.dataset.pronto === 'sim') ||
            (filtroEncomendas === 'prazo' && linha.dataset.prazo === 'sim') ||
            (filtroEncomendas === 'arte' && linha.dataset.arte === 'sim');
        linha.hidden = !(correspondeBusca && correspondeFiltro);
    });
}

buscaEncomendas?.addEventListener('input', filtrarEncomendas);
document.querySelectorAll('[data-filtro]').forEach((botao) => botao.addEventListener('click', () => {
    filtroEncomendas = botao.dataset.filtro;
    filtrarEncomendas();
}));
document.getElementById('limpar')?.addEventListener('click', () => {
    buscaEncomendas.value = '';
    filtroEncomendas = '';
    filtrarEncomendas();
});

document.querySelectorAll('.editar').forEach((botao) => botao.addEventListener('click', () => {
    const linha = botao.closest('tr');
    const celulas = linha.querySelectorAll('td[contenteditable]');
    if (botao.textContent === 'Editar') {
        celulas.forEach((celula) => { celula.contentEditable = 'true'; });
        botao.textContent = 'Salvar';
        celulas[0].focus();
    } else {
        celulas.forEach((celula) => { celula.contentEditable = 'false'; });
        botao.textContent = 'Editar';
        alert('Demonstração: alteração ainda não foi salva no banco.');
    }
}));

document.querySelectorAll('.excluir').forEach((botao) => botao.addEventListener('click', () => {
    if (confirm('Remover esta encomenda da demonstração?')) botao.closest('tr').remove();
}));

document.getElementById('exportar')?.addEventListener('click', () => {
    const linhas = [...tabelaEncomendas.querySelectorAll('tr')].filter((linha) => !linha.hidden);
    const csv = linhas.map((linha) => [...linha.cells].slice(0, -1)
        .map((celula) => '"' + celula.innerText.replaceAll('"', '""') + '"').join(';')).join('\n');
    const arquivo = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' });
    const url = URL.createObjectURL(arquivo);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'encomendas.csv';
    link.click();
    URL.revokeObjectURL(url);
});
