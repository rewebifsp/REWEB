<?php
// Esta página está dentro da pasta Cobranca, então o site principal fica uma pasta acima
$raiz = '../';

@session_start();
require('../Adiministracao/conexao.php');
require('../Includes/funcoes.php');

// Sem nada no carrinho não tem o que cobrar
$itens = [];
if (isset($_SESSION['carrinho'])) {
    $itens = $_SESSION['carrinho'];
}
if (count($itens) == 0) {
    header('Location: ../Carrinho/carrinho.php');
    exit;
}

// Para finalizar a compra precisa ter conta. Vai para o login e depois volta para cá (o carrinho continua)
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'cliente') {
    $_SESSION['voltar_para'] = 'Cobranca/cobranca.php';
    header('Location: ../Cadastro/login.php');
    exit;
}

$idUsuario = $_SESSION['usuario']['id'];

// Soma o total da compra
$total = 0;
foreach ($itens as $item) {
    $total = $total + $item['preco'] * $item['quantidade'];
}

// Pega o e-mail e o telefone da conta para já deixar preenchido
$stmt = $con->prepare("SELECT email, telefone FROM login WHERE id = ?");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$conta = mysqli_fetch_assoc($stmt->get_result());

$nome = '';
$telefone = $conta['telefone'];
$email = $conta['email'];
$forma = 'retirada';
$endereco = '';

// Quando a pessoa clica em "Confirmar pedido"
if (isset($_POST['nome'])) {

    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $forma = ($_POST['forma_entrega'] == 'entrega') ? 'entrega' : 'retirada';
    $endereco = trim($_POST['endereco']);

    if ($nome == '' || $telefone == '') {
        $msg = 'Preencha o nome e o telefone.';
    } else if ($forma == 'entrega' && $endereco == '') {
        $msg = 'Para entrega, preencha o endereço.';
    } else {

        // Se for retirada, não precisa de endereço
        if ($forma == 'retirada') {
            $endereco = '';
        }

        // Salva o pedido
        $stmt = $con->prepare("INSERT INTO pedidos (id_usuario, criado_por, cliente, telefone, email, forma_entrega, endereco) VALUES (?, 'site', ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $idUsuario, $nome, $telefone, $email, $forma, $endereco);
        $stmt->execute();
        $idPedido = $con->insert_id; // número do pedido que acabou de ser criado

        // Salva cada item do carrinho dentro do pedido
        foreach ($itens as $item) {
            $stmt = $con->prepare("INSERT INTO itens_pedido (id_pedido, id_produto, produto, valor, quantidade, tema, tamanho, idade, nome_escrito, cor, observacoes, arte) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iisdisssssss", $idPedido, $item['id_produto'], $item['nome'], $item['preco'], $item['quantidade'], $item['tema'], $item['tamanho'], $item['idade'], $item['nome_escrito'], $item['cor'], $item['observacoes'], $item['arte']);
            $stmt->execute();
        }

        // Anota no histórico do painel
        $descricao = $nome . ' solicitou encomenda';
        $stmt = $con->prepare("INSERT INTO historico (descricao, id_pedido) VALUES (?, ?)");
        $stmt->bind_param("si", $descricao, $idPedido);
        $stmt->execute();

        // Esvazia o carrinho e vai para o pagamento
        unset($_SESSION['carrinho']);
        header('Location: pix.php?pedido=' . $idPedido);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar compra - Corisco's Paper</title>
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

    <main class="pagina-cobranca">

        <h1>Finalizar compra</h1>
        <p class="total">Total: <?php echo dinheiro($total); ?></p>

        <form class="form-cobranca" id="formCobranca" action="cobranca.php" method="post">

            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" value="<?php echo escapar($nome); ?>" required>

            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" value="<?php echo escapar($telefone); ?>" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?php echo escapar($email); ?>">

            <p class="titulo-opcao">Como você quer receber?</p>

            <label class="opcao">
                <input type="radio" name="forma_entrega" value="retirada" <?php echo $forma == 'retirada' ? 'checked' : ''; ?>>
                Vou retirar com a Júlia
            </label>

            <label class="opcao">
                <input type="radio" name="forma_entrega" value="entrega" <?php echo $forma == 'entrega' ? 'checked' : ''; ?>>
                Quero que entregue no meu endereço
            </label>

            <div id="campoEndereco">
                <label for="endereco">Endereço completo</label>
                <input type="text" id="endereco" name="endereco" placeholder="Rua, número, bairro, cidade e CEP" value="<?php echo escapar($endereco); ?>">
            </div>

            <button class="botao-confirmar" type="submit">CONFIRMAR PEDIDO</button>
            <a class="voltar" href="../Carrinho/carrinho.php">Voltar ao carrinho</a>

        </form>

    </main>

    <footer>
        REWEB E CORISCO'S PAPER
    </footer>

    <script src="Js/cobranca.js"></script>

    <?php
    // Se tiver mensagem de erro, mostra num alert
    if (isset($msg)) {
        echo "<script>alert('$msg');</script>";
    }
    ?>
    <!-- acessibilidades implementadas -->
    <div data-acc-position="bottom-left"></div>
    <script src="https://cdn.jsdelivr.net/gh/ifrederico/accessible-web-widget@1.6.3/dist/accessible-web-widget.min.js"></script>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>

</body>

</html>
