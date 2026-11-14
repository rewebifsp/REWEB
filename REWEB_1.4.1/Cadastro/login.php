<?php
// Verifica se o formulário foi enviado com 'id' (email) e 'senha'
if (isset($_POST['email']) && isset($_POST['senha'])) {

    // Conecta com o banco de dados
    require('../Adiministracao/conexao.php'); // Certifique-se de que o arquivo de conexão está correto

    // Verifica a conexão com o banco de dados
    if (!$con) {
        die('Erro de conexão: ' . mysqli_connect_error());
    }

    // Escapa os dados recebidos para evitar SQL Injection
    $email = mysqli_real_escape_string($con, trim($_POST['email'])); // Escapa o email para segurança
    $senha = trim($_POST['senha']); // Remove espaços extras da senha

    // Prepara e executa a consulta SQL para verificar o usuário no banco de dados
    $stmt = $con->prepare("SELECT * FROM login WHERE email = ?");
    $stmt->bind_param("s", $email); // "s" significa string, pois $email é um string (email)
    $stmt->execute();
    $result = $stmt->get_result(); // Obtém o resultado da consulta

    // Verifica se encontrou um usuário com o email fornecido
    if ($result && mysqli_num_rows($result) > 0) {
        $usuario = mysqli_fetch_assoc($result); // Obtém os dados do usuário encontrado

        // Depuração - Exibe os dados do usuário para verificação
        // var_dump($usuario); // Remova a depuração em produção

        // Compara a senha informada com a senha armazenada no banco (verifica a correspondência)
        if (password_verify($senha, $usuario['senha'])) {
            
            session_start(); // Inicia uma sessão para armazenar informações do usuário
            $_SESSION['usuario'] = $usuario; // Armazena os dados do usuário na sessão

            // Redireciona para a página principal do sistema após o login bem-sucedido
            header('Location: ../index.php');
            exit; // Garante que o código não continue após o redirecionamento
        } else {
            // Se a senha estiver incorreta, exibe uma mensagem de erro
            $msg = 'Senha incorreta!';
        }
    } else {
        // Se o usuário não for encontrado no banco de dados, exibe uma mensagem de erro
        $msg = 'Usuário não encontrado!';
    }
} //obs falta jogar o msg em um alert, mas to com sono, depois faço isso ou alguem ai faz a boa ai  -Dev sono
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Corisco's Paper</title>
    <!-- style.css é o mesmo da tela inicial (cores, logo e rodapé) -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Css/style.css">
    <link rel="stylesheet" href="Css/login.css">
</head>

<body>

    <!-- Topo: a logo é um botão que volta para a tela inicial (usar o mesmo em todas as telas) -->
    <header class="cabecalho">
        <a class="logo" href="../index.php" aria-label="Voltar para a tela inicial">
            <img class="logo-img" src="../imagens/logo.png" alt="Corisco's Paper - Papelaria Criativa">
        </a>
    </header>

    <main class="area-login">

        <section class="coluna-form">

            <!-- Entrar com Google: por enquanto só o desenho. A parte que funciona fica para o final -->
            <button class="botao-google" type="button">ENTRA COM GOOGLE</button>

            <p class="ou">OU</p>

            <!-- Quando o João terminar o PHP, trocar o action pelo arquivo de login -->
            <form class="form-login" action="#" method="post">

                <label class="so-leitor" for="email">Usuário (e-mail ou telefone)</label>
                <input class="campo" type="text" id="email" name="email"
                    placeholder="USUÁRIO (e-mail ou telefone)" autocomplete="username" required>

                <div class="campo-senha">
                    <label class="so-leitor" for="senha">Senha</label>
                    <input class="campo" type="password" id="senha" name="senha"
                        placeholder="SENHA" autocomplete="current-password" required>

                    <!-- Olhinho: mostra ou esconde a senha -->
                    <button class="olho" id="olho" type="button" aria-label="Mostrar senha" aria-pressed="false">
                        <svg viewBox="0 0 32 24" aria-hidden="true">
                            <path d="M2 12C6 5 12 3 16 3s10 2 14 9c-4 7-10 9-14 9S6 19 2 12z" />
                            <circle cx="16" cy="12" r="4.5" />
                            <line class="risco" x1="4" y1="21" x2="28" y2="3" />
                        </svg>
                    </button>
                </div>

                <a class="link-azul esqueci" href="esqueci-senha.html">ESQUECI A SENHA</a>

                <button class="botao-entrar" type="submit">ENTRAR</button>

            </form>

            <p class="sem-conta">NÃO TEM UMA CONTA? <a class="link-azul" href="cadastro.php">CADASTRE-SE</a></p>

        </section>

        <!-- Desenho da caixinha: salvar a imagem da Júlia em imagens/caixa.png -->
        <div class="coluna-imagem">
            <img src="../imagens/caixa.png" alt="" onerror="this.parentElement.remove()">
        </div>

    </main>

    <footer>
        REWEB E CORISCO'S PAPER
    </footer>

    <script src="Js/login.js"></script>

</body>

</html>
