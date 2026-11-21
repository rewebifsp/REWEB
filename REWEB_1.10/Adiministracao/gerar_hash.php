<?php
// Página só para gerar o hash (senha criptografada) de um administrador.
// Digite a senha, copie o resultado e cole no arquivo adm.php.
// Esta página não guarda nada. Apague este arquivo antes de colocar o site na internet.
require('so-adm.php'); // só um ADM logado consegue abrir

$hash = '';
if (isset($_POST['senha']) && $_POST['senha'] != '') {
    $hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerar hash</title>
</head>
<body>
    <h1>Gerar senha criptografada</h1>
    <form method="post">
        <input type="text" name="senha" placeholder="Digite a senha" required>
        <button type="submit">Gerar</button>
    </form>
    <?php if ($hash != '') { ?>
        <p>Copie e cole no adm.php:</p>
        <textarea rows="3" cols="70" readonly><?php echo $hash; ?></textarea>
    <?php } ?>
    <p><a href="painel.php">Voltar ao painel</a></p>
</body>
</html>
