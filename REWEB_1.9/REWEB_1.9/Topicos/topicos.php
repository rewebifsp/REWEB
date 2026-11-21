<?php
// Tela de tópicos: uma única tela que serve para todas as categorias.
// Ela muda conforme o nome que vem no endereço (exemplo: topicos.php?nome=Brindes)
// Sem nome no endereço (topicos.php) ela mostra todos os produtos (categoria GERAL)
// Esta página está dentro da pasta Topicos, então o site principal fica uma pasta acima
$raiz = '../';

@session_start();
require('../Adiministracao/conexao.php'); // conexão com o banco
require('../Includes/funcoes.php');       // funções dinheiro() e escapar()

// Pega o nome da categoria que veio no link
$nome = isset($_GET['nome']) ? trim($_GET['nome']) : '';

if ($nome == '') {

    // GERAL: sem nome no endereço, mostra todos os produtos ativos
    $titulo = 'Produtos';
    $resultado = $con->query("SELECT id, nome, preco, foto1
                              FROM produtos
                              WHERE ativo = 1
                              ORDER BY id DESC");

} else {

    // Procura a categoria no banco (o ? é preenchido pelo bind_param, igual no login)
    $stmt = $con->prepare("SELECT id, nome FROM topicos WHERE nome = ?");
    $stmt->bind_param("s", $nome); // "s" = texto
    $stmt->execute();
    $topico = mysqli_fetch_assoc($stmt->get_result()); // $topico vira uma lista com os dados da categoria

    // Se não existe nenhuma categoria com esse nome, volta para a tela inicial
    if (!$topico) {
        header('Location: ../index.php');
        exit;
    }

    $titulo = $topico['nome'];

    // Busca os produtos ativos dessa categoria (os cadastrados por último aparecem primeiro)
    $stmt = $con->prepare("SELECT id, nome, preco, foto1
                           FROM produtos
                           WHERE id_topico = ? AND ativo = 1
                           ORDER BY id DESC");
    $stmt->bind_param("i", $topico['id']); // "i" = número inteiro
    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escapar($titulo); ?> - Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Playfair+Display:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../Includes/cabecalho.css">
    <link rel="stylesheet" href="Css/topicos.css">
</head>

<body>

    <!-- Topo do site (logo, pesquisa, favoritos, carrinho e login) -->
    <?php include('../Includes/cabecalho.php'); ?>

    <!-- Faixa azul com o título: "DIVERSOS" pequeno e o nome da categoria grande -->
    <section class="faixa-topico">
        <h1 class="titulo-topico">
            <span>DIVERSOS</span>
            <?php echo escapar($titulo); ?>
        </h1>
    </section>

    <main class="pagina-topico">

        <?php if (mysqli_num_rows($resultado) == 0) { ?>

            <!-- Aparece quando a categoria ainda não tem nenhum produto -->
            <p class="sem-produtos">Ainda não temos produtos nessa categoria.</p>

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
