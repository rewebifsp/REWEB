const campoBusca = document.getElementById('campoBusca');
const linhasPesquisa = [...document.querySelectorAll('#linhasEncomendas tr')];
let filtroPesquisa = '';

function atualizarPesquisa() {
    const termo = campoBusca.value.trim().toLocaleLowerCase('pt-BR');
    linhasPesquisa.forEach((linha) => {
        const correspondeBusca = linha.textContent.toLocaleLowerCase('pt-BR').includes(termo);
        const correspondeFiltro = !filtroPesquisa ||
            (filtroPesquisa === 'aguardando' && linha.dataset.pagou === 'nao') ||
            (filtroPesquisa === 'pronto' && linha.dataset.pronto === 'sim') ||
            (filtroPesquisa === 'prazo' && linha.dataset.prazo === 'sim') ||
            (filtroPesquisa === 'arte' && linha.dataset.arte === 'sim');
        linha.hidden = !(correspondeBusca && correspondeFiltro);
    });
}

campoBusca?.addEventListener('input', atualizarPesquisa);
document.querySelectorAll('[data-filtro]').forEach((botao) => {
    botao.addEventListener('click', () => { filtroPesquisa = botao.dataset.filtro; atualizarPesquisa(); });
});
document.getElementById('limparFiltros')?.addEventListener('click', () => {
    campoBusca.value = '';
    filtroPesquisa = '';
    atualizarPesquisa();
});
