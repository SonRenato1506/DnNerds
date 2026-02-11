<?php
include_once("config.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$paginaAtual = basename($_SERVER['PHP_SELF']);

$paginasComBusca = ['Noticias.php', 'Quizzes.php', 'Copinhas.php', 'nerdlists.php'];

$temBusca = in_array($paginaAtual, $paginasComBusca);

$fotoUsuario = null;

if (isset($_SESSION['id'])) {

    $sqlFoto = "SELECT foto FROM usuarios WHERE id = ?";
    $stmtFoto = $conexao->prepare($sqlFoto);
    $stmtFoto->bind_param("i", $_SESSION['id']);
    $stmtFoto->execute();

    $resultFoto = $stmtFoto->get_result();
    $dadosFoto = $resultFoto->fetch_assoc();

    $fotoUsuario = $dadosFoto['foto'] ?? null;
}

?>

<style>
    /* ===================== */
    /* 🎨 Cores – Padrão IGN */
    :root {
        --vermelho1: #E60012;
        --vermelho2: #B8000E;
        --vermelho-hover: #FF1A1A;

        --texto: #ffffff;
        --texto-secundario: #ff4d4d;

        --preto: #0f0f0f;
        --preto-escuro: #000000;

        --cinza-escuro: #242525;
        /* mantém body */
        --cinza-medio: #4D524E;
        --cinza-claro: #cccccc;

        --borda-preta: #000000;
        --borda-cinza: #aaa;
        --branco: #ffffff;
    }

    /* ===================== */
    /* 🌐 Geral */
    * {
        margin: 0;
        padding: 0;
    }

    /* ===================== */
    /* 🧍 Corpo */
    body {
        background-color: var(--cinza-escuro);
        font-family: Arial, Helvetica, sans-serif;
    }

    /* ===================== */
    /* 📝 Textos do Header */
    header h1,
    header h2,
    header p,
    header a {
        color: var(--texto);
        text-shadow: 0 0 4px rgba(230, 0, 18, 0.6);
        text-transform: uppercase;
    }

    /* ===================== */
    /* 📋 Navbar */
    .navbar {
        display: grid;
        align-items: center;
        background-color: var(--preto);
        padding: 15px 30px;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 10;
        box-shadow: 0 0 35px var(--vermelho1);
        border-bottom: 3px solid var(--vermelho1);
        gap: 20px;
    }

    /* ✅ Layout com busca (4 partes) */
    .navbar.has-search {
        grid-template-columns: auto 1fr auto auto;
    }

    /* ✅ Layout sem busca (3 partes) */
    .navbar:not(.has-search) {
        grid-template-columns: auto 1fr auto;
    }


    .navbar ul {
        display: flex;
        justify-content: center;
        list-style: none;
        gap: 10px;
    }


    .navbar ul a {
        text-decoration: none;
        padding: 10px 18px;
        font-size: 14px;
        border-radius: 25px;
        transition: 0.3s;
    }

    /* Hover links */
    .navbar ul li a:hover {
        background-color: var(--vermelho1);
        color: var(--branco);
        border-radius: 30px;
        transition: background-color 0.3s, color 0.3s;
    }

    /* Link ativo */
    .navbar ul li a.ativo {
        background-color: var(--vermelho2);
        box-shadow: 0 0 10px var(--vermelho1);
        border-radius: 30px;
    }

    /* ===================== */
    /* 🔘 Botão Login */
    .btn-navbar {
        background-color: var(--vermelho1);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 14px;
        cursor: pointer;
        font-size: 12px;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-navbar a {
        color: var(--branco);
        text-shadow: none;
    }

    .btn-navbar:hover {
        background-color: var(--vermelho-hover);
    }

    /* ===================== */
    /* 🔍 Barra de Pesquisa */
    .search-container {
        display: flex;
        align-items: center;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        height: 38px;
    }


    /* Botão lupa */
    .btn-lupa {
        background-color: white;
        color: white;
        border: none;
        padding: 6px 10px;
        cursor: pointer;
    }

    /* Input */
    .search-container input {
        border: none;
        padding: 6px 10px;
        outline: none;
        width: 180px;
    }

    /* Foco */
    .search-container:focus-within {
        box-shadow: 0 0 10px var(--vermelho-hover);
    }

    /* ===================== */
    /* 🧠 Logo */
    .title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 28px;
        white-space: nowrap;
    }

    .title img {
        width: 32px;
        height: 32px;
    }

    /* ===================== */
    /* ⚙️ Rodapé */
    .footer {
        background: rgb(32, 32, 32);
        color: white;
        padding: 30px 20px;
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
        position: relative;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.6);
    }

    .footer::before {
        content: "";
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        height: 2px;
        background: linear-gradient(90deg,
                transparent,
                var(--vermelho1),
                transparent);
        opacity: 0.4;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    /* ===================== */
    /* 📱 Responsividade */
    @media (max-width: 1115px) {
        .title {
            display: none;
        }
    }

    @media (max-width: 850px) {
        .search-container {
            display: none;
        }
    }

    @media (max-width: 625px) {
        .btn-navbar {
            font-size: 8px;
            margin-right: 45px;
        }
    }

    @media (max-width: 540px) {
        .btn-navbar {
            display: none;
        }
    }

    @media (max-width: 440px) {
        .navbar a {
            padding: 6px;
        }
    }


    /* ===================== */
    /* ⚙️ Rodapé */
    .footer {
        background: rgb(32, 32, 32);
        color: white;
        padding: 30px 20px;
        text-align: center;
        font-family: "Anonymous Pro", monospace;
        position: relative;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.6);
    }

    /* Linha decorativa no topo */
    .footer::before {
        content: "";
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--texto), transparent);
        opacity: 0.4;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .footer p {
        font-size: 0.95rem;
        line-height: 1.5;
        opacity: 0.85;
    }

    /* Links / ícones */
    .footer-links {
        display: flex;
        gap: 18px;
        margin-top: 10px;
    }

    .footer-links a img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        /* background-color: var(--roxo1); */
        padding: 6px;
        transition: transform 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease;
    }

    /* Hover com glow */
    .footer-links a img:hover {
        transform: translateY(-4px) scale(1.1);
        /* box-shadow: 0 0 15px var(--roxo2); */
        filter: brightness(1.2);
    }

    /* Responsivo */
    @media (max-width: 600px) {
        .footer p {
            font-size: 0.85rem;
        }

        .footer-links a img {
            width: 36px;
            height: 36px;
        }
    }

    .user-area,
    .auth-buttons {
        display: flex;
        align-items: center;
        gap: 12px;
        white-space: nowrap;
    }

    .user-photo {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
        transition: 0.3s;
    }

    .user-photo:hover {
        transform: scale(1.1);
    }
</style>

<header>
    <nav class="navbar <?= $temBusca ? 'has-search' : '' ?>">

        <h2 class="title">
            DnNerds <img src="../Imagens/favicon.png" alt="">
        </h2>

        <ul>
            <li><a class="<?= $paginaAtual == 'Noticias.php' ? 'ativo' : '' ?>" href="Noticias.php">Notícias</a></li>
            <li><a class="<?= $paginaAtual == 'Quizzes.php' ? 'ativo' : '' ?>" href="Quizzes.php">Quizzes</a></li>
            <li><a class="<?= $paginaAtual == 'nerdlists.php' ? 'ativo' : '' ?>" href="nerdlists.php">NerdList</a></li>
            <li><a class="<?= $paginaAtual == 'Copinhas.php' ? 'ativo' : '' ?>" href="Copinhas.php">Copinhas</a></li>
        </ul>

        <?php if ($temBusca): ?>
            <form class="search-container" action="buscar.php" method="GET">
                <button class="btn-lupa">🔍</button>
                <input type="text" name="q" placeholder="Buscar..." required>
            </form>
        <?php endif; ?>


        <?php if (isset($_SESSION['id'])): ?>

            <!-- USUÁRIO LOGADO -->
            <div class="user-area">
                <a href="user.php">
                    <img src="<?= !empty($fotoUsuario) ? $fotoUsuario : '../Imagens/user.png' ?>" alt="Usuário"
                        class="user-photo">
                </a>

                <span><?php echo $_SESSION['nome']; ?></span>
            </div>

        <?php else: ?>

            <!-- NÃO LOGADO -->
            <div class="auth-buttons">
                <a href="FazerLogin.php" class="btn-navbar">Login</a>
                <a href="CriarConta.php" class="btn-navbar">Criar Conta</a>
            </div>

        <?php endif; ?>

    </nav>
</header>