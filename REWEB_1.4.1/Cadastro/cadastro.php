<?php
// Verifica se os campos 'id', 'email' e 'senha' foram enviados via POST
if (isset($_POST['email']) && isset($_POST['senha'])) {
    // Conecta com o banco de dados
    require('../Adiministracao/conexao.php');

    // Recebe o id, senha e email enviados pelo formulário
    $telefone = $_POST['telefone'];
    $senha = $_POST['senha'];
    $email = $_POST['email'];

    // Verifica se o e-mail já está cadastrado
    $q = "SELECT * FROM login WHERE email = '$email'";
    $result = mysqli_query($con, $q);

    if (mysqli_num_rows($result) > 0) {
        $msg = 'Este e-mail já está cadastrado. Por favor, use outro e-mail.'; // Mensagem de erro se o e-mail já existir
    } else {
        // Cria a consulta SQL para inserir o novo usuário
        $q = "INSERT INTO login (telefone, email, senha) VALUES ('$telefone','$email' ,'$senha')";
        mysqli_query($con, $q);

        // Verifica se o e-mail e senha não estão vazios antes de atualizar a senha
        if ($email != '' && $senha != '') {
            $senha = password_hash($senha, PASSWORD_DEFAULT); // Criptografa a senha
            $q = "UPDATE login SET senha='$senha' WHERE email='$email'";
            mysqli_query($con, $q);
            header('Location: login.php'); // Redireciona para a página de login após o registro
            exit;
        } else {
            $msg = 'Falha ao fazer o registro. Tente novamente.'; // Mensagem de erro caso a inscrição falhe
        }
    } //obs falta jogar o msg em um alert, mas to com sono, depois faço isso ou alguem ai faz a boa ai  -Dev sono
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Css/style.css">
    <link rel="stylesheet" href="Css/login.css">
    <link rel="stylesheet" href="Css/cadastro.css">
</head>

<body>

    <header class="cabecalho">
        <a class="logo" href="../index.php">
            <img class="logo-img" src="../imagens/logo.png" alt="Corisco's Paper - Papelaria Criativa">
        </a>
    </header>

    <main class="area-login">

        <section class="coluna-form">

            <!-- trocar o action quando o PHP do cadastro estiver pronto -->
            <form class="form-login" id="formCadastro" action="#" method="post">

                <div class="grupo">
                    <label class="rotulo" for="email">*E-MAIL</label>
                    <input class="campo" type="email" id="email" name="email" placeholder="E-MAIL" required>
                </div>

                <div class="grupo">
                    <label class="rotulo" for="telefone">*TELEFONE</label>
                    <input class="campo" type="tel" id="telefone" name="telefone" placeholder="TELEFONE" required>
                </div>

                <div class="grupo">
                    <label class="rotulo" for="senha">*SENHA</label>
                    <input class="campo" type="password" id="senha" name="senha" placeholder="SENHA" required>
                </div>

                <div class="grupo">
                    <label class="rotulo" for="confirmar">*REPITA A SENHA</label>
                    <input class="campo" type="password" id="confirmar" name="confirmar_senha"
                        placeholder="CONFIRMAR SENHA" required>
                </div>

                <button class="botao-entrar" type="submit">CRIAR CONTA</button>

            </form>

            <p class="sem-conta">JÁ TEM UMA CONTA? <a class="link-azul" href="login.php">FAÇA LOGIN</a></p>

        </section>

        <div class="coluna-imagem">
            <img src="../imagens/caixa.png" alt="" onerror="this.parentElement.remove()">
        </div>

    </main>

    <footer>
        REWEB E CORISCO'S PAPER
    </footer>

    <script src="Js/cadastro.js"></script>

</body>

</html>
