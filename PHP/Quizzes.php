<?php
include_once('config.php');

/* ===============================
   CATEGORIAS
================================ */
$categorias = [
    'Games' => 'Games',
    'Anime' => 'Anime',
    'Series' => 'Séries',
    'Filmes' => 'Filmes',
    'Livros' => 'Livros',
    'Variados' => 'Variados'
];

/* ===============================
   TIPO DE QUIZ
================================ */
$tipo = $_GET['tipo'] ?? 'normal';

switch ($tipo) {
    case 'personalidade':
        $tabela = 'personalidade';
        $paginaQuiz = 'quiz_personalidade.php';
        break;
    case 'list':
        $tabela = 'quizzes_list';
        $paginaQuiz = 'quiz_list.php';
        break;
    default:
        $tabela = 'quizzes';
        $paginaQuiz = 'quiz.php';
        break;
}

if ($tipo === 'personalidade') {
    $linkEditor = 'criadorPersonalidade.php';
} else {
    $linkEditor = 'criadorQuiz.php';
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>DnNerds - Quizzes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="../Styles/Noticias.css?v=28">
    <link rel="stylesheet" href="../Styles/Header.css?v=32">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bitcount+Grid+Single&family=Anonymous+Pro&family=Poppins&display=swap"
        rel="stylesheet">
</head>

<body>

    <header>
        <nav class="navbar">
            <h1 class="title">
                DnNerds <img src="../Imagens/favicon.png?v=2" alt="">
            </h1>

            <ul>
                <li><a href="../PHP/Noticias.php">Notícias</a></li>
                <li><a href="nerdlists.php">NerdList</a></li>
                <li><a href="../PHP/Quizzes.php" class="ativo">Quizzes</a></li>
                <li><a href="<?= $linkEditor ?>">Criar</a></li>
            </ul>

            <form method="GET" action="Noticias.php" class="search-container">
                <button type="submit" class="btn-lupa">🔍</button>
                <input type="search" name="q" placeholder="Buscar..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </form>

            <button class="btn-navbar">
                <a href="../PHP/FazerLogin.php">Fazer Login</a>
            </button>
        </nav>
    </header>

    <main>

        <!-- TIPOS DE QUIZ -->
        <div id="tipoQuiz">
            <a href="Quizzes.php?tipo=normal" class="tipo-link <?= $tipo === 'normal' ? 'ativo' : '' ?>">
                Quizzes
            </a>

            <a href="Quizzes.php?tipo=personalidade" class="tipo-link <?= $tipo === 'personalidade' ? 'ativo' : '' ?>">
                Quiz de Personalidade
            </a>
        </div>

        <?php
        /* ===============================
           BUSCA
        ================================ */
        if (!empty($_GET['q'])) {
            $termo = $conexao->real_escape_string(trim($_GET['q']));
            echo "<h2 class='classe'>Resultados para: " . htmlspecialchars($termo) . "</h2>";

            $sqlBusca = "
            SELECT * FROM quizzes
            WHERE titulo LIKE '%$termo%'
               OR descricao LIKE '%$termo%'
            ORDER BY id DESC
        ";

            $resultBusca = $conexao->query($sqlBusca);

            if ($resultBusca && $resultBusca->num_rows > 0) {
                echo '<div class="carousel-container">
                    <button class="carousel-btn left">&#10094;</button>
                    <div class="carousel">';

                while ($quiz = $resultBusca->fetch_assoc()) {
                    ?>
                    <a href="<?= $paginaQuiz ?>?id=<?= $quiz['id'] ?>">
                        <div id="caixa">
                            <img src="<?= htmlspecialchars($quiz['imagem'] ?: 'quizdefault.jpg') ?>">">
                            <p><b><?= htmlspecialchars($quiz['titulo']) ?></b></p>
                            <small><?= htmlspecialchars(substr($quiz['descricao'], 0, 80)) ?>...</small>
                        </div>
                    </a>
                    <?php
                }

                echo '</div><button class="carousel-btn right">&#10095;</button></div>';
            } else {
                echo "<p>Nenhum quiz encontrado.</p>";
            }
            exit;
        }
        ?>

        <!-- LISTAGEM POR CATEGORIA -->
        <section id="quizzes">
            <?php foreach ($categorias as $key => $titulo): ?>

                <div class="classe">
                    <h2><?= $titulo ?></h2>
                </div>

                <?php
                $sql = "SELECT * FROM $tabela WHERE categoria='$key' ORDER BY id DESC LIMIT 10";
                $result = $conexao->query($sql);
                ?>

                <div class="carousel-container">
                    <button class="carousel-btn left">&#10094;</button>

                    <div class="carousel">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($quiz = $result->fetch_assoc()): ?>
                                <a href="<?= $paginaQuiz ?>?id=<?= $quiz['id'] ?>">
                                    <div id="caixa">
                                        <img src="<?= htmlspecialchars($quiz['imagem'] ?: 'quizdefault.jpg') ?>">
                                        <p><b><?= htmlspecialchars($quiz['titulo']) ?></b></p>
                                        <small><?= htmlspecialchars(substr($quiz['descricao'], 0, 80)) ?>...</small>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>Nenhum quiz nesta categoria.</p>
                        <?php endif; ?>
                    </div>

                    <button class="carousel-btn right">&#10095;</button>
                </div>

            <?php endforeach; ?>
        </section>

    </main>

    <!-- SCRIPT -->
    <script>
        document.querySelectorAll(".carousel-container").forEach(container => {
            const carousel = container.querySelector(".carousel");
            container.querySelector(".left").onclick = () =>
                carousel.scrollBy({ left: -250, behavior: "smooth" });

            container.querySelector(".right").onclick = () =>
                carousel.scrollBy({ left: 250, behavior: "smooth" });
        });
    </script>

    <footer class="footer">
        <div class="footer-container">
            <p>2025 DnNerds — Renato Matos, Natalia Macedo, Arthur Simões, Diego Toscano, Yuri Reis, Enzo Niglia </p>
            <div class="footer-links"> <a href="https://www.youtube.com/" target="_blank" title="YouTube"><img
                        src="../Imagens/youtube.png" alt="YouTube"></a> <a href="https://www.instagram.com/DnNerds"
                    target="_blank" title="Instagram"><img src="../Imagens/instagram.jpeg" alt="Instagram"></a> <a
                    href="https://www.facebook.com/" target="_blank" title="Facebook"><img src="../Imagens/facebook.png"
                        alt="Facebook"></a> <a href="https://www.tiktok.com/" target="_blank" title="TikTok"><img
                        src="../Imagens/tiktok.jpeg" alt="TikTok"></a> </div>
        </div>
    </footer>

</body>

</html>