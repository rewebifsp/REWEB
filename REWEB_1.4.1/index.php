<?php
// Conecta com o banco de dados
require('Adiministracao/conexao.php'); // Certifique-se de que o arquivo de conexão está correto

// Inicia a sessão
@session_start();

// Verifica se o usuário está logado
$usuarioLogado = isset($_SESSION['usuario']) && !empty($_SESSION['usuario']) ? $_SESSION['usuario'] : null;



?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corisco's Paper</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header class="cabecalho cabecalho-inicio">

        <!-- Redes sociais. TROCAR os dois links pelos reais da Corisco's Paper -->
        <!-- Eu pedro, ja realizei as trocas dos links pelos links reais, e adicionei o carrinho e o coração de favoritos -->
        <div class="redes">
            <a class="rede-insta" href="https://www.instagram.com/juliacoriscopapelaria?rpxt=cHZmZG5xanV1enc0" target="_blank" rel="noopener"
                aria-label="Instagram da Corisco's Paper">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="5" />
                    <circle cx="12" cy="12" r="4" />
                    <circle cx="17.2" cy="6.8" r=".6" />
                </svg>
            </a>
            <a class="rede-zap" href="https://wa.me/5519988565666" target="_blank" rel="noopener"
                aria-label="WhatsApp da Corisco's Paper">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 21l1.6-4.8A9 9 0 1 1 8 19.6L3 21z" />
                    <path d="M9 8.5c0 3.5 3 6.5 6.5 6.5l1-1.5-2-1-1 .8c-1-.4-2-1.4-2.4-2.4l.8-1-1-2z" />
                </svg>
            </a>
        </div>
        <div class="atalhos-loja">
            <button type="button" aria-label="Favoritos">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8z"/>
                </svg>
            </button>

            <button type="button" aria-label="Carrinho">
                <img src="imagens/carrinho.png" alt="">
            </button>
        </div>

      
        <!-- Logo (caixinha + nome). Aqui na tela inicial não precisa ser link.
             Nas outras telas, colocar <a href="index.html"> em volta da imagem para voltar ao início. -->
        <div class="logo">
            <img class="logo-img" src="imagens/logo.png" alt="Corisco's Paper - Papelaria Criativa">
        </div>

        <!-- Login: o círculo escuro depois pode virar a foto/ícone do usuário -->
        <a class="login" href="<?php echo $usuarioLogado != null ? 'Cadastro/deslogar.php' : 'Cadastro/login.php'; ?>">

            <span class="login-icone"></span>

            <span class="login-texto">
                <?php if ($usuarioLogado != null) { ?>
                    LOGADO
                    <small>Sair da conta</small>
                <?php } else { ?>
                    LOGIN
                    <small>ou cadastre-se</small>
                <?php } ?>
            </span>

        </a>

    </header>

    <main>

        <!-- Pesquisa e menu -->
        <section class="area-busca">

            <form class="form-busca" action="#" method="get">
                <!-- Bonequinho da pesquisa: salvar a imagem em imagens/pesquisa.png -->
                <img class="mascote" src="imagens/pesquisa.png" alt="" onerror="this.remove()">
                <input class="busca" type="search" name="pesquisa"
                    placeholder="PESQUISA" aria-label="Pesquisar produtos">
            </form>

            <!-- Atualizado para essa navegção, side bar deixou de existir -->
            <nav class="navegacao-categorias" aria-label="Categorias">
                <a href="#inicio">GERAL</a>
                <a href="#produtos">LOUÇAS</a>
                <a href="#produtos">TÊNTIL</a>
                <a href="#produtos">TOPPER</a>
                <a href="#papel-arroz">PAPEL ARROZ/AÇÚCAR</a>
                <a href="#produtos">BRINDES</a>
                <a href="#produtos">ENCADERNAÇÃO</a>
            </nav>

        </section>

        <!-- Banner -->
        <section class="banner" id="inicio"></section>

        <!-- Produtos pronta entrega -->
        <section class="secao-pronta-entrega">

            <h2>PRODUTOS PRONTA ENTREGA</h2>

            <!-- CARROSEL -->
            <div class="area-carrossel">

                <button class="seta" id="voltar" type="button">
                    &#10094;
                </button>

                <div class="carrossel" id="carrossel">

                    <a href="produto.html" class="produto-card">
                        <img src="imagens/carrosel.png" alt="Produto 1"> 
                    </a>

                    <a href="produto.html" class="produto-card">
                        <img src="imagens/produto2.png" alt="Produto 2">
                    </a>

                    <a href="produto.html" class="produto-card">
                        <img src="imagens/produto3.png" alt="Produto 3">
                    </a>

                    <a href="produto.html" class="produto-card">
                        <img src="imagens/produto4.png" alt="Produto 4">
                    </a>
                    <a href="produto.html" class="produto-card">
                        <img src="imagens/produto5.png" alt="Produto 5">
                    </a>

                </div>

                <button class="seta" id="avancar" type="button">
                    &#10095;
                </button>

            </div>

            <div class="indicadores" id="indicadores">
                <span class="ativo"></span>
                <span></span>
                <span></span>
                <span></span>
            </div>

        </section>

        <!-- Categorias -->
        <section class="secao-produtos" id="produtos">

            <h2>PRODUTOS</h2>

            <div class="categorias">

                <a href="#sublimacao" id="sublimacao">SUBLIMAÇÃO</a>
                <a href="#brindes">BRINDES</a>
                <a href="#papel-arroz" id="papel-arroz">PAPEL ARROZ</a>
                <a href="#encadernacao">ENCADERNAÇÃO</a>
                <a href="#topper">TOPPER</a>

            </div>

        </section>

        <!-- Coleções -->
        <section class="secao-colecoes" id="colecoes">

            <h2>COLEÇÕES</h2>

            <div class="colecoes">

                <a href="camisas.html">
                    <span class="circulo camisas"></span>
                    <span>CAMISAS</span>
                </a>

                <a href="kit-festas.html">
                    <span class="circulo kit-festas"></span>
                    <span>KITS FESTA</span>
                </a>

                <a href="presentes.html">
                    <span class="circulo presentes"></span>
                    <span>PRESENTES</span>
                </a>

                <a href="volta-aulas.html">
                    <span class="circulo volta-aulas"></span>
                    <span>VOLTA ÀS<br>AULAS</span>
                </a>

                <a href="datas-festivas.html">
                    <span class="circulo datas-festivas"></span>
                    <span>DATAS<br>FESTIVAS</span>
                </a>

            </div>

        </section>

    </main>

    <footer>
        REWEB E CORISCO'S PAPER
    </footer>

    <script src="script.js"></script>

</body>
</html>
