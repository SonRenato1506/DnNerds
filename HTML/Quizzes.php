<?php
include_once('config.php');

// Categorias disponíveis
$categorias = [
    'Games' => 'Games',
    'Anime' => 'Anime',
    'Series' => 'Séries',
    'Filmes' => 'Filmes',
    'Livros' => 'Livros',
    'Variados' => 'Variados'
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DnNerds - Quizzes</title>
    <link rel="stylesheet" href="../Styles/Noticias.css?v=27">
    <link rel="stylesheet" href="../Styles/Header.css?v=31">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Grid+Single:wght@100..900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Anonymous+Pro:wght@400;700&family=Poppins:wght@300;600;800&display=swap"
        rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar">
            <h1 class="title">DnNerds <img src="../Imagens/favicon.png?v=2" alt=""></h1>
            <ul>
                <li><a href="../HTML/Noticias.php">Notícias</a></li>
                <li><a href="">NerdList</a></li>
                <li><a href="../HTML/Quizzes.php" class="ativo">Quizzes</a></li>
                <li><a href="">IA</a></li>
            </ul>
            <div class="search-container">
                <form method="GET" action="Quizzes.php">
                    <input type="search" name="q" id="search" placeholder="Buscar quiz..."
                        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>
            <button class="btn-navbar"><a href="../HTML/FazerLogin.php">Fazer Login</a></button>
        </nav>
    </header>

    <main>
        <div id="tipoQuiz">
            <a href="Quizzes.php?tipo=normal"
                class="tipo-link <?php echo (!isset($_GET['tipo']) || $_GET['tipo'] == 'normal') ? 'ativo' : ''; ?>">Quizzes</a>
            <a href="Quizzes.php?tipo=personalidade"
                class="tipo-link <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'personalidade') ? 'ativo' : ''; ?>">
                Quiz de Personalidade</a>
            <a href="Quizzes.php?tipo=list"
                class="tipo-link <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'list') ? 'ativo' : ''; ?>">ListQuiz</a>
        </div>

        <?php
        // Tipo padrão = normal
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'normal';

        // Define tabela dependendo do tipo
        switch ($tipo) {
            case 'personalidade':
                $tabela = 'personalidade';
                break;
            case 'list':
                $tabela = 'quizzes_list';
                break;
            default:
                $tabela = 'quizzes';
                break;
        }

        // Se o usuário fez uma busca
        if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
            $termo = $conexao->real_escape_string(trim($_GET['q']));
            echo '<h2 class="classe">Resultados para: ' . htmlspecialchars($termo) . '</h2>';

            $sqlBusca = "SELECT * FROM quizzes WHERE titulo LIKE '%$termo%' OR descricao LIKE '%$termo%' ORDER BY id DESC";
            $resultBusca = $conexao->query($sqlBusca);

            if ($resultBusca && $resultBusca->num_rows > 0) {
                echo '<div class="carousel-container">';
                echo '<button class="carousel-btn left" type="button">&#10094;</button>';
                echo '<div class="carousel">';
                while ($quiz = $resultBusca->fetch_assoc()) {
                    echo '
$a href="' . $paginaQuiz . '?id=' . $quiz['id'] . '">
                        <div id="caixa">
                            <img src="../Imagens/' . (!empty($quiz['imagem']) ? htmlspecialchars($quiz['imagem']) : 'quizdefault.jpg') . '" alt="Quiz">
                            <p><b>' . htmlspecialchars($quiz['titulo']) . '</b></p>
                            <small>' . htmlspecialchars(substr($quiz['descricao'], 0, 80)) . '...</small>
                        </div>
                    </a>';
                }
                echo '</div><button class="carousel-btn right" type="button">&#10095;</button></div>';
            } else {
                echo '<p>Nenhum quiz encontrado.</p>';
            }
            exit;
        }
        ?>

        <section id="quizzes">
            <?php
            foreach ($categorias as $categoriaKey => $categoriaTitulo) {
                echo '<div class="classe"><h2>' . $categoriaTitulo . '</h2></div>';

                $sql = "SELECT * FROM $tabela WHERE categoria='$categoriaKey' ORDER BY id DESC LIMIT 10";
                $result = $conexao->query($sql);
                $paginaQuiz = ($tipo == 'personalidade') ? 'quiz_personalidade.php' :
                    (($tipo == 'list') ? 'quiz_list.php' : 'quiz.php');

                echo '<div class="carousel-container">';
                echo '<button class="carousel-btn left" type="button">&#10094;</button>';
                echo '<div class="carousel">';
                if ($result && $result->num_rows > 0) {
                    while ($quiz = $result->fetch_assoc()) {

                        // Define página correta
                        $paginaQuiz = ($tipo == 'personalidade') ? 'quiz_personalidade.php' :
                            (($tipo == 'list') ? 'quiz_list.php' : 'quiz.php');

                        echo '
    <a href="' . $paginaQuiz . '?id=' . $quiz['id'] . '">
        <div id="caixa">
            <img src="' . (!empty($quiz['imagem']) ? htmlspecialchars($quiz['imagem']) : 'quizdefault.jpg') . '" alt="Quiz">
            <p><b>' . htmlspecialchars($quiz['titulo']) . '</b></p>
            <small>' . htmlspecialchars(substr($quiz['descricao'], 0, 80)) . '...</small>
        </div>
    </a>';
                    }

                } else {
                    echo '<p>Nenhum quiz disponível nesta categoria.</p>';
                }
                echo '</div>';
                echo '<button class="carousel-btn right" type="button">&#10095;</button>';
                echo '</div>';
            }
            ?>
        </section>
    </main>

    <script>
        // Script para rolar os carrosséis
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".carousel-container").forEach(container => {
                const carousel = container.querySelector(".carousel");
                const btnLeft = container.querySelector(".carousel-btn.left");
                const btnRight = container.querySelector(".carousel-btn.right");

                btnLeft.addEventListener("click", () => {
                    carousel.scrollBy({ left: -250, behavior: "smooth" });
                });

                btnRight.addEventListener("click", () => {
                    carousel.scrollBy({ left: 250, behavior: "smooth" });
                });
            });
        });
    </script>

    <footer class="footer">
        <div class="footer-container">
            <p>2025 DnNerds — Renato Matos, Natalia Macedo, Arthur Simões, Diego Toscano, Yuri Rei, Enzo Niglia </p>
            <div class="footer-links">
                <a href="https://www.youtube.com/" target="_blank" title="YouTube"><img src="../Imagens/youtube.png"
                        alt="YouTube"></a>
                <a href="https://www.instagram.com/" target="_blank" title="Instagram"><img
                        src="../Imagens/instagram.jpeg" alt="Instagram"></a>
                <a href="https://www.facebook.com/" target="_blank" title="Facebook"><img src="../Imagens/facebook.png"
                        alt="Facebook"></a>
                <a href="https://www.tiktok.com/" target="_blank" title="TikTok"><img src="../Imagens/tiktok.jpeg"
                        alt="TikTok"></a>
            </div>
        </div>
    </footer>
</body>

</html>