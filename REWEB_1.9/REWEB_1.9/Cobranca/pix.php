<?php
// Tela de pagamento com Pix (por enquanto o QR Code e o código são de mentira, só para apresentar)
$raiz = '../';

@session_start();
require('../Adiministracao/conexao.php');
require('../Includes/funcoes.php');

// Só quem fez o pedido pode ver essa tela
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'cliente') {
    header('Location: ../Cadastro/login.php');
    exit;
}

$idUsuario = $_SESSION['usuario']['id'];
$idPedido = isset($_GET['pedido']) ? (int) $_GET['pedido'] : 0;

// Confere se o pedido existe e é dessa pessoa
$stmt = $con->prepare("SELECT id FROM pedidos WHERE id = ? AND id_usuario = ?");
$stmt->bind_param("ii", $idPedido, $idUsuario);
$stmt->execute();
if (mysqli_num_rows($stmt->get_result()) == 0) {
    header('Location: ../index.php');
    exit;
}

// Soma o valor do pedido
$stmt = $con->prepare("SELECT SUM(valor * quantidade) AS total FROM itens_pedido WHERE id_pedido = ?");
$stmt->bind_param("i", $idPedido);
$stmt->execute();
$linha = mysqli_fetch_assoc($stmt->get_result());
$total = $linha['total'];

// Código Pix de mentira (muda de acordo com o número do pedido)
$codigoPix = '00020126FAKE' . strtoupper(md5('corisco' . $idPedido)) . '6304ABCD';

// Desenha um "QR Code" de mentira: quadradinhos pretos e brancos sorteados.
// O mt_srand com o número do pedido faz o desenho ser sempre o mesmo para o mesmo pedido
mt_srand($idPedido);
$tamanho = 25;
$quadradinhos = '';
for ($linhaQr = 0; $linhaQr < $tamanho; $linhaQr++) {
    for ($colunaQr = 0; $colunaQr < $tamanho; $colunaQr++) {

        // Os 3 cantos ficam reservados para os quadrados grandes
        $canto = ($linhaQr < 8 && $colunaQr < 8) || ($linhaQr < 8 && $colunaQr > 16) || ($linhaQr > 16 && $colunaQr < 8);

        if (!$canto && mt_rand(0, 1) == 1) {
            $quadradinhos .= '<rect x="' . $colunaQr . '" y="' . $linhaQr . '" width="1" height="1"/>';
        }
    }
}
// Quadrados dos cantos
$cantos = '';
foreach ([[0, 0], [18, 0], [0, 18]] as $posicao) {
    $x = $posicao[0];
    $y = $posicao[1];
    $cantos .= '<rect x="' . $x . '" y="' . $y . '" width="7" height="7"/>';
    $cantos .= '<rect x="' . ($x + 1) . '" y="' . ($y + 1) . '" width="5" height="5" fill="#fff"/>';
    $cantos .= '<rect x="' . ($x + 2) . '" y="' . ($y + 2) . '" width="3" height="3"/>';
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento - Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="Css/cobranca.css">
</head>

<body>

    <header class="cabecalho">
        <a class="logo" href="../index.php" aria-label="Voltar para a tela inicial">
            <img class="logo-img" src="../imagens/logo.png" alt="Corisco's Paper - Papelaria Criativa">
        </a>
    </header>

    <main class="pagina-pix">

        <h1>Pague com Pix</h1>
        <p class="valor-pix"><?php echo dinheiro($total); ?></p>

        <div class="caixa-qr">
            <svg viewBox="-1 -1 27 27" aria-label="QR Code de exemplo">
                <rect x="-1" y="-1" width="27" height="27" fill="#fff"/>
                <?php echo $cantos; ?>
                <?php echo $quadradinhos; ?>
            </svg>
        </div>

        <h2>Escaneie o QR Code</h2>
        <p>Abra o aplicativo do seu banco e escaneie o código para pagar</p>

        <section class="caixa-copia">
            <h3>Ou copie o código Pix</h3>
            <p>Código Pix copia e cola</p>
            <p class="codigo" id="codigoPix"><?php echo $codigoPix; ?></p>
            <button class="botao-copiar" id="botaoCopiar" type="button">COPIAR CÓDIGO PIX</button>
        </section>

        <p class="aguardando">Aguardando confirmação de pagamento.</p>
        <a class="voltar" href="../index.php">Voltar ao início</a>

    </main>

    <footer>
        REWEB E CORISCO'S PAPER
    </footer>

    <script>
        // Copia o código Pix para a área de transferência
        const botaoCopiar = document.getElementById('botaoCopiar');

        botaoCopiar.addEventListener('click', function () {
            navigator.clipboard.writeText(document.getElementById('codigoPix').textContent);
            botaoCopiar.textContent = 'CÓDIGO COPIADO!';
        });
    </script>

    <!-- acessibilidades implementadas -->
    <div data-acc-position="bottom-left"></div>
    <script src="https://cdn.jsdelivr.net/gh/ifrederico/accessible-web-widget@1.6.3/dist/accessible-web-widget.min.js"></script>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>

</body>

</html>
