<?php
// Liga e desliga o favorito de um produto (quem chama é o coração da tela de produto)
@session_start();
require('../Adiministracao/conexao.php');

$id = isset($_POST['id_produto']) ? (int) $_POST['id_produto'] : 0;

if ($id == 0) {
    header('Location: ../index.php');
    exit;
}

// Favoritos pedem uma conta. Se não estiver logado, vai para o login e depois volta para o produto
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'cliente') {
    $_SESSION['voltar_para'] = 'Produto/produto.php?id=' . $id;
    header('Location: ../Cadastro/login.php');
    exit;
}

$idUsuario = $_SESSION['usuario']['id'];

// Esse produto já está nos favoritos dessa pessoa?
$stmt = $con->prepare("SELECT id FROM favoritos WHERE id_usuario = ? AND id_produto = ?");
$stmt->bind_param("ii", $idUsuario, $id);
$stmt->execute();
$resultado = $stmt->get_result();

if (mysqli_num_rows($resultado) > 0) {
    // Já era favorito: remove
    $stmt = $con->prepare("DELETE FROM favoritos WHERE id_usuario = ? AND id_produto = ?");
} else {
    // Ainda não era: adiciona
    $stmt = $con->prepare("INSERT INTO favoritos (id_usuario, id_produto) VALUES (?, ?)");
}
$stmt->bind_param("ii", $idUsuario, $id);
$stmt->execute();

// Volta para a tela do produto
header('Location: ../Produto/produto.php?id=' . $id);
exit;
