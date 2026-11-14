<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar | Corisco's Paper</title>
    <link rel="stylesheet" href="Css/admin-base.css">
    <link rel="stylesheet" href="Css/pesquisar.css">
    <script src="Js/pesquisar.js" defer></script>
</head>
<body>
    <header class="cabecalho-admin">
        <a href="painel.php" aria-label="Voltar ao painel"><img src="../imagens/logo.png" alt="Corisco's Paper"></a>
        <h1>PESQUISAR CLIENTE/ENCOMENDA</h1>
    </header>

    <main class="pagina-tabela">
        <section class="filtros">
            <label class="busca">
                <span class="somente-leitor">Pesquisar</span>
                <input id="campoBusca" type="search" placeholder="Pesquisar cliente ou encomenda">
            </label>
            
            <div class="botoes-filtro" aria-label="Filtros rápidos">

                <button type="button" data-filtro="aguardando">Aguardando Pagamento</button>
                <button type="button" data-filtro="pronto">Prontos</button>
                <button type="button" data-filtro="prazo">Prazo próximo</button>
                <button type="button" data-filtro="arte">Enviar arte</button>
                <button type="button" id="limparFiltros">Limpar filtros</button>

            </div>
        </section>

        <div class="tabela-rolagem">
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Produto</th>
                        <th>Valor</th>
                        <th>Qtd.</th>
                        <th>Total</th>
                        <th>Data</th>
                        <th>Tema</th>
                        <th>Pagou</th>
                        <th>Buscou</th>
                        <th>Prazo</th>
                        <th>Obs.</th>
                    </tr>
                </thead>

                <tbody id="linhasEncomendas">

                    <tr data-pagou="nao" data-pronto="nao" data-prazo="sim" data-arte="sim">
                        <td>Jak Motta</td>
                        <td>Topper</td>
                        <td>R$ 22,00</td>
                        <td>1</td>
                        <td>R$ 22,00</td>
                        <td>22/10/26</td>
                        <td>Bluey</td>
                        <td>Não</td>
                        <td>Não</td>
                        <td>10/12/26</td>
                        <td>Produzir</td>
                    </tr>
                    <tr data-pagou="sim" data-pronto="sim" data-prazo="nao" data-arte="nao">
                        <td>Debby</td>
                        <td>Papel arroz</td>
                        <td>R$ 17,00</td>
                        <td>2</td>
                        <td>R$ 34,00</td>
                        <td>22/10/26</td>
                        <td>Os Roses</td>
                        <td>Sim</td>
                        <td>Não</td>
                        <td>10/12/26</td>
                        <td>Enviar arte</td>
                    </tr>
                    <tr data-pagou="nao" data-pronto="nao" data-prazo="sim" data-arte="nao">
                        <td>Juny</td>
                        <td>Bottom</td>
                        <td>R$ 8,00</td>
                        <td>2</td>
                        <td>R$ 16,00</td>
                        <td>22/10/26</td>
                        <td>Luna</td>
                        <td>Não</td>
                        <td>Sim</td>
                        <td>01/12/26</td>
                        <td>Pagamento pendente</td>
                    </tr >

                </tbody >
            </table >
        </div >
        <section class="historico">
            <h2>Histórico</h2>
            <p>
                <span>Juliana adicionou encomenda</span>
                <time>22/10/26 — 08:00</time>
            </p>
            <p>
                <span>Jak Motta solicitou encomenda</span>
                <time>22/10/26 — 13:00</time>
            </p>
        </section>

        <a class="botao-voltar" href="painel.php">VOLTAR À PÁGINA INICIAL</a>
        
    </main>
</body>
</html>
