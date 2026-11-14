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
        $q = "INSERT INTO login (nome, email, senha) VALUES ('$telefone','$email' ,'$senha')";
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
    }
}
?>


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
}
?>




<?php


# n]ao vai ser usadao mais pelo menos dessa forma, só não foi deletado para deixar os comandos de php que possivel mente vou usar

require('conexao.php');

$sql = "SELECT * FROM lugares"; #no caso criar varios assim para cada tabela que presisas carregar dados no site
$sql1 = "SELECT * FROM tds";
$sql2 = "SELECT * FROM sdasd";
$sql3 = "SELECT * FROM bt";
$sql4 = "SELECT * FROM chapeu";

$resultado = mysqli_query($con, $sql);
$resultadotds = mysqli_query($con, $sql1); #php faz a coneção com o banco e guarda o resultado
$resultadoasdas = mysqli_query($con, $sql2);
$resultadobt = mysqli_query($con, $sql3);
$resultadochapeu = mysqli_query($con, $sql4); #nomes provisorios foi criado só para testar a logica

$dados = [];
$tds = [];
$asad = [];
$bt = [];
$chapeu = [];

while ($linha = mysqli_fetch_assoc($resultado)) { #string com cada resultado, lembra do exercicio do carcapio mesmo principio
    $dados[] = $linha;
}

while ($linha = mysqli_fetch_assoc($resultadotds)) {
    $tds[] = $linha;
}

while ($linha = mysqli_fetch_assoc($resultadoasdas)) {
    $asad[] = $linha;
}

while ($linha = mysqli_fetch_assoc($resultadobt)) {
    $bt[] = $linha;
}

while ($linha = mysqli_fetch_assoc($resultadochapeu)) {
    $chapeu[] = $linha;
}

$resposta = [
    "lugares" => $dados,
    "tds" => $tds,
    "asad" => $asad,
    "bt" => $bt,
    "chapeu" => $chapeu
];

echo json_encode($resposta);


/* usar no js quando for carregar algo no site (obs: não é para dar um control c e depois um V, é mais a ideia de como se vai usar)
 
fetch("buscar.php")
    .then(resposta => resposta.json())
    .then(dados => {
        console.log(dados.lugares);
        console.log(dados.tds);
        console.log(dados.asad);
        console.log(dados.bt);
        console.log(dados.chapeu);
    });

    */
?>