<?php
// Coloca um produto no carrinho. Quem chama é o botão "Adicionar ao carrinho" da tela de produto.
// O carrinho fica na sessão, então funciona sem conta.
@session_start();
require('../Adiministracao/conexao.php');
require('../Includes/funcoes.php');

$id = isset($_POST['id_produto']) ? (int) $_POST['id_produto'] : 0;

// Busca o produto no banco. O preço vem do banco, nunca do formulário
$stmt = $con->prepare("SELECT id, nome, tema, preco, especificacoes, foto1 FROM produtos WHERE id = ? AND ativo = 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$produto = mysqli_fetch_assoc($stmt->get_result());

if (!$produto) {
    header('Location: ../index.php');
    exit;
}

// Se a pessoa enviou uma imagem de referência (arte), guarda na pasta uploads
$arte = salvarImagem('imagem', 'arte_');

// Monta o item com o que a pessoa escreveu na tela de produto
$item = [
    'id_produto' => $produto['id'],
    'nome' => $produto['nome'],
    'tema' => $produto['tema'],
    'especificacoes' => $produto['especificacoes'],
    'preco' => $produto['preco'],
    'foto' => $produto['foto1'] != '' ? $produto['foto1'] : 'imagens/caixa.png',
    'quantidade' => 1,
    'tamanho' => isset($_POST['tamanho']) ? trim($_POST['tamanho']) : '',
    'idade' => isset($_POST['idade']) ? trim($_POST['idade']) : '',
    'nome_escrito' => isset($_POST['nome_escrito']) ? trim($_POST['nome_escrito']) : '',
    'cor' => isset($_POST['cor']) ? trim($_POST['cor']) : '',
    'observacoes' => isset($_POST['observacoes']) ? trim($_POST['observacoes']) : '',
    'arte' => $arte
];

// Cria o carrinho se ainda não existir e coloca o item no final
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}
$_SESSION['carrinho'][] = $item;

header('Location: carrinho.php');
exit;
