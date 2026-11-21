<?php
// Esta página está dentro da pasta Produto, então o site principal fica uma pasta acima
$raiz = '../';

@session_start();
require('../Adiministracao/conexao.php'); // conexão com o banco
require('../Includes/funcoes.php');

// Pega o id do produto que veio no link (exemplo: produto.php?id=1)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Procura o produto no banco (o ? é preenchido pelo bind_param, igual no login)
$stmt = $con->prepare("SELECT * FROM produtos WHERE id = ? AND ativo = 1");
$stmt->bind_param("i", $id); // "i" = número inteiro
$stmt->execute();
$resultado = $stmt->get_result();
$produto = mysqli_fetch_assoc($resultado); // $produto vira uma lista com os dados do produto

// Se não achou nenhum produto com esse id, volta para a tela inicial
if (!$produto) {
    header('Location: ../index.php');
    exit;
}

// Conta quantas pessoas favoritaram esse produto
$stmt = $con->prepare("SELECT COUNT(*) AS total FROM favoritos WHERE id_produto = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$linha = mysqli_fetch_assoc($stmt->get_result());
$totalFavoritos = $linha['total'];

// Descobre se quem está logado já favoritou esse produto
$favoritado = false;
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] == 'cliente') {
    $idUsuario = $_SESSION['usuario']['id'];
    $stmt = $con->prepare("SELECT id FROM favoritos WHERE id_usuario = ? AND id_produto = ?");
    $stmt->bind_param("ii", $idUsuario, $id);
    $stmt->execute();
    $favoritado = mysqli_num_rows($stmt->get_result()) > 0;
}

// Junta as fotos que existem (algumas podem estar vazias)
$fotos = [];
foreach ([$produto['foto1'], $produto['foto2'], $produto['foto3']] as $foto) {
    if ($foto != '') {
        $fotos[] = $foto;
    }
}
if (count($fotos) == 0) {
    $fotos[] = 'imagens/caixa.png'; // imagem padrão quando o produto não tem foto
}

// As cores vêm no banco separadas por vírgula (exemplo: Azul, Rosa)
$cores = [];
if ($produto['cores_disponiveis'] != '') {
    $cores = explode(',', $produto['cores_disponiveis']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escapar($produto['nome']); ?> - Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../Includes/cabecalho.css">
    <link rel="stylesheet" href="Css/produto.css">
</head>

<body>

    <?php include('../Includes/cabecalho.php'); ?>

    <main class="pagina-produto">

        <!-- Lado esquerdo: fotos e especificações -->
        <section class="coluna-fotos">

            <div class="foto-grande">
                <img id="fotoGrande" src="<?php echo $raiz . escapar($fotos[0]); ?>" alt="<?php echo escapar($produto['nome']); ?>">

                <!-- Coração: quem não está logado é levado ao login -->
                <form action="../Favoritos/favoritar.php" method="post">
                    <input type="hidden" name="id_produto" value="<?php echo $id; ?>">
                    <button class="coracao <?php echo $favoritado ? 'ativo' : ''; ?>" type="submit" aria-label="Favoritar produto">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8z" />
                        </svg>
                        <span><?php echo $totalFavoritos; ?></span>
                    </button>
                </form>
            </div>

            <div class="miniaturas">
                <?php foreach ($fotos as $foto) { ?>
                    <button class="miniatura" type="button"><img src="<?php echo $raiz . escapar($foto); ?>" alt="Foto do produto"></button>
                <?php } ?>
            </div>

            <div class="especificacoes">
                <h2>ESPECIFICAÇÕES</h2>
                <p><?php echo escapar($produto['especificacoes']); ?></p>
            </div>

        </section>

        <!-- Lado direito: nome, preço e personalização -->
        <section class="coluna-info">

            <h1 class="nome-produto"><?php echo escapar($produto['nome']); ?></h1>
            <p class="preco-produto"><?php echo dinheiro($produto['preco']); ?></p>

            <!-- Envia o produto para o carrinho (o carrinho funciona mesmo sem conta) -->
            <form class="form-produto" action="../Carrinho/adicionar.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_produto" value="<?php echo $id; ?>">


                <div class="campos-produto">

                    <div class="grupo-produto">
                        <label for="tamanho">TAMANHO E FORMATO DO BOLO:</label>
                        <div class="campo-produto">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20l1-4L16 5l3 3L8 19l-4 1z" /><path d="M14 7l3 3" /></svg>
                            <input type="text" id="tamanho" name="tamanho">
                        </div>
                    </div>

                    <div class="grupo-produto">
                        <label for="idade">IDADE A SER ESCRITA:</label>
                        <div class="campo-produto">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20l1-4L16 5l3 3L8 19l-4 1z" /><path d="M14 7l3 3" /></svg>
                            <input type="text" id="idade" name="idade">
                        </div>
                    </div>

                    <div class="grupo-produto">
                        <label for="nome_escrito">NOME A SER ESCRITO:</label>
                        <div class="campo-produto">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20l1-4L16 5l3 3L8 19l-4 1z" /><path d="M14 7l3 3" /></svg>
                            <input type="text" id="nome_escrito" name="nome_escrito">
                        </div>
                    </div>

                    <div class="grupo-produto">
                        <label for="observacoes">OBSERVAÇÕES:</label>
                        <div class="campo-produto">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20l1-4L16 5l3 3L8 19l-4 1z" /><path d="M14 7l3 3" /></svg>
                            <input type="text" id="observacoes" name="observacoes">
                        </div>
                    </div>

                </div>

                <?php if (count($cores) > 0) { ?>
                    <div class="grupo-produto grupo-cor">
                        <label for="cor">COR:</label>
                        <div class="campo-produto">
                            <select id="cor" name="cor">
                                <?php foreach ($cores as $cor) { ?>
                                    <option><?php echo escapar(trim($cor)); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>

                <!-- Botão para enviar uma imagem de referência -->
                <label class="botao-imagem" title="Enviar uma imagem">
                    <input type="file" name="imagem" accept="image/*">
                    <svg viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M6 36l10-14 8 10 6-7 12 11z" fill="#5a9a3a" stroke="none" />
                        <circle cx="34" cy="13" r="5" fill="#f5c542" stroke="none" />
                        <path d="M24 30V18M19 23l5-5 5 5" />
                    </svg>
                </label>

                <button class="botao-carrinho" type="submit">
                    <img src="<?php echo $raiz; ?>imagens/carrinho.png" alt="">
                    ADICIONAR AO CARRINHO
                </button>

            </form>

        </section>

    </main>

    <footer>
        REWEB E CORISCO'S PAPER
    </footer>

    <script src="Js/produto.js"></script>
    <!-- acessibilidades implementadas -->
    <div data-acc-position="bottom-left"></div>
    <script src="https://cdn.jsdelivr.net/gh/ifrederico/accessible-web-widget@1.6.3/dist/accessible-web-widget.min.js"></script>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
</body>

</html>
