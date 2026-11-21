<?php
// TELA ÚNICA DA ADMINISTRAÇÃO (substitui as 6 telas antigas: painel com botões, painel de encomendas,
// inserir encomenda, pesquisar, inserir produto).
// Quem chega aqui é o ADM logado (o login.php manda o ADM direto para esta página).

require('so-adm.php'); // só ADM entra
require('buscar-encomendas.php'); // busca pedidos, histórico, clientes e produtos no banco

// Token de segurança: o JavaScript manda ele junto em toda ação, e o acoes.php confere
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

// Se chegou até aqui o banco respondeu, então está conectado (o selo verde do topo usa isso)
$conectado = ($con instanceof mysqli) && !$con->connect_errno;

// Nomes dos meses para o menu "Painel" da lateral
$nomesMeses = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
               '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];

// Dados de cada linha para o JavaScript preencher a janela "Editar encomenda"
$dadosJs = [];
foreach ($linhas as $linha) {
    $dadosJs[$linha['id_item']] = [
        'id_pedido' => (int) $linha['id_pedido'],
        'id_item' => (int) $linha['id_item'],
        'cliente' => $linha['cliente'],
        'produto' => $linha['produto'],
        'valor' => $linha['valor'],
        'quantidade' => (int) $linha['quantidade'],
        'data' => substr($linha['data_pedido'], 0, 10),
        'tema' => $linha['tema'],
        'prazo' => $linha['prazo'],
        'telefone' => $linha['telefone'],
        'email' => $linha['email'],
        'endereco' => $linha['endereco'],
        'observacoes' => $linha['observacoes'],
        'pago' => (int) $linha['pago'],
        'pronto' => (int) $linha['pronto'],
        'buscou' => (int) $linha['buscou'],
        'precisa_arte' => (int) $linha['precisa_arte'],
        'arte' => $linha['arte'],
        // O que o cliente escreveu na tela do produto (só aparece em pedidos feitos pelo site)
        'detalhes' => array_filter([
            'Tamanho e formato' => $linha['tamanho'],
            'Idade' => $linha['idade'],
            'Nome a ser escrito' => $linha['nome_escrito'],
            'Cor' => $linha['cor'],
            'Observações do cliente' => $linha['obs_item'],
        ], function ($valor) {
            return $valor != '';
        })
    ];
}

// Quantas linhas vazias mostrar para a tabela parecer uma planilha (como no desenho)
$linhasVazias = max(0, 10 - count($linhas));

// Ícone de funil que aparece em cada título de coluna da tabela
$funil = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h18l-7 8v7l-4 2v-9z"/></svg>';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel | Corisco's Paper</title>
    <link rel="stylesheet" href="Css/painel.css">
    <script src="Js/painel.js" defer></script>
</head>
<body>

<div class="layout">

    <!-- ============================================================
         BARRA LATERAL (logo, pesquisa e menus que se abrem)
         ============================================================ -->
    <aside class="lateral" id="lateral">

        <a class="lateral-logo" href="painel.php" aria-label="Painel da administração">
            <img src="../imagens/logo.png" alt="Corisco's Paper">
        </a>

        <!-- Pesquisa da lateral: por enquanto filtra a mesma tabela da pesquisa de cima -->
        <!-- DÚVIDA: não ficou claro se a pesquisa da lateral deveria procurar só clientes/produtos.
             ABAIXO, se precisar, trocar por uma busca própria da lateral. -->
        <label class="pesquisa-lateral">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10" cy="10" r="6"/><path d="M15 15l5 5"/></svg>
            <span class="so-leitor">Pesquisar</span>
            <input id="buscaLateral" type="search" placeholder="PESQUISA">
        </label>

        <!-- MÉTRICAS -->
        <details class="menu">
            <summary><svg class="seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 9l7 7 7-7"/></svg>Métricas</summary>
            <div class="menu-conteudo">
                <!-- DÚVIDA: não sei quais métricas a Júlia quer ver aqui (os 4 quadros do topo já mostram
                     pedidos, aguardando pagamento, prontos e faturamento).
                     ABAIXO, preencher com as métricas extras (ex.: produto mais vendido, faturamento por mês...). -->
                <p class="em-breve">Em breve.</p>
            </div>
        </details>

        <!-- PAINEL: um item para cada mês que tem pedido -->
        <details class="menu" open>
            <summary><svg class="seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 9l7 7 7-7"/></svg>Painel</summary>
            <div class="menu-conteudo">
                <button type="button" class="item-menu ativo" data-mes="">Todos os meses</button>
                <?php foreach ($meses as $mes => $quantidade) { ?>
                    <button type="button" class="item-menu" data-mes="<?php echo escapar($mes); ?>">
                        <?php echo $nomesMeses[substr($mes, 5, 2)] . ' ' . substr($mes, 0, 4); ?>
                        <small><?php echo $quantidade; ?></small>
                    </button>
                <?php } ?>
            </div>
        </details>

        <!-- CLIENTES: clicando num nome, a tabela mostra só os pedidos dele -->
        <details class="menu">
            <summary><svg class="seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 9l7 7 7-7"/></svg>Clientes</summary>
            <div class="menu-conteudo">
                <?php if (count($clientes) == 0) { ?>
                    <p class="em-breve">Nenhum cliente ainda.</p>
                <?php } ?>
                <?php foreach ($clientes as $cliente) { ?>
                    <button type="button" class="item-menu" data-cliente="<?php echo escapar($cliente['cliente']); ?>">
                        <?php echo escapar($cliente['cliente']); ?>
                        <small><?php echo $cliente['total']; ?></small>
                    </button>
                <?php } ?>
                <!-- DÚVIDA: aqui só aparecem clientes que já fizeram pedido. Contas criadas no site que
                     ainda não compraram (tabela login) têm só e-mail e telefone, sem nome.
                     ABAIXO, preencher se a Júlia quiser ver também essas contas. -->
            </div>
        </details>

        <!-- PRODUTOS: lista do que está à venda e botão para cadastrar um novo -->
        <details class="menu">
            <summary><svg class="seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 9l7 7 7-7"/></svg>Produtos</summary>
            <div class="menu-conteudo">
                <button type="button" class="item-menu item-novo" id="abrirProduto">+ Novo produto</button>
                <?php foreach ($produtos as $produto) { ?>
                    <p class="item-produto" title="<?php echo escapar($produto['topico']); ?>">
                        <?php echo escapar($produto['nome']); ?>
                        <small><?php echo dinheiro($produto['preco']); ?></small>
                    </p>
                <?php } ?>
                <!-- DÚVIDA: ainda não existe editar preço, trocar foto ou tirar um produto da loja.
                     ABAIXO, preencher com a função de editar/desativar produto (a coluna "ativo" já existe no banco). -->
            </div>
        </details>

        <!-- Conta de quem está logado -->
        <div class="conta-adm">
            <p class="conta-nome">
                <span class="conta-icone"></span>
                <span>ADM<small><?php echo escapar($_SESSION['usuario']['email']); ?></small></span>
            </p>
            <div class="conta-links">
                <a href="../index.php">Ver o site</a>
                <a href="../Cadastro/deslogar.php">Sair da conta</a>
            </div>
        </div>

    </aside>

    <!-- ============================================================
         CONTEÚDO (topo, quadros, filtros, tabela e histórico)
         ============================================================ -->
    <main class="conteudo">

        <header class="topo">
            <button type="button" class="botao-menu" id="botaoMenu" aria-label="Abrir menu" aria-controls="lateral" aria-expanded="false">&#9776;</button>

            <h1>Painel</h1>

            <p class="selo-banco <?php echo $conectado ? '' : 'caiu'; ?>" role="status">
                <span class="bolinha"></span>
                <?php echo $conectado ? 'Conectado ao Banco' : 'Sem conexão com o Banco'; ?>
            </p>

            <button type="button" class="botao-exportar" id="exportar">Exportar</button>
            <button type="button" class="botao-nova" id="abrirNova">+ Nova encomenda</button>
        </header>

        <!-- Quadros com os números (mudam junto com os filtros da tabela) -->
        <section class="quadros" aria-label="Resumo">
            <article class="quadro">
                <h2>Total de pedidos</h2>
                <p id="kpiPedidos">00</p>
            </article>
            <article class="quadro">
                <h2>Aguardando PGTO</h2>
                <p id="kpiAguardando">00</p>
            </article>
            <article class="quadro">
                <h2>Prontos</h2>
                <p id="kpiProntos">00</p>
            </article>
            <article class="quadro">
                <!-- Faturamento = soma só do que JÁ foi pago (pedidos marcados como "Pagou: Sim") -->
                <h2>Faturamento</h2>
                <p id="kpiFaturamento">R$0,00</p>
            </article>
        </section>

        <hr class="linha-azul">

        <!-- Pesquisa e filtros rápidos -->
        <section class="filtros">
            <label class="pesquisa-tabela">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10" cy="10" r="6"/><path d="M15 15l5 5"/></svg>
                <span class="so-leitor">Pesquisar cliente ou encomenda</span>
                <input id="buscaTabela" type="search" placeholder="PESQUISA">
            </label>

            <div class="chips" aria-label="Filtros rápidos">
                <button type="button" class="chip chip-limpar" id="limpar">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h18l-7 8v7l-4 2v-9z"/></svg>
                    Limpar filtros
                </button>
                <button type="button" class="chip" data-filtro="aguardando">Aguarda PGTO</button>
                <button type="button" class="chip" data-filtro="pronto">Prontos</button>
                <button type="button" class="chip" data-filtro="prazo">Prazo próximo</button>
                <button type="button" class="chip" data-filtro="arte">Enviar arte</button>
            </div>
        </section>

        <!-- Tabela (uma linha para cada item de pedido). Clique numa linha para ver e editar -->
        <div class="tabela-rolagem">
            <table id="tabelaEncomendas">
                <thead>
                    <tr>
                        <th><button type="button" class="filtro-coluna" data-col="0" aria-label="Filtrar cliente"><?php echo $funil; ?></button>Cliente</th>
                        <th><button type="button" class="filtro-coluna" data-col="1" aria-label="Filtrar produto"><?php echo $funil; ?></button>Produto</th>
                        <th><button type="button" class="filtro-coluna" data-col="2" aria-label="Filtrar valor"><?php echo $funil; ?></button>Valor</th>
                        <th><button type="button" class="filtro-coluna" data-col="3" aria-label="Filtrar quantidade"><?php echo $funil; ?></button>Qtd.</th>
                        <th><button type="button" class="filtro-coluna" data-col="4" aria-label="Filtrar total"><?php echo $funil; ?></button>Total</th>
                        <th><button type="button" class="filtro-coluna" data-col="5" aria-label="Filtrar data"><?php echo $funil; ?></button>Data</th>
                        <th><button type="button" class="filtro-coluna" data-col="6" aria-label="Filtrar tema"><?php echo $funil; ?></button>Tema</th>
                        <th><button type="button" class="filtro-coluna" data-col="7" aria-label="Filtrar pagou"><?php echo $funil; ?></button>Pagou</th>
                        <th><button type="button" class="filtro-coluna" data-col="8" aria-label="Filtrar buscou"><?php echo $funil; ?></button>Buscou</th>
                        <th><button type="button" class="filtro-coluna" data-col="9" aria-label="Filtrar prazo"><?php echo $funil; ?></button>Prazo</th>
                        <th><button type="button" class="filtro-coluna" data-col="10" aria-label="Filtrar observações"><?php echo $funil; ?></button>Obs.</th>
                        <!-- DÚVIDA: no desenho essa coluna se chama "login?" e o conteúdo era ilegível.
                             Por enquanto mostra quem anotou o pedido: ADM (a Júlia) ou Site (o próprio cliente).
                             ABAIXO, trocar se a ideia for mostrar se o cliente tem conta (login) ou não. -->
                        <th><button type="button" class="filtro-coluna" data-col="11" aria-label="Filtrar origem"><?php echo $funil; ?></button>Origem</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($linhas as $linha) { ?>
                    <tr data-item="<?php echo $linha['id_item']; ?>"
                        data-pedido="<?php echo $linha['id_pedido']; ?>"
                        data-mes="<?php echo escapar($linha['f_mes']); ?>"
                        data-cliente="<?php echo escapar($linha['cliente']); ?>"
                        data-pagou="<?php echo $linha['f_pagou']; ?>"
                        data-pronto="<?php echo $linha['f_pronto']; ?>"
                        data-prazo="<?php echo $linha['f_prazo']; ?>"
                        data-arte="<?php echo $linha['f_arte']; ?>"
                        data-total="<?php echo $linha['total']; ?>"
                        tabindex="0" title="Clique para ver e editar">

                        <td class="col-cliente">
                            <?php echo escapar($linha['cliente']); ?>
                            <?php if ($linha['arte'] != '') { ?>
                                <!-- Clips: o cliente (ou a Júlia) já enviou a arte. Clicar abre a imagem -->
                                <a class="clips" href="../<?php echo escapar($linha['arte']); ?>" target="_blank" rel="noopener" title="Ver a arte enviada" aria-label="Ver a arte enviada">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M4 18l5-5 4 4 3-3 4 4"/></svg>
                                </a>
                            <?php } ?>
                        </td>
                        <td><?php echo escapar($linha['produto']); ?></td>
                        <td><?php echo dinheiro($linha['valor']); ?></td>
                        <td><?php echo $linha['quantidade']; ?></td>
                        <td><?php echo dinheiro($linha['total']); ?></td>
                        <td><?php echo date('d/m/y', strtotime($linha['data_pedido'])); ?></td>
                        <!-- DÚVIDA: no desenho a célula do tema aparece colorida. Interpretei assim:
                             amarela = ainda falta enviar a arte; bege = a arte já chegou.
                             ABAIXO, mudar se as cores significarem outra coisa. -->
                        <td class="<?php echo $linha['f_arte'] == 'sim' ? 'tema-falta-arte' : ($linha['arte'] != '' ? 'tema-com-arte' : ''); ?>"><?php echo escapar($linha['tema']); ?></td>
                        <td class="<?php echo $linha['pago'] == 1 ? 'pago' : 'pendente'; ?>"><?php echo $linha['pago'] == 1 ? 'Sim' : 'Não'; ?></td>
                        <td><?php echo $linha['buscou'] == 1 ? 'Sim' : 'Não'; ?></td>
                        <td><?php echo $linha['prazo'] != '' ? date('d/m/y', strtotime($linha['prazo'])) : ''; ?></td>
                        <td><?php echo escapar($linha['observacoes']); ?></td>
                        <td><?php echo $linha['criado_por'] == 'adm' ? 'ADM' : 'Site'; ?></td>
                    </tr>
                    <?php } ?>

                    <!-- Linhas em branco, só para a tabela parecer uma planilha -->
                    <?php for ($i = 0; $i < $linhasVazias; $i++) { ?>
                    <tr class="linha-vazia" aria-hidden="true"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Histórico: as últimas coisas que aconteceram -->
        <section class="historico">
            <h2>Histórico</h2>
            <?php if (count($historico) == 0) { ?>
                <p class="historico-vazio">Nada por aqui ainda.</p>
            <?php } ?>
            <?php foreach ($historico as $item) { ?>
            <p>
                <span><?php echo escapar($item['descricao']); ?></span>
                <time><?php echo date('d/m/y', strtotime($item['data_hora'])); ?></time>
                <time><?php echo date('H:i', strtotime($item['data_hora'])); ?></time>
            </p>
            <?php } ?>
        </section>

    </main>
</div>

<!-- Caixinha que abre quando se clica no funil de uma coluna da tabela -->
<div class="pop-filtro" id="popFiltro" hidden>
    <label>
        Filtrar <strong id="popNome"></strong>
        <input id="popInput" type="text" placeholder="Digite para filtrar">
    </label>
    <button type="button" id="popLimpar">Limpar</button>
</div>

<!-- ============================================================
     JANELA: NOVA ENCOMENDA
     ============================================================ -->
<dialog id="janelaNova" class="janela">
    <form id="formNova" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
        <input type="hidden" name="acao" value="nova_encomenda">

        <h2>Nova encomenda</h2>

        <div class="campos">
            <label>Cliente<input name="cliente" type="text" placeholder="Nome do cliente" required></label>
            <label>Produto<input name="produto" type="text" placeholder="Produto" required></label>
            <label>Valor<input name="valor" type="number" min="0" step="0.01" placeholder="0,00" required></label>
            <label>Quantidade<input name="quantidade" type="number" min="1" value="1" required></label>
            <label>Data<input name="data" id="novaData" type="date" required></label>
            <label>Tema<input name="tema" type="text" placeholder="Tema da encomenda"></label>
            <label>Prazo<input name="prazo" type="date"></label>
            <label>Telefone<input name="telefone" type="tel" placeholder="(00) 00000-0000"></label>
            <label>Endereço<input name="endereco" type="text" placeholder="Só se for entrega"></label>
            <label>E-mail<input name="email" type="email" placeholder="email@exemplo.com"></label>
            <label class="largo">Observações<textarea name="observacoes" rows="2" placeholder="Detalhes da encomenda"></textarea></label>
            <label class="marcar"><input name="pagou" type="checkbox"> Já pagou</label>
            <label class="marcar"><input name="buscou" type="checkbox"> Já buscou</label>
            <label class="marcar largo"><input name="precisa_arte" type="checkbox"> Cliente ainda precisa enviar a arte</label>
            <label class="largo">Foto / arte da encomenda<input name="foto" type="file" accept="image/*"></label>
        </div>

        <div class="acoes">
            <button type="button" class="botao-cancelar" data-fechar>Cancelar</button>
            <button type="submit" class="botao-salvar">Salvar encomenda</button>
        </div>
    </form>
</dialog>

<!-- ============================================================
     JANELA: EDITAR ENCOMENDA (abre ao clicar numa linha da tabela)
     ============================================================ -->
<dialog id="janelaEditar" class="janela">
    <form id="formEditar" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
        <input type="hidden" name="acao" value="salvar_encomenda">
        <input type="hidden" name="id_pedido" id="editIdPedido">
        <input type="hidden" name="id_item" id="editIdItem">

        <h2>Encomenda</h2>

        <!-- O que o cliente escreveu na tela do produto (só aparece em pedidos do site) -->
        <div class="detalhes-cliente" id="editDetalhes" hidden></div>

        <div class="campos">
            <label>Cliente<input name="cliente" id="editCliente" type="text" required></label>
            <label>Produto<input name="produto" id="editProduto" type="text" required></label>
            <label>Valor<input name="valor" id="editValor" type="number" min="0" step="0.01" required></label>
            <label>Quantidade<input name="quantidade" id="editQuantidade" type="number" min="1" required></label>
            <label>Data<input name="data" id="editData" type="date" required></label>
            <label>Tema<input name="tema" id="editTema" type="text"></label>
            <label>Prazo<input name="prazo" id="editPrazo" type="date"></label>
            <label>Telefone<input name="telefone" id="editTelefone" type="tel"></label>
            <label>Endereço<input name="endereco" id="editEndereco" type="text" placeholder="Só se for entrega"></label>
            <label>E-mail<input name="email" id="editEmail" type="email"></label>
            <label class="largo">Observações<textarea name="observacoes" id="editObservacoes" rows="2"></textarea></label>
            <label class="marcar"><input name="pagou" id="editPagou" type="checkbox"> Já pagou</label>
            <label class="marcar"><input name="pronto" id="editPronto" type="checkbox"> Pronto</label>
            <label class="marcar"><input name="buscou" id="editBuscou" type="checkbox"> Já buscou</label>
            <label class="marcar largo"><input name="precisa_arte" id="editPrecisaArte" type="checkbox"> Cliente ainda precisa enviar a arte</label>
            <label class="largo">
                Foto / arte da encomenda <a id="editArteAtual" href="#" target="_blank" rel="noopener" hidden>(ver a atual)</a>
                <input name="foto" type="file" accept="image/*">
            </label>
        </div>

        <div class="acoes">
            <button type="button" class="botao-excluir" id="excluirEncomenda">Excluir encomenda</button>
            <button type="button" class="botao-cancelar" data-fechar>Cancelar</button>
            <button type="submit" class="botao-salvar">Salvar alterações</button>
        </div>
    </form>
</dialog>

<!-- ============================================================
     JANELA: NOVO PRODUTO
     ============================================================ -->
<dialog id="janelaProduto" class="janela">
    <form id="formProduto" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
        <input type="hidden" name="acao" value="novo_produto">

        <h2>Novo produto</h2>

        <div class="campos">
            <label>Produto<input name="produto" type="text" placeholder="Nome do produto" required></label>
            <label>Tema<input name="tema" type="text" placeholder="Tema"></label>
            <label>Valor<input name="valor" type="number" min="0" step="0.01" placeholder="0,00" required></label>
            <label>Tipo
                <select name="tipo">
                    <option value="encomenda">Encomenda</option>
                    <option value="pronta_entrega">Pronta entrega</option>
                </select>
            </label>
            <label>Tópico (categoria)
                <input name="topico" type="text" list="listaTopicos" placeholder="Ex.: Topper, Brindes...">
                <datalist id="listaTopicos">
                    <?php foreach ($topicos as $topico) { ?>
                        <option value="<?php echo escapar($topico); ?>">
                    <?php } ?>
                </datalist>
            </label>
            <label>Cores disponíveis<input name="cores" type="text" placeholder="Ex.: azul, rosa, amarelo"></label>
            <label class="largo">Especificações<textarea name="especificacoes" rows="3" placeholder="Detalhes do produto"></textarea></label>
            <label class="largo">Foto do produto<input name="imagem" type="file" accept="image/*"></label>
        </div>

        <div class="acoes">
            <button type="button" class="botao-cancelar" data-fechar>Cancelar</button>
            <button type="submit" class="botao-salvar">Salvar produto</button>
        </div>
    </form>
</dialog>

<!-- Dados das linhas para o JavaScript (usados na janela "Editar encomenda") -->
<script type="application/json" id="dadosPedidos"><?php echo json_encode($dadosJs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?></script>

</body>
</html>
