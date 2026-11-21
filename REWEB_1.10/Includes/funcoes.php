<?php
// Funções pequenas que várias páginas usam

// Transforma 55 em "R$55,00"
function dinheiro($valor) {
    return 'R$' . number_format($valor, 2, ',', '.');
}

// Deixa um texto seguro para mostrar na página
// (assim ninguém consegue escrever código HTML no meio de um nome, por exemplo)
function escapar($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Guarda uma imagem enviada pelo formulário na pasta uploads.
// Devolve o caminho da imagem (exemplo: uploads/arte_123.png) ou '' se não veio imagem válida.
function salvarImagem($campo, $prefixo) {
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] != 0) {
        return '';
    }

    $extensao = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Só aceita se a extensão for de imagem e o arquivo for uma imagem de verdade
    if (!in_array($extensao, $permitidas) || getimagesize($_FILES[$campo]['tmp_name']) === false) {
        return '';
    }

    if (!is_dir('../uploads')) {
        mkdir('../uploads');
    }

    $caminho = 'uploads/' . uniqid($prefixo) . '.' . $extensao;
    move_uploaded_file($_FILES[$campo]['tmp_name'], '../' . $caminho);
    return $caminho;
}
