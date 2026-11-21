<?php
// Tela de produtos pronta entrega: mostra só os produtos que já estão prontos para levar
// Esta página está dentro da pasta ProntaEntrega, então o site principal fica uma pasta acima
$raiz = '../';

@session_start();
require('../Adiministracao/conexao.php'); // conexão com o banco
require('../Includes/funcoes.php');       // funções dinheiro() e escapar()

// ------------------------------------------------------------
// FILTRO: a pessoa marca o que NÃO quer ver
// As caixas marcadas chegam no endereço assim: ?esconder[]=2&esconder[]=5
// (os números são os ids dos tópicos que ela quer esconder)
// ------------------------------------------------------------
$esconder = [];
if (isset($_GET['esconder']) && is_array($_GET['esconder'])) {
    // intval deixa só números (assim ninguém consegue colocar código SQL no endereço)
    $esconder = array_map('intval', $_GET['esconder']);
}

// Procura os tópicos que têm produtos pronta entrega (são as opções do filtro)
$consulta = $con->query("SELECT DISTINCT t.id, t.nome
                         FROM produtos p
                         JOIN topicos t ON t.id = p.id_topico
                         WHERE p.tipo = 'pronta_entrega' AND p.ativo = 1
                         ORDER BY t.nome");

// Guarda as opções numa lista (id => nome) para usar mais de uma vez na página
// e já separa os nomes dos tópicos escondidos para avisar a pessoa
$opcoes = [];
$nomesEscondidos = [];
while ($linha = mysqli_fetch_assoc($consulta)) {
    $opcoes[$linha['id']] = $linha['nome'];

    if (in_array($linha['id'], $esconder)) {
        $nomesEscondidos[] = $linha['nome'];
    }
}

// Monta o pedaço da busca que esconde os tópicos marcados
$filtroSql = '';
if (count($esconder) > 0) {
    $lista = implode(',', $esconder); // exemplo: 2,5
    // Os produtos sem tópico (vazio) continuam aparecendo
    $filtroSql = " AND (id_topico IS NULL OR id_topico NOT IN ($lista))";
}

// Busca no banco os produtos ativos que são do tipo 'pronta_entrega'
// (os cadastrados por último aparecem primeiro), já sem os tópicos escondidos
$resultado = $con->query("SELECT id, nome, preco, foto1
                          FROM produtos
                          WHERE tipo = 'pronta_entrega' AND ativo = 1" . $filtroSql . "
                          ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pronta entrega - Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../Includes/cabecalho.css">
    <link rel="stylesheet" href="Css/pronta-entrega.css">
</head>

<body>

    <!-- Topo do site (logo, pesquisa, favoritos, carrinho e login) -->
    <?php include('../Includes/cabecalho.php'); ?>

    <!-- Faixa azul com o título (imagem da letra de mão) e o funil no canto direito -->
    <section class="faixa-pronta">

        <h1 class="titulo-pronta">
            <!-- Título "pronta entrega" recortado do protótipo (letra de mão não existe no CSS) -->
            <img src="../imagens/pronta-entrega.png" alt="Pronta entrega">
        </h1>

        <!-- Funil: ao clicar, abre o painel do filtro (o <details> abre e fecha sozinho) -->
        <details class="filtro">
            <summary aria-label="Filtrar produtos">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 4h18l-7 8.5V20l-4 2v-9.5z" />
                </svg>
            </summary>

            <!-- Painel do filtro: a pessoa marca o que NÃO quer ver -->
            <form class="filtro-painel" method="get" action="pronta-entrega.php">

                <fieldset>
                    <legend>Marque o que você NÃO quer ver</legend>

                    <?php if (count($opcoes) == 0) { ?>

                        <p class="sem-opcoes">Não há opções de filtro por enquanto.</p>

                    <?php } else { ?>

                        <!-- Uma caixinha para cada tópico (fica marcada se já estava escondido) -->
                        <?php foreach ($opcoes as $id => $nome) { ?>
                            <label>
                                <input type="checkbox" name="esconder[]" value="<?php echo (int) $id; ?>" <?php echo in_array($id, $esconder) ? 'checked' : ''; ?>>
                                <?php echo escapar($nome); ?>
                            </label>
                        <?php } ?>

                    <?php } ?>
                </fieldset>

                <button class="botao-aplicar" type="submit">APLICAR FILTRO</button>
                <a class="limpar-filtro" href="pronta-entrega.php">Limpar filtro</a>

            </form>
        </details>

    </section>

    <main class="pagina-pronta">

        <!-- Aviso que lembra a pessoa de que tem um filtro ligado -->
        <?php if (count($nomesEscondidos) > 0) { ?>
            <p class="aviso-filtro">
                Filtro ligado: você não está vendo <strong><?php echo escapar(implode(', ', $nomesEscondidos)); ?></strong>.
                <a href="pronta-entrega.php">Limpar filtro</a>
            </p>
        <?php } ?>

        <?php if (mysqli_num_rows($resultado) == 0) { ?>

            <?php if (count($esconder) > 0) { ?>
                <!-- Aparece quando o filtro escondeu todos os produtos -->
                <p class="sem-produtos">Nenhum produto sobrou com esse filtro.</p>
            <?php } else { ?>
                <!-- Aparece quando não existe nenhum produto pronta entrega -->
                <p class="sem-produtos">Ainda não temos produtos pronta entrega.</p>
            <?php } ?>

        <?php } else { ?>

            <div class="lista-produtos">

                <!-- Repete um cartão para cada produto encontrado no banco -->
                <?php while ($produto = mysqli_fetch_assoc($resultado)) { ?>

                    <article class="cartao-produto">
                        <!-- Foto do produto (se não tiver foto, usa a imagem padrão) -->
                        <img src="<?php echo $raiz . ($produto['foto1'] != '' ? escapar($produto['foto1']) : 'imagens/caixa.png'); ?>" alt="<?php echo escapar($produto['nome']); ?>">

                        <!-- Nome e preço -->
                        <h2><?php echo escapar($produto['nome']); ?></h2>
                        <p class="preco"><?php echo dinheiro($produto['preco']); ?></p>

                        <!-- Botão azul que leva para a tela do produto -->
                        <a class="botao-comprar" href="../Produto/produto.php?id=<?php echo (int) $produto['id']; ?>">COMPRAR</a>
                    </article>

                <?php } ?>

            </div>

        <?php } ?>

    </main>

    <footer>
        REWEB E CORISCO'S PAPER
    </footer>

    <!-- acessibilidades implementadas -->
    <div data-acc-position="bottom-left"></div>
    <script src="https://cdn.jsdelivr.net/gh/ifrederico/accessible-web-widget@1.6.3/dist/accessible-web-widget.min.js"></script>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>

</body>

</html>
