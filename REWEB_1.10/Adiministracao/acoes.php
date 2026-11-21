<?php
// Todas as ações da tela de administração (painel.php) passam por aqui:
//   nova_encomenda    -> anota uma encomenda nova
//   salvar_encomenda  -> altera uma encomenda que já existe
//   excluir_encomenda -> apaga uma encomenda inteira (com todos os itens dela)
//   novo_produto      -> cadastra um produto novo na loja
// O painel.php chama este arquivo com fetch() e recebe a resposta em JSON ({ok: true} ou {ok: false, erro: "..."}).

require('so-adm.php'); // só ADM entra (quem não for volta para a tela inicial)
require('conexao.php');
require('../Includes/funcoes.php');

header('Content-Type: application/json; charset=utf-8');

// Termina a execução devolvendo uma resposta em JSON
function responder($ok, $erro = '')
{
    echo json_encode(['ok' => $ok, 'erro' => $erro]);
    exit;
}

// Só aceita envio de formulário (POST)
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    responder(false, 'Método não permitido.');
}

// Proteção contra formulários falsos vindos de outros sites: o token precisa ser o mesmo da sessão
$token = isset($_POST['csrf']) ? $_POST['csrf'] : '';
if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
    http_response_code(403);
    responder(false, 'Sessão expirada. Recarregue a página e tente de novo.');
}

$acao = isset($_POST['acao']) ? $_POST['acao'] : '';

// Pega um campo de texto do formulário já sem espaços nas pontas
function texto($campo)
{
    return isset($_POST[$campo]) ? trim($_POST[$campo]) : '';
}

// Anota uma frase no histórico (a caixa amarela do painel)
function anotarHistorico($con, $descricao, $idPedido)
{
    $stmt = $con->prepare("INSERT INTO historico (descricao, id_pedido) VALUES (?, ?)");
    $stmt->bind_param("si", $descricao, $idPedido);
    $stmt->execute();
}

// ------------------------------------------------------------
// NOVA ENCOMENDA (o ADM anota na mão)
// ------------------------------------------------------------
if ($acao == 'nova_encomenda') {

    $cliente = texto('cliente');
    $produto = texto('produto');
    $valor = (float) texto('valor');
    $quantidade = (int) texto('quantidade');
    $tema = texto('tema');
    $telefone = texto('telefone');
    $endereco = texto('endereco');
    $email = texto('email');
    $observacoes = texto('observacoes');

    if ($cliente == '' || $produto == '') {
        responder(false, 'Preencha o cliente e o produto.');
    }
    if ($quantidade < 1) {
        $quantidade = 1;
    }

    // Se a data não foi preenchida, usa a data de hoje
    $data = texto('data') != '' ? texto('data') : date('Y-m-d');
    $data = $data . ' 00:00:00';

    // O prazo é opcional (fica vazio no banco quando não preenchido)
    $prazo = texto('prazo') != '' ? texto('prazo') : null;

    // Caixinhas de marcar: marcada = 1, desmarcada = 0
    $pagou = isset($_POST['pagou']) ? 1 : 0;
    $buscou = isset($_POST['buscou']) ? 1 : 0;

    // Se tem endereço é entrega, se não tem é retirada
    $forma = $endereco != '' ? 'entrega' : 'retirada';

    // Guarda a foto da encomenda (se tiver). Se foi enviada, ela já conta como arte recebida
    $arte = salvarImagem('foto', 'encomenda_');

    // Salva o pedido (criado_por = 'adm' porque foi a administração que anotou)
    $stmt = $con->prepare("INSERT INTO pedidos (criado_por, cliente, telefone, email, forma_entrega, endereco, data_pedido, prazo, pago, buscou, observacoes) VALUES ('adm', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssiis", $cliente, $telefone, $email, $forma, $endereco, $data, $prazo, $pagou, $buscou, $observacoes);
    $stmt->execute();
    $idPedido = $con->insert_id;

    // Salva o produto dentro do pedido
    $precisaArte = isset($_POST['precisa_arte']) ? 1 : 0;
    $stmt = $con->prepare("INSERT INTO itens_pedido (id_pedido, produto, valor, quantidade, tema, arte, precisa_arte) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdissi", $idPedido, $produto, $valor, $quantidade, $tema, $arte, $precisaArte);
    $stmt->execute();

    anotarHistorico($con, 'Administração adicionou encomenda de ' . $cliente, $idPedido);
    responder(true);
}

// ------------------------------------------------------------
// SALVAR ENCOMENDA (alterar uma que já existe)
// ------------------------------------------------------------
if ($acao == 'salvar_encomenda') {

    $idPedido = (int) texto('id_pedido');
    $idItem = (int) texto('id_item');

    $cliente = texto('cliente');
    $produto = texto('produto');
    if ($cliente == '' || $produto == '') {
        responder(false, 'Preencha o cliente e o produto.');
    }

    // Confere se o pedido e o item existem e são um do outro
    $stmt = $con->prepare("SELECT p.data_pedido, i.arte FROM pedidos p JOIN itens_pedido i ON i.id_pedido = p.id WHERE p.id = ? AND i.id = ?");
    $stmt->bind_param("ii", $idPedido, $idItem);
    $stmt->execute();
    $atual = mysqli_fetch_assoc($stmt->get_result());
    if (!$atual) {
        responder(false, 'Encomenda não encontrada.');
    }

    $telefone = texto('telefone');
    $email = texto('email');
    $endereco = texto('endereco');
    $observacoes = texto('observacoes');
    $forma = $endereco != '' ? 'entrega' : 'retirada';
    $prazo = texto('prazo') != '' ? texto('prazo') : null;
    $pagou = isset($_POST['pagou']) ? 1 : 0;
    $pronto = isset($_POST['pronto']) ? 1 : 0;
    $buscou = isset($_POST['buscou']) ? 1 : 0;

    // A data só muda se a pessoa mexeu nela (assim a hora do pedido não se perde)
    $data = $atual['data_pedido'];
    if (texto('data') != '' && texto('data') != substr($atual['data_pedido'], 0, 10)) {
        $data = texto('data') . ' 00:00:00';
    }

    $stmt = $con->prepare("UPDATE pedidos SET cliente = ?, telefone = ?, email = ?, forma_entrega = ?, endereco = ?, data_pedido = ?, prazo = ?, pago = ?, pronto = ?, buscou = ?, observacoes = ? WHERE id = ?");
    $stmt->bind_param("sssssssiiisi", $cliente, $telefone, $email, $forma, $endereco, $data, $prazo, $pagou, $pronto, $buscou, $observacoes, $idPedido);
    $stmt->execute();

    // Se mandaram uma foto nova, ela troca a anterior. Se não, continua a que já estava
    $arte = salvarImagem('foto', 'encomenda_');
    if ($arte == '') {
        $arte = $atual['arte'];
    }

    $valor = (float) texto('valor');
    $quantidade = max(1, (int) texto('quantidade'));
    $tema = texto('tema');
    $precisaArte = isset($_POST['precisa_arte']) ? 1 : 0;

    $stmt = $con->prepare("UPDATE itens_pedido SET produto = ?, valor = ?, quantidade = ?, tema = ?, arte = ?, precisa_arte = ? WHERE id = ?");
    $stmt->bind_param("sdissii", $produto, $valor, $quantidade, $tema, $arte, $precisaArte, $idItem);
    $stmt->execute();

    anotarHistorico($con, 'Administração alterou a encomenda de ' . $cliente, $idPedido);
    responder(true);
}

// ------------------------------------------------------------
// EXCLUIR ENCOMENDA (o pedido inteiro; os itens saem junto por causa do ON DELETE CASCADE)
// ------------------------------------------------------------
if ($acao == 'excluir_encomenda') {

    $idPedido = (int) texto('id_pedido');

    $stmt = $con->prepare("SELECT cliente FROM pedidos WHERE id = ?");
    $stmt->bind_param("i", $idPedido);
    $stmt->execute();
    $pedido = mysqli_fetch_assoc($stmt->get_result());
    if (!$pedido) {
        responder(false, 'Encomenda não encontrada.');
    }

    $stmt = $con->prepare("DELETE FROM pedidos WHERE id = ?");
    $stmt->bind_param("i", $idPedido);
    $stmt->execute();

    // O pedido já não existe, então o histórico fica sem número de pedido (id_pedido vazio)
    $descricao = 'Administração excluiu a encomenda de ' . $pedido['cliente'];
    $stmt = $con->prepare("INSERT INTO historico (descricao) VALUES (?)");
    $stmt->bind_param("s", $descricao);
    $stmt->execute();

    responder(true);
}

// ------------------------------------------------------------
// NOVO PRODUTO (aparece na loja)
// ------------------------------------------------------------
if ($acao == 'novo_produto') {

    $nome = texto('produto');
    $tema = texto('tema');
    $preco = (float) texto('valor');
    $especificacoes = texto('especificacoes');
    $topico = texto('topico');
    $tipo = texto('tipo') == 'pronta_entrega' ? 'pronta_entrega' : 'encomenda';
    $cores = texto('cores');

    if ($nome == '') {
        responder(false, 'Preencha o nome do produto.');
    }

    // O tópico é a categoria. Se já existir usa o que existe, se não existir cria
    $idTopico = null;
    if ($topico != '') {
        $stmt = $con->prepare("SELECT id FROM topicos WHERE nome = ?");
        $stmt->bind_param("s", $topico);
        $stmt->execute();
        $achou = mysqli_fetch_assoc($stmt->get_result());

        if ($achou) {
            $idTopico = $achou['id'];
        } else {
            $stmt = $con->prepare("INSERT INTO topicos (nome) VALUES (?)");
            $stmt->bind_param("s", $topico);
            $stmt->execute();
            $idTopico = $con->insert_id;
        }
    }

    // Guarda a foto do produto (se tiver)
    $foto = salvarImagem('imagem', 'produto_');

    $stmt = $con->prepare("INSERT INTO produtos (nome, tema, preco, especificacoes, id_topico, tipo, cores_disponiveis, foto1) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsisss", $nome, $tema, $preco, $especificacoes, $idTopico, $tipo, $cores, $foto);
    $stmt->execute();

    anotarHistorico($con, 'Administração cadastrou o produto ' . $nome, null);
    responder(true);
}

responder(false, 'Ação desconhecida.');
