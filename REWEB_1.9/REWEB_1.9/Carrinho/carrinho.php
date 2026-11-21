<?php
// Esta página está dentro da pasta Carrinho, então o site principal fica uma pasta acima
$raiz = '../';

@session_start();
require('../Includes/funcoes.php');

// O carrinho fica guardado na sessão, por isso funciona mesmo sem conta.
// Ele sobrevive ao F5, mas some quando a pessoa fecha o navegador.
$itens = [];
if (isset($_SESSION['carrinho'])) {
    $itens = $_SESSION['carrinho'];
}

// Soma o valor de todos os itens para mostrar no resumo
$subtotal = 0;
foreach ($itens as $item) {
    $subtotal = $subtotal + $item['preco'] * $item['quantidade'];
}

// Por enquanto não existem adicionais nem taxa de serviço
$adicionais = 0;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../Includes/cabecalho.css">
    <link rel="stylesheet" href="Css/carrinho.css">
</head>

<body>

    <?php include('../Includes/cabecalho.php'); ?>

    <main class="pagina-carrinho">

        <?php if (count($itens) == 0) { ?>

            <!-- Carrinho sem nenhum item -->
            <p class="carrinho-vazio">Seu carrinho está vazio.</p>
            <a class="continuar" href="../index.php">Continuar comprando</a>

        <?php } else { ?>

            <!-- Um bloco para cada item do carrinho -->
            <?php foreach ($itens as $indice => $item) { ?>

                <article class="item-carrinho">

                    <div class="item-esquerda">
                        <img class="foto-item" src="<?php echo $raiz . escapar($item['foto']); ?>" alt="<?php echo escapar($item['nome']); ?>">

                        <div class="item-textos">
                            <h2><?php echo escapar($item['nome']); ?></h2>
                            <p class="descricao"><?php echo escapar($item['especificacoes']); ?></p>
                        </div>
                    </div>

                    <ul class="detalhes">
                        <li>Nome: <?php echo escapar($item['nome_escrito']); ?></li>
                        <li>Idade: <?php echo escapar($item['idade']); ?></li>
                        <li>Tamanho e formato do bolo: <?php echo escapar($item['tamanho']); ?></li>
                        <li>Obs: <?php echo escapar($item['observacoes']); ?></li>
                        <?php if ($item['cor'] != '') { ?>
                            <li>Cor: <?php echo escapar($item['cor']); ?></li>
                        <?php } ?>
                    </ul>

                    <!-- Quantidade: os botões enviam para o atualizar.php, que muda o carrinho na sessão -->
                    <form class="contador" action="atualizar.php" method="post">
                        <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                        <button name="acao" value="menos" type="submit" aria-label="Diminuir quantidade">-</button>
                        <input class="quantidade" type="text" value="<?php echo $item['quantidade']; ?>" readonly>
                        <button name="acao" value="mais" type="submit" aria-label="Aumentar quantidade">+</button>
                    </form>

                    <div class="item-direita">
                        <p class="preco-item"><?php echo dinheiro($item['preco'] * $item['quantidade']); ?></p>
                        <p class="status">STATUS: AGUARDANDO PAGAMENTO</p>

                        <form action="atualizar.php" method="post">
                            <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                            <button class="remover" name="acao" value="remover" type="submit">Remover do carrinho</button>
                        </form>
                    </div>

                </article>

            <?php } ?>

            <section class="resumo">
                <h2>Resumo</h2>

                <p class="linha-resumo"><span>Subtotal:</span> <span id="subtotal"><?php echo dinheiro($subtotal); ?></span></p>
                <p class="linha-resumo"><span>Adicionais:</span> <span><?php echo dinheiro($adicionais); ?></span></p>
                <p class="linha-resumo"><span>Taxa de serviço:</span> <span>Isento</span></p>

                <!-- Quem não está logado é levado ao login na tela de cobrança, e depois volta para ela -->
                <a class="botao-finalizar" href="../Cobranca/cobranca.php">Finalizar compra</a>
                <a class="continuar" href="../index.php">Continuar comprando</a>
            </section>

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
