<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserir produto | Corisco's Paper</title>
    <link rel="stylesheet" href="Css/admin-base.css">
    <link rel="stylesheet" href="Css/inserir-produto.css">
    <script src="Js/inserir-produto.js" defer></script>
</head>
<body>
    <header class="cabecalho-admin">
        <a href="painel.php" aria-label="Voltar ao painel"><img src="../imagens/logo.png" alt="Corisco's Paper"></a>
        <h1>INSERIR PRODUTO</h1>
    </header>
    <main class="pagina-produto">
        <form id="formProduto" class="form-produto">
            <section class="campos-produto" aria-label="Dados do produto">

                <label>
                    Produto<input id="nomeProduto" name="produto" type="text" placeholder="Nome do produto" required>
                </label>
                <label>
                    Tema<input name="tema" type="text" placeholder="Tema">
                </label>
                <label>
                    Valor<input id="valorProduto" name="valor" type="number" min="0" step="0.01" placeholder="0,00" required>
                </label>
                <label>
                    Especificações<textarea name="especificacoes" rows="3" placeholder="Detalhes do produto"></textarea>
                </label>
                <label>
                    Tópico<input name="topico" type="text" placeholder="Categoria">
                </label>
                <label>
                    Tipo<input name="tipo" type="text" placeholder="Tipo do produto">
                </label>
                <label>
                    Cores disponíveis<input name="cores" type="text" placeholder="Ex.: azul, rosa, amarelo">
                </label>
                <label>
                    Foto do produto<input id="imagemProduto" name="imagem" type="file" accept="image/*">
                </label>

            </section>
            
            <article class="cartao-produto">
                <div class="foto-produto">
                    <img id="previaImagem" src="../imagens/caixa.png" alt="Prévia do produto">
                </div>

                <h2 id="previaNome">NOME DO PRODUTO</h2>

                <p id="previaValor">R$ 0,00</p>

                <div class="especificacoes-previa" id="previaEspecificacoes">ESPECIFICAÇÕES</div>

            </article>
            
            <div class="acoes-formulario">
                <a href="painel.php">VOLTAR</a>
                <button type="submit">ENVIAR</button>
            </div>
        </form>
    </main>
</body>
</html>
