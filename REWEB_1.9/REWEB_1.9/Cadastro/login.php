<?php
@session_start(); // Inicia a sessão (ela guarda o carrinho e quem está logado)

// Se a pessoa veio de uma página que exige login (ex.: finalizar a compra), avisa
if (isset($_SESSION['voltar_para']) && !isset($_POST['email'])) {
    $msg = 'Entre na sua conta para continuar.';
}

// Verifica se o formulário foi enviado com o usuário (e-mail ou telefone) e a senha
if (isset($_POST['email']) && isset($_POST['senha'])) {

    // O campo se chama "email", mas a pessoa pode digitar e-mail ou telefone
    $usuarioDigitado = trim($_POST['email']);
    $senha = trim($_POST['senha']); // Remove espaços extras da senha

    // Carrega a lista de administradores (e-mail e senha criptografada)
    require('../Adiministracao/adm.php');

    // 1) Primeiro confere se é uma conta de ADMINISTRADOR (essa não precisa do banco)
    $emailMinusculo = strtolower($usuarioDigitado);
    if (isset($administradores[$emailMinusculo]) && password_verify($senha, $administradores[$emailMinusculo])) {
        $_SESSION['usuario'] = ['id' => 0, 'email' => $emailMinusculo, 'tipo' => 'adm'];
        header('Location: ../Adiministracao/painel.php'); // ADM vai para o painel
        exit;
    }

    // 2) Se não for administrador, procura no banco como CLIENTE
    require('../Adiministracao/conexao.php');

    // Verifica a conexão com o banco de dados
    if (!$con) {
        die('Erro de conexão: ' . mysqli_connect_error());
    }

    // Deixa só os números, para comparar com o telefone que foi salvo no cadastro
    $telefone = preg_replace('/\D/', '', $usuarioDigitado);

    // Procura no banco alguém com esse e-mail ou com esse telefone
    // O prepare com ? protege contra SQL Injection
    $stmt = $con->prepare("SELECT * FROM login WHERE email = ? OR telefone = ?");
    $stmt->bind_param("ss", $usuarioDigitado, $telefone); // "ss" = duas strings
    $stmt->execute();
    $result = $stmt->get_result(); // Obtém o resultado da consulta

    // Verifica se encontrou um usuário
    if ($result && mysqli_num_rows($result) > 0) {
        $usuario = mysqli_fetch_assoc($result); // Obtém os dados do usuário encontrado

        // Compara a senha informada com a senha criptografada que está no banco
        if (password_verify($senha, $usuario['senha'])) {

            // Guarda só o id e o e-mail (a senha criptografada não precisa ficar na sessão)
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'email' => $usuario['email'],
                'tipo' => 'cliente'
            ];

            // Volta para a página que pediu o login. Se não tinha nenhuma, vai para o início
            $destino = 'index.php';
            if (isset($_SESSION['voltar_para'])) {
                $destino = $_SESSION['voltar_para'];
                unset($_SESSION['voltar_para']);
            }
            header('Location: ../' . $destino);
            exit; // Garante que o código não continue após o redirecionamento
        } else {
            // Se a senha estiver incorreta, guarda a mensagem de erro
            $msg = 'Senha incorreta!';
        }
    } else {
        // Se o usuário não for encontrado no banco de dados, guarda a mensagem de erro
        $msg = 'Usuário não encontrado!';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
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

            <!-- o formulário envia para a própria página, o PHP lá em cima faz o login -->
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
