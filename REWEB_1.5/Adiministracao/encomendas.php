<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de encomendas | Corisco's Paper</title>
    <link rel="stylesheet" href="Css/admin-base.css">
    <link rel="stylesheet" href="Css/encomendas.css">
    <script src="Js/encomendas.js" defer></script>
</head>
<body>
    <header class="cabecalho-admin">
        <a href="painel.php" aria-label="Voltar ao painel"><img src="../imagens/logo.png" alt="Corisco's Paper"></a>
        <h1>PAINEL DE ENCOMENDAS</h1>
    </header>

    <main class="pagina-encomendas">
    
        <div class="barra-acoes">
            <button id="exportar" type="button">Exportar CSV</button>
            <a href="adicionar-encomenda.php">+ Nova encomenda</a><a href="pesquisar.php">Pesquisar</a>
        </div>

        <section class="filtros-encomendas">

            <label>
                <span class="somente-leitor">Buscar encomenda</span>
                <input id="buscaEncomendas" type="search" placeholder="Pesquisar cliente ou encomenda">
            </label>
           
            <div class="filtros-rapidos">
                <button type="button" data-filtro="aguardando">Aguarda PGTO</button>
                <button type="button" data-filtro="pronto">Prontos</button>
                <button type="button" data-filtro="prazo">Prazo próximo</button>
                <button type="button" data-filtro="arte">Enviar arte</button>
                <button type="button" id="limpar">Limpar filtros</button>
            </div>

        </section>
        
        <div class="tabela-rolagem">
            <table id="tabelaEncomendas">
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
                        <th>Ações</th>
                    </tr>
                </thead>
            
                <tbody>
                    <tr data-pagou="nao" data-pronto="nao" data-prazo="sim" data-arte="sim">
                        <td contenteditable="false">Jak Motta</td>
                        <td contenteditable="false">Topper</td>
                        <td contenteditable="false">R$ 22,00</td>
                        <td contenteditable="false">1</td>
                        <td>R$ 22,00</td>
                        <td contenteditable="false">22/10/26</td>
                        <td>Bluey</td>
                        <td class="pendente">Não</td>
                        <td>Não</td>
                        <td>10/12/26</td>
                        <td>Produzir</td>
                        <td class="acoes-linha">
                            <button class="editar" type="button">Editar</button>
                            <button class="excluir" type="button">Excluir</button>
                        </td>
                    </tr>

                    <tr data-pagou="sim" data-pronto="sim" data-prazo="nao" data-arte="nao">
                        <td contenteditable="false">Debby</td>
                        <td contenteditable="false">Papel arroz</td>
                        <td contenteditable="false">R$ 17,00</td>
                        <td contenteditable="false">2</td>
                        <td>R$ 34,00</td>
                        <td contenteditable="false">22/10/26</td>
                        <td>Os Roses</td>
                        <td class="pago">Sim</td>
                        <td>Não</td>
                        <td>10/12/26</td>
                        <td>Enviar arte</td>
                        <td class="acoes-linha">
                            <button class="editar" type="button">Editar</button>
                            <button class="excluir" type="button">Excluir</button>
                        </td>
                    </tr>

                    <tr data-pagou="nao" data-pronto="nao" data-prazo="sim" data-arte="nao">
                        <td contenteditable="false">Juny</td>
                        <td contenteditable="false">Bottom</td>
                        <td contenteditable = "false" > R$ 8,00 </td>
                        <td contenteditable = "false" > 2 </td>
                        <td > R$ 16,00 </td>
                        <td contenteditable = "false" > 22/10/26 </td>
                        <td > Luna </td>
                        <td class = "pendente" > Não </td>
                        <td > Sim </td>
                        <td > 01/12/26 </td>
                        <td > Pagamento pendente </td>
                        <td class = "acoes-linha">
                            <button class = "editar" type = "button" > Editar </button>
                            <button class = "excluir" type = "button" > Excluir </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <section class = "historico" >
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
