<?php
// Muda a quantidade de um item do carrinho ou remove o item.
// Quem chama são os botões "-", "+" e "Remover" da tela do carrinho.
@session_start();

$indice = isset($_POST['indice']) ? (int) $_POST['indice'] : -1;
$acao = isset($_POST['acao']) ? $_POST['acao'] : '';

// Só mexe se esse item existir no carrinho
if (isset($_SESSION['carrinho'][$indice])) {

    if ($acao == 'mais') {
        $_SESSION['carrinho'][$indice]['quantidade']++;
    }

    // A quantidade nunca fica menor que 1
    if ($acao == 'menos' && $_SESSION['carrinho'][$indice]['quantidade'] > 1) {
        $_SESSION['carrinho'][$indice]['quantidade']--;
    }

    if ($acao == 'remover') {
        unset($_SESSION['carrinho'][$indice]);
        $_SESSION['carrinho'] = array_values($_SESSION['carrinho']); // reorganiza os números da lista
    }
}

header('Location: carrinho.php');
exit;
