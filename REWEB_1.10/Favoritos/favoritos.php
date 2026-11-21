<?php
// Esta página está dentro da pasta Favoritos, então o site principal fica uma pasta acima
$raiz = '../';

@session_start();
require('../Adiministracao/conexao.php');
require('../Includes/funcoes.php');

// Favoritos pedem uma conta. Se não estiver logado, vai para o login e depois volta para cá
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'cliente') {
    $_SESSION['voltar_para'] = 'Favoritos/favoritos.php';
    header('Location: ../Cadastro/login.php');
    exit;
}

$idUsuario = $_SESSION['usuario']['id'];

// Busca os produtos favoritados por essa pessoa (o JOIN junta favoritos com produtos)
$stmt = $con->prepare("SELECT p.id, p.nome, p.preco, p.foto1
                       FROM favoritos f
                       JOIN produtos p ON p.id = f.id_produto
                       WHERE f.id_usuario = ?
                       ORDER BY f.id DESC");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favoritos - Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Caveat:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../Includes/cabecalho.css">
    <link rel="stylesheet" href="Css/favoritos.css">
</head>

<body>

    <?php include('../Includes/cabecalho.php'); ?>

    <section class="faixa-titulo">
        <h1>FAVORITOS</h1>
        <p>Seus</p>
    </section>

    <main class="pagina-favoritos">

        <?php if (mysqli_num_rows($resultado) == 0) { ?>

            <p class="sem-favoritos">Você ainda não favoritou nenhum produto.</p>

        <?php } else { ?>

            <div class="lista-produtos">
                <?php while ($produto = mysqli_fetch_assoc($resultado)) { ?>

                    <article class="cartao-favorito">
                        <img src="<?php echo $raiz . ($produto['foto1'] != '' ? escapar($produto['foto1']) : 'imagens/caixa.png'); ?>" alt="<?php echo escapar($produto['nome']); ?>">
                        <h2><?php echo escapar($produto['nome']); ?></h2>
                        <p class="preco"><?php echo dinheiro($produto['preco']); ?></p>
                        <a class="botao-comprar" href="../Produto/produto.php?id=<?php echo $produto['id']; ?>">COMPRAR</a>
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
