<?php
// Tela de resultado de pesquisa (exemplo: pesquisa.php?pesquisa=batman)
// Esta página está dentro da pasta Pesquisa, então o site principal fica uma pasta acima
$raiz = '../';

@session_start();
require('../Adiministracao/conexao.php'); // conexão com o banco
require('../Includes/funcoes.php');       // funções dinheiro() e escapar()

// Pega o que a pessoa digitou na barra de pesquisa
// (o cabecalho.php usa essa variável para continuar mostrando o texto na barra)
$termoPesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';

// Os % fazem o banco procurar o texto em qualquer parte do nome ou do tema
// (exemplo: "bat" encontra "Topper Batman")
$busca = '%' . $termoPesquisa . '%';

// Procura nos produtos ativos (os cadastrados por último aparecem primeiro)
// O ? é preenchido pelo bind_param, igual no login
$stmt = $con->prepare("SELECT id, nome, preco, foto1
                       FROM produtos
                       WHERE ativo = 1 AND (nome LIKE ? OR tema LIKE ?)
                       ORDER BY id DESC");
$stmt->bind_param("ss", $busca, $busca); // "ss" = dois textos (um para o nome e outro para o tema)
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa - Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../Includes/cabecalho.css">
    <link rel="stylesheet" href="Css/pesquisa.css">
</head>

<body>

    <!-- Topo do site (logo, pesquisa, favoritos, carrinho e login) -->
    <?php include('../Includes/cabecalho.php'); ?>

    <!-- Faixa com o texto da pesquisa e o funil no canto direito -->
    <section class="faixa-pesquisa">

        <?php if ($termoPesquisa == '') { ?>
            <!-- Pesquisa vazia: mostra todos os produtos -->
            <p class="titulo-pesquisa">Todos os produtos</p>
        <?php } else { ?>
            <p class="titulo-pesquisa">Resultados para pesquisa "<?php echo escapar($termoPesquisa); ?>"</p>
        <?php } ?>

        <!-- Funil de filtro (por enquanto só aparece, ainda não faz nada) -->
        <button class="filtro" type="button" aria-label="Filtrar produtos">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 4h18l-7 8.5V20l-4 2v-9.5z" />
            </svg>
        </button>

    </section>

    <main class="pagina-pesquisa">

        <?php if (mysqli_num_rows($resultado) == 0) { ?>

            <!-- Aparece quando nenhum produto combina com o que foi digitado -->
            <p class="sem-produtos">Nenhum produto encontrado para essa pesquisa.</p>

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
