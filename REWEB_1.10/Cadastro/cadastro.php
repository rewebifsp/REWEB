<?php
// Verifica se os campos do formulário foram enviados via POST
if (isset($_POST['email']) && isset($_POST['telefone']) && isset($_POST['senha']) && isset($_POST['confirmar_senha'])) {

    // Conecta com o banco de dados
    require('../Adiministracao/conexao.php');

    // Verifica a conexão com o banco de dados
    if (!$con) {
        die('Erro de conexão: ' . mysqli_connect_error());
    }

    // Lista dos administradores (esses e-mails não podem ser usados no cadastro)
    require('../Adiministracao/adm.php');

    // Recebe os dados enviados pelo formulário
    $email = trim($_POST['email']);
    $telefone = preg_replace('/\D/', '', $_POST['telefone']); // deixa só os números do telefone
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar_senha'];

    // Verifica se o e-mail OU o telefone já estão cadastrados
    // (o telefone também não pode repetir, porque dá para entrar no site com ele)
    // O prepare com ? protege contra SQL Injection (igual ao login.php)
    $stmt = $con->prepare("SELECT id FROM login WHERE email = ? OR telefone = ?");
    $stmt->bind_param("ss", $email, $telefone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($email == '' || $senha == '') {
        $msg = 'Falha ao fazer o registro. Tente novamente.'; // Mensagem de erro caso falte e-mail ou senha
    } else if ($senha != $confirmar) {
        $msg = 'As senhas não são iguais!'; // O JavaScript já avisa, mas aqui confere de novo
    } else if (isset($administradores[strtolower($email)])) {
        $msg = 'Este e-mail ou telefone já está cadastrado. Por favor, use outro.'; // E-mail reservado para administrador
    } else if (mysqli_num_rows($result) > 0) {
        $msg = 'Este e-mail ou telefone já está cadastrado. Por favor, use outro.'; // Mensagem de erro se já existir
    } else {
        // Criptografa a senha ANTES de salvar, assim a senha de verdade nunca vai para o banco
        $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

        // Insere o novo usuário no banco
        $stmt = $con->prepare("INSERT INTO login (telefone, email, senha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $telefone, $email, $senhaCriptografada); // "sss" = três strings
        $stmt->execute();

        header('Location: login.php'); // Redireciona para a página de login após o registro
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
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

            <!-- o formulário envia para a própria página, o PHP lá em cima faz o cadastro -->
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
