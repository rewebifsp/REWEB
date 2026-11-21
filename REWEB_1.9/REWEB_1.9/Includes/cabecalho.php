<?php
// Topo que se repete nas telas de dentro do site (produto, carrinho, favoritos...)
// Quem chama esse arquivo precisa criar a variável $raiz antes, por exemplo:
//   $raiz = '../';   (página dentro de uma pasta)
// Assim os links e as imagens funcionam não importa de qual pasta o topo foi chamado.

// Inicia a sessão para saber se tem alguém logado
@session_start();
$usuarioLogado = isset($_SESSION['usuario']) && !empty($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

// Quantos itens tem no carrinho (o carrinho fica guardado na sessão)
$qtdCarrinho = isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : 0;

// Se tem item no carrinho, o ícone ganha a classe "com-itens" (círculo laranja no CSS)
$classeCarrinho = $qtdCarrinho > 0 ? ' com-itens' : '';

// Texto que leitores de tela falam ao chegar no carrinho (exemplo: "Carrinho: 3 itens")
$textoCarrinho = 'Carrinho';
if ($qtdCarrinho > 0) {
    $textoCarrinho = 'Carrinho: ' . $qtdCarrinho . ($qtdCarrinho == 1 ? ' item' : ' itens');
}
?>

<header class="topo-interno">

    <!-- Logo: clicando volta para a tela inicial -->
    <a class="logo-link" href="<?php echo $raiz; ?>index.php" aria-label="Voltar para a tela inicial">
        <img class="logo-img" src="<?php echo $raiz; ?>imagens/logo.png" alt="Corisco's Paper - Papelaria Criativa">
    </a>

    <nav class="links-topo">
        <a href="<?php echo $raiz; ?>index.php#produtos">SUBLIMAÇÃO</a>
        <span>|</span>
        <a href="<?php echo $raiz; ?>index.php#produtos">PAPELARIA</a>
        <span>|</span>
        <a href="<?php echo $raiz; ?>index.php#papel-arroz">PAPEL ARROZ</a>
    </nav>

    <div class="ajuda">
        <span>Precisa de ajuda?</span>
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
    </div>

    <!-- Pesquisa, favoritos e carrinho -->
    <div class="meio-topo">

        <form class="busca-interna" action="#" method="get">
            <img class="mascote" src="<?php echo $raiz; ?>imagens/pesquisa.png" alt="" onerror="this.remove()">
            <input class="busca" type="search" name="pesquisa" placeholder="PESQUISA" aria-label="Pesquisar produtos">
        </form>

        <!-- O carrinho é de todo mundo. Os favoritos pedem login (a própria página leva para o login) -->
        <!-- Esses dois links levam para telas que ainda vão ser feitas -->
        <a class="atalho" href="<?php echo $raiz; ?>Favoritos/favoritos.php" aria-label="Favoritos">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8z" />
            </svg>
        </a>

        <a class="atalho atalho-carrinho<?php echo $classeCarrinho; ?>" href="<?php echo $raiz; ?>Carrinho/carrinho.php" aria-label="<?php echo $textoCarrinho; ?>">
            <img src="<?php echo $raiz; ?>imagens/carrinho.png" alt="">
            <!-- Número de itens diferentes: só aparece quando o carrinho não está vazio -->
            <?php if ($qtdCarrinho > 0) { ?>
                <span class="contagem"><?php echo $qtdCarrinho; ?></span>
            <?php } ?>
        </a>

    </div>

    <!-- Login: muda conforme a pessoa estiver logada ou não (igual na tela inicial) -->
    <a class="login conta-topo" href="<?php echo $usuarioLogado != null ? $raiz . 'Cadastro/deslogar.php' : $raiz . 'Cadastro/login.php'; ?>">

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
