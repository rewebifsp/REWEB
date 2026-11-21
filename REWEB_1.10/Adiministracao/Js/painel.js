// Tela única da administração (painel.php): filtros, números do topo, exportar e as janelas.

const tabela = document.getElementById('tabelaEncomendas');
const buscaTabela = document.getElementById('buscaTabela');
const buscaLateral = document.getElementById('buscaLateral');

// Dados de cada linha (vêm do PHP), usados para preencher a janela "Editar encomenda"
const dadosPedidos = JSON.parse(document.getElementById('dadosPedidos').textContent);

// Tudo o que está filtrando a tabela neste momento
const filtros = {
    busca: '',        // texto digitado na pesquisa
    mes: '',          // mês escolhido na lateral (exemplo: 2026-10). Vazio = todos
    cliente: '',      // cliente escolhido na lateral. Vazio = todos
    rapido: '',       // botão rápido: aguardando, pronto, prazo ou arte
    colunas: {}       // filtro de cada coluna (funil): { 0: 'jak', 7: 'não' }
};

// Deixa o texto minúsculo e sem acento, assim "Prazo" acha "prazo" e "nao" acha "não"
function limpar(texto) {
    return texto.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();
}

// ------------------------------------------------------------
// Aplica todos os filtros e atualiza os quadros do topo
// ------------------------------------------------------------
function filtrar() {
    let pedidosVistos = new Set();
    let aguardando = new Set();
    let prontos = new Set();
    let faturamento = 0;

    tabela.querySelectorAll('tbody tr[data-item]').forEach((linha) => {
        const d = linha.dataset;

        let aparece = limpar(linha.textContent).includes(limpar(filtros.busca));

        if (aparece && filtros.mes !== '') {
            aparece = d.mes === filtros.mes;
        }
        if (aparece && filtros.cliente !== '') {
            aparece = d.cliente === filtros.cliente;
        }
        if (aparece && filtros.rapido !== '') {
            aparece =
                (filtros.rapido === 'aguardando' && d.pagou === 'nao') ||
                (filtros.rapido === 'pronto' && d.pronto === 'sim') ||
                (filtros.rapido === 'prazo' && d.prazo === 'sim') ||
                (filtros.rapido === 'arte' && d.arte === 'sim');
        }
        if (aparece) {
            for (const coluna in filtros.colunas) {
                if (!limpar(linha.cells[coluna].textContent).includes(limpar(filtros.colunas[coluna]))) {
                    aparece = false;
                    break;
                }
            }
        }

        linha.hidden = !aparece;

        // Os quadros do topo contam só o que está aparecendo
        if (aparece) {
            pedidosVistos.add(d.pedido);
            if (d.pagou === 'nao') aguardando.add(d.pedido);
            if (d.pronto === 'sim') prontos.add(d.pedido);
            if (d.pagou === 'sim') faturamento += parseFloat(d.total);
        }
    });

    // Número com dois dígitos, como no desenho (02, 06, 84)
    const doisDigitos = (n) => String(n).padStart(2, '0');
    document.getElementById('kpiPedidos').textContent = doisDigitos(pedidosVistos.size);
    document.getElementById('kpiAguardando').textContent = doisDigitos(aguardando.size);
    document.getElementById('kpiProntos').textContent = doisDigitos(prontos.size);
    document.getElementById('kpiFaturamento').textContent =
        'R$' + faturamento.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ------------------------------------------------------------
// Pesquisas (a de cima e a da lateral mexem na mesma coisa)
// ------------------------------------------------------------
function pesquisar(texto) {
    filtros.busca = texto;
    buscaTabela.value = texto;
    buscaLateral.value = texto;
    filtrar();
}

buscaTabela.addEventListener('input', () => pesquisar(buscaTabela.value));
buscaLateral.addEventListener('input', () => pesquisar(buscaLateral.value));

// ------------------------------------------------------------
// Botões rápidos (Aguarda PGTO, Prontos, Prazo próximo, Enviar arte)
// ------------------------------------------------------------
document.querySelectorAll('[data-filtro]').forEach((botao) => {
    botao.addEventListener('click', () => {
        // Clicar de novo no botão que já está ligado desliga ele
        filtros.rapido = filtros.rapido === botao.dataset.filtro ? '' : botao.dataset.filtro;
        document.querySelectorAll('[data-filtro]').forEach((b) => {
            b.classList.toggle('ativo', b.dataset.filtro === filtros.rapido);
        });
        filtrar();
    });
});

// ------------------------------------------------------------
// Lateral: meses e clientes
// ------------------------------------------------------------
function marcarItemLateral() {
    document.querySelectorAll('.item-menu[data-mes]').forEach((b) => {
        b.classList.toggle('ativo', b.dataset.mes === filtros.mes);
    });
    document.querySelectorAll('.item-menu[data-cliente]').forEach((b) => {
        b.classList.toggle('ativo', b.dataset.cliente === filtros.cliente && filtros.cliente !== '');
    });
}

document.querySelectorAll('.item-menu[data-mes]').forEach((botao) => {
    botao.addEventListener('click', () => {
        filtros.mes = botao.dataset.mes;
        marcarItemLateral();
        filtrar();
    });
});

document.querySelectorAll('.item-menu[data-cliente]').forEach((botao) => {
    botao.addEventListener('click', () => {
        // Clicar de novo no mesmo cliente volta a mostrar todos
        filtros.cliente = filtros.cliente === botao.dataset.cliente ? '' : botao.dataset.cliente;
        marcarItemLateral();
        filtrar();
    });
});

// ------------------------------------------------------------
// Funil de cada coluna: abre uma caixinha para digitar o filtro
// ------------------------------------------------------------
const popFiltro = document.getElementById('popFiltro');
const popInput = document.getElementById('popInput');
const popNome = document.getElementById('popNome');
let colunaAberta = null;

function fecharPop() {
    popFiltro.hidden = true;
    colunaAberta = null;
}

document.querySelectorAll('.filtro-coluna').forEach((botao) => {
    botao.addEventListener('click', (evento) => {
        evento.stopPropagation();
        const coluna = botao.dataset.col;

        // Clicar no mesmo funil de novo fecha a caixinha
        if (colunaAberta === coluna && !popFiltro.hidden) {
            fecharPop();
            return;
        }

        colunaAberta = coluna;
        popNome.textContent = botao.parentElement.textContent.trim().toLowerCase();
        popInput.value = filtros.colunas[coluna] || '';

        // Põe a caixinha embaixo do funil clicado
        const caixa = botao.getBoundingClientRect();
        popFiltro.style.top = (window.scrollY + caixa.bottom + 6) + 'px';
        popFiltro.style.left = Math.min(window.scrollX + caixa.left, window.innerWidth - 240) + 'px';
        popFiltro.hidden = false;
        popInput.focus();
    });
});

popInput.addEventListener('input', () => {
    if (popInput.value.trim() === '') {
        delete filtros.colunas[colunaAberta];
    } else {
        filtros.colunas[colunaAberta] = popInput.value;
    }
    marcarFunis();
    filtrar();
});

document.getElementById('popLimpar').addEventListener('click', () => {
    delete filtros.colunas[colunaAberta];
    popInput.value = '';
    marcarFunis();
    filtrar();
    fecharPop();
});

// Funil fica vermelho quando a coluna está filtrada
function marcarFunis() {
    document.querySelectorAll('.filtro-coluna').forEach((b) => {
        b.classList.toggle('ativo', filtros.colunas[b.dataset.col] !== undefined);
    });
}

// Clicar fora ou apertar Esc fecha a caixinha
document.addEventListener('click', (evento) => {
    if (!popFiltro.hidden && !popFiltro.contains(evento.target)) fecharPop();
});
document.addEventListener('keydown', (evento) => {
    if (evento.key === 'Escape') fecharPop();
});

// ------------------------------------------------------------
// Limpar filtros: volta tudo ao começo
// ------------------------------------------------------------
document.getElementById('limpar').addEventListener('click', () => {
    filtros.busca = '';
    filtros.mes = '';
    filtros.cliente = '';
    filtros.rapido = '';
    filtros.colunas = {};
    buscaTabela.value = '';
    buscaLateral.value = '';
    document.querySelectorAll('[data-filtro]').forEach((b) => b.classList.remove('ativo'));
    marcarItemLateral();
    marcarFunis();
    fecharPop();
    filtrar();
});

// ------------------------------------------------------------
// Exportar: baixa as linhas que estão aparecendo em um arquivo CSV (abre no Excel)
// ------------------------------------------------------------
document.getElementById('exportar').addEventListener('click', () => {
    const linhas = [...tabela.querySelectorAll('tr')].filter((l) => !l.hidden && !l.classList.contains('linha-vazia'));
    const csv = linhas.map((linha) => [...linha.cells]
        .map((celula) => '"' + celula.innerText.trim().replaceAll('"', '""') + '"').join(';')).join('\n');

    const arquivo = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8' });
    const url = URL.createObjectURL(arquivo);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'encomendas.csv';
    link.click();
    URL.revokeObjectURL(url);
});

// ------------------------------------------------------------
// Menu da lateral em telas pequenas
// ------------------------------------------------------------
const lateral = document.getElementById('lateral');
const botaoMenu = document.getElementById('botaoMenu');

botaoMenu.addEventListener('click', () => {
    const aberta = lateral.classList.toggle('aberta');
    botaoMenu.setAttribute('aria-expanded', aberta);
});

// ------------------------------------------------------------
// Janelas (nova encomenda, editar encomenda, novo produto)
// ------------------------------------------------------------
const janelaNova = document.getElementById('janelaNova');
const janelaEditar = document.getElementById('janelaEditar');
const janelaProduto = document.getElementById('janelaProduto');

// Botões "Cancelar" fecham a janela em que estão
document.querySelectorAll('[data-fechar]').forEach((botao) => {
    botao.addEventListener('click', () => botao.closest('dialog').close());
});

// Clicar no fundo escuro (fora da janela) também fecha
document.querySelectorAll('dialog').forEach((janela) => {
    janela.addEventListener('click', (evento) => {
        if (evento.target === janela) janela.close();
    });
});

// Data de hoje no formato do campo de data (aaaa-mm-dd), usando o horário do computador
function hoje() {
    const agora = new Date();
    agora.setMinutes(agora.getMinutes() - agora.getTimezoneOffset());
    return agora.toISOString().slice(0, 10);
}

document.getElementById('abrirNova').addEventListener('click', () => {
    document.getElementById('formNova').reset();
    document.getElementById('novaData').value = hoje();
    janelaNova.showModal();
});

document.getElementById('abrirProduto').addEventListener('click', () => {
    document.getElementById('formProduto').reset();
    janelaProduto.showModal();
});

// Clicar numa linha da tabela abre a janela de edição daquela encomenda
function abrirEdicao(linha) {
    const d = dadosPedidos[linha.dataset.item];
    if (!d) return;

    document.getElementById('editIdPedido').value = d.id_pedido;
    document.getElementById('editIdItem').value = d.id_item;
    document.getElementById('editCliente').value = d.cliente || '';
    document.getElementById('editProduto').value = d.produto || '';
    document.getElementById('editValor').value = d.valor || 0;
    document.getElementById('editQuantidade').value = d.quantidade || 1;
    document.getElementById('editData').value = d.data || '';
    document.getElementById('editTema').value = d.tema || '';
    document.getElementById('editPrazo').value = d.prazo || '';
    document.getElementById('editTelefone').value = d.telefone || '';
    document.getElementById('editEndereco').value = d.endereco || '';
    document.getElementById('editEmail').value = d.email || '';
    document.getElementById('editObservacoes').value = d.observacoes || '';
    document.getElementById('editPagou').checked = d.pago === 1;
    document.getElementById('editPronto').checked = d.pronto === 1;
    document.getElementById('editBuscou').checked = d.buscou === 1;
    document.getElementById('editPrecisaArte').checked = d.precisa_arte === 1;
    janelaEditar.querySelector('input[type="file"]').value = '';

    // Link para ver a arte que já foi enviada
    const arte = document.getElementById('editArteAtual');
    arte.hidden = !d.arte;
    if (d.arte) arte.href = '../' + d.arte;

    // O que o cliente escreveu na tela do produto
    const detalhes = document.getElementById('editDetalhes');
    detalhes.textContent = '';
    Object.entries(d.detalhes || {}).forEach(([nome, valor]) => {
        const p = document.createElement('div');
        const negrito = document.createElement('strong');
        negrito.textContent = nome + ': ';
        p.append(negrito, document.createTextNode(valor));
        detalhes.append(p);
    });
    detalhes.hidden = detalhes.children.length === 0;

    janelaEditar.showModal();
}

tabela.querySelectorAll('tbody tr[data-item]').forEach((linha) => {
    linha.addEventListener('click', (evento) => {
        // Clicar no clips (ver a arte) abre a imagem, não a janela
        if (evento.target.closest('a')) return;
        abrirEdicao(linha);
    });
    linha.addEventListener('keydown', (evento) => {
        if (evento.key === 'Enter') abrirEdicao(linha);
    });
});

// Manda os dados de um formulário para o acoes.php e recarrega a página se deu certo
async function enviar(dados, botao) {
    if (botao) botao.disabled = true;
    try {
        const resposta = await fetch('acoes.php', { method: 'POST', body: dados });
        const resultado = await resposta.json();
        if (resultado.ok) {
            window.location.reload();
            return;
        }
        alert(resultado.erro || 'Não foi possível concluir.');
    } catch (erro) {
        alert('Erro de conexão com o servidor. Tente de novo.');
    }
    if (botao) botao.disabled = false;
}

['formNova', 'formEditar', 'formProduto'].forEach((id) => {
    const formulario = document.getElementById(id);
    formulario.addEventListener('submit', (evento) => {
        evento.preventDefault();
        enviar(new FormData(formulario), formulario.querySelector('.botao-salvar'));
    });
});

// Excluir a encomenda (o pedido inteiro) depois de confirmar
document.getElementById('excluirEncomenda').addEventListener('click', () => {
    const nome = document.getElementById('editCliente').value;
    if (!confirm('Excluir a encomenda de ' + nome + '? Todos os itens dela serão apagados.')) return;

    const dados = new FormData();
    dados.append('csrf', document.querySelector('#formEditar [name="csrf"]').value);
    dados.append('acao', 'excluir_encomenda');
    dados.append('id_pedido', document.getElementById('editIdPedido').value);
    enviar(dados, document.getElementById('excluirEncomenda'));
});

// Começa mostrando os números de tudo
filtrar();
