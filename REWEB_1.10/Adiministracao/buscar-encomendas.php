<?php
// Busca no banco tudo o que a tela da administração (painel.php) precisa mostrar.
// Quem usa: require('buscar-encomendas.php'); e depois percorre as listas abaixo.
//   $linhas     -> uma linha da tabela para cada ITEM de pedido
//   $historico  -> as últimas coisas que aconteceram (caixa amarela)
//   $clientes   -> lista de clientes (menu "Clientes" da lateral)
//   $produtos   -> lista de produtos (menu "Produtos" da lateral)
//   $topicos    -> categorias (sugestões no formulário de produto)
//   $meses      -> meses que têm pedidos (menu "Painel" da lateral)
require('conexao.php');
require('../Includes/funcoes.php');

// Cada item de pedido vira uma linha da tabela (o JOIN junta o item com os dados do pedido)
$sql = "SELECT i.id AS id_item, i.id_pedido, i.produto, i.valor, i.quantidade, i.tema,
               i.tamanho, i.idade, i.nome_escrito, i.cor, i.observacoes AS obs_item,
               i.precisa_arte, i.arte,
               p.cliente, p.telefone, p.email, p.endereco, p.forma_entrega, p.criado_por,
               p.id_usuario, p.data_pedido, p.prazo, p.pago, p.pronto, p.buscou,
               p.observacoes
        FROM itens_pedido i
        JOIN pedidos p ON p.id = i.id_pedido
        ORDER BY p.data_pedido DESC, i.id DESC";
$resultado = $con->query($sql);

$linhas = [];
while ($linha = mysqli_fetch_assoc($resultado)) {

    // O total da linha é o valor vezes a quantidade
    $linha['total'] = $linha['valor'] * $linha['quantidade'];

    // Essas informações ajudam os botões de filtro (Aguarda PGTO, Prontos...) da tela
    $linha['f_pagou'] = $linha['pago'] == 1 ? 'sim' : 'nao';
    $linha['f_pronto'] = $linha['pronto'] == 1 ? 'sim' : 'nao';
    $linha['f_arte'] = ($linha['precisa_arte'] == 1 && $linha['arte'] == '') ? 'sim' : 'nao';

    // Prazo próximo: o prazo é daqui a 15 dias ou menos
    $linha['f_prazo'] = ($linha['prazo'] != '' && strtotime($linha['prazo']) <= strtotime('+15 days')) ? 'sim' : 'nao';

    // Mês do pedido (exemplo: 2026-10), usado no menu "Painel" da lateral
    $linha['f_mes'] = date('Y-m', strtotime($linha['data_pedido']));

    $linhas[] = $linha;
}

// As últimas coisas que aconteceram (caixa amarela "Histórico")
$resultado = $con->query("SELECT descricao, data_hora FROM historico ORDER BY data_hora DESC, id DESC LIMIT 10");
$historico = [];
while ($linha = mysqli_fetch_assoc($resultado)) {
    $historico[] = $linha;
}

// Clientes que já fizeram pedido (um por nome) e quantos pedidos cada um tem
$resultado = $con->query("SELECT cliente, COUNT(*) AS total FROM pedidos GROUP BY cliente ORDER BY cliente");
$clientes = [];
while ($linha = mysqli_fetch_assoc($resultado)) {
    $clientes[] = $linha;
}

// Produtos que estão à venda
$resultado = $con->query("SELECT p.id, p.nome, p.preco, p.tipo, t.nome AS topico
                          FROM produtos p
                          LEFT JOIN topicos t ON t.id = p.id_topico
                          WHERE p.ativo = 1
                          ORDER BY p.nome");
$produtos = [];
while ($linha = mysqli_fetch_assoc($resultado)) {
    $produtos[] = $linha;
}

// Categorias que já existem (o formulário de produto sugere esses nomes)
$resultado = $con->query("SELECT nome FROM topicos ORDER BY nome");
$topicos = [];
while ($linha = mysqli_fetch_assoc($resultado)) {
    $topicos[] = $linha['nome'];
}

// Meses que têm pedido, do mais novo para o mais antigo, com a quantidade de pedidos de cada um.
// O mês de hoje sempre aparece, mesmo sem pedidos ainda.
$meses = [date('Y-m') => 0];
$vistos = [];
foreach ($linhas as $linha) {
    if (!isset($vistos[$linha['id_pedido']])) {
        $vistos[$linha['id_pedido']] = true;
        $mes = $linha['f_mes'];
        $meses[$mes] = isset($meses[$mes]) ? $meses[$mes] + 1 : 1;
    }
}
krsort($meses);
