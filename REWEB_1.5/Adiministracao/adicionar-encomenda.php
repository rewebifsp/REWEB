<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserir encomenda | Corisco's Paper</title>
    <link rel="stylesheet" href="Css/admin-base.css">
    <link rel="stylesheet" href="Css/adicionar-encomenda.css">
    <script src="Js/adicionar-encomenda.js" defer></script>
</head>
<body>
    <header class="cabecalho-admin">
        <a href="painel.php" aria-label="Voltar ao painel"><img src="../imagens/logo.png" alt="Corisco's Paper"></a>
        <h1>INSERIR ENCOMENDA</h1>
    </header>
    <main class="conteudo-admin">
        <form class="form-encomenda" id="formEncomenda">
            <div class="campos-encomenda">
                <label>Cliente<input name="cliente" type="text" placeholder="Nome do cliente" required></label>

                <label>Produto<input name="produto" type="text" placeholder="Produto" required></label>

                <label>Valor<input name="valor" type="number" min="0" step="0.01" placeholder="0,00" required></label>

                <label>Quantidade<input name="quantidade" type="number" min="1" placeholder="1" required></label>

                <label>Data<input name="data" type="date" required></label>

                <label>Tema<input name="tema" type="text" placeholder="Tema da encomenda"></label>

                <label>Prazo<input name="prazo" type="date"></label>

                <label>Telefone<input name="telefone" type="tel" placeholder="(00) 00000-0000"></label>

                <label>Endereço<input name="endereco" type="text" placeholder="Endereço do cliente"></label>

                <label>E-mail<input name="email" type="email" placeholder="email@exemplo.com"></label>

                <label class="campo-largo">Observações
                    <textarea name="observacoes" rows="3" placeholder="Detalhes da encomenda"></textarea>
                </label>

                <label class="marcacao"><input name="pagou" type="checkbox"> Já pagou</label>

                <label class="marcacao"><input name="buscou" type="checkbox"> Já buscou</label>

                <label class="campo-largo">Foto da encomenda<input name="foto" type="file" accept="image/*"></label>
            </div>

            <div class="acoes-formulario">
                <a href="painel.php">VOLTAR</a>
                <button type="submit">ENVIAR</button>
            </div>
        </form>
    </main>
</body>
</html>
