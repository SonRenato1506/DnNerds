<?php
include_once('config.php');

/* ===============================
   CATEGORIAS
================================ */
$categorias = [
    'Recente' => 'Notícias Recentes',
    'Jogos' => 'Games',
    'Animes' => 'Animes',
    'Series/Filmes' => 'Séries/Filmes',
    'Livros' => 'Livros',
    'Tecnologia' => 'Tecnologia',
    'RPG' => 'RPG'
];

/* ===============================
   DESTAQUES
================================ */
$destaques = ['astrobot', 'round6', 'batman'];

$sqlDestaque = "
    SELECT * FROM noticias
    WHERE palavrachave IN ('" . implode("','", $destaques) . "')
";
$resultDestaque = $conexao->query($sqlDestaque);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Noticias.css?v=28">
    <link rel="stylesheet" href="../Styles/Header.css?v=36">
</head>

<body>

    <header>
        <nav class="navbar">
            <h1 class="title">
                DnNerds <img src="../Imagens/favicon.png?v=2" alt="">
            </h1>

            <ul>
                <li><a href="../PHP/Noticias.php" class="ativo">Notícias</a></li>
                <li><a href="nerdlists.php">NerdList</a></li>
                <li><a href="../PHP/Quizzes.php">Quizzes</a></li>
                <li><a href="criadorNoticias.php">Criar</a></li>
                <li><a href="copinhas.php">Copinhas</a></li>
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

        <?php
        /* ===============================
           BUSCA
        ================================ */
        if (!empty($_GET['q'])) {
            $termo = $conexao->real_escape_string(trim($_GET['q']));
            echo "<h2 class='classe'>Resultados da busca por: " . htmlspecialchars($termo) . "</h2>";

            $sqlBusca = "
        SELECT * FROM noticias
        WHERE titulo LIKE '%$termo%'
        ORDER BY data_publicacao DESC
    ";
            $resultBusca = $conexao->query($sqlBusca);

            if ($resultBusca && $resultBusca->num_rows > 0) {
                echo '<div class="carousel-container">
                <button class="carousel-btn left">&#10094;</button>
                <div class="carousel">';

                while ($row = $resultBusca->fetch_assoc()) {
                    ?>
                    <a href="./noticia.php?palavrachave=<?= urlencode($row['palavrachave']) ?>">
                        <div id="caixa">
                            <img src="<?= $row['imagem'] ?>" alt="">
                            <p><?= htmlspecialchars($row['titulo']) ?></p>
                        </div>
                    </a>
                    <?php
                }

                echo '</div><button class="carousel-btn right">&#10095;</button></div>';
            } else {
                echo "<p>Nenhum resultado encontrado.</p>";
            }
            exit;
        }
        ?>

        <!-- DESTAQUES -->
        <section id="noticia-destaque">
            <div class="destaque-container">
                <button class="destaque-btn left">&#10094;</button>

                <div class="destaque-carousel">
                    <?php
                    if ($resultDestaque && $resultDestaque->num_rows > 0) {
                        while ($row = $resultDestaque->fetch_assoc()) {
                            ?>
                            <div class="destaque-item">
                                <a href="./noticia.php?palavrachave=<?= urlencode($row['palavrachave']) ?>">
                                    <img src="<?= $row['imagem'] ?>" alt="">
                                    <div class="destaque-texto">
                                        <h3><?= htmlspecialchars($row['titulo']) ?></h3>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>Nenhuma notícia destaque encontrada.</p>";
                    }
                    ?>
                </div>

                <button class="destaque-btn right">&#10095;</button>
            </div>
        </section>

        <!-- CATEGORIAS -->
        <section id="noticias">
            <?php foreach ($categorias as $key => $titulo): ?>

                <div class="classe">
                    <h2><?= $titulo ?></h2>
                </div>

                <?php
                $sql = ($key === 'Recente')
                    ? "SELECT * FROM noticias ORDER BY data_publicacao DESC LIMIT 25"
                    : "SELECT * FROM noticias WHERE categoria='$key' ORDER BY data_publicacao DESC LIMIT 15";

                $result = $conexao->query($sql);
                ?>

                <div class="carousel-container">
                    <button class="carousel-btn left">&#10094;</button>

                    <div class="carousel">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <a href="./noticia.php?palavrachave=<?= urlencode($row['palavrachave']) ?>">
                                    <div id="caixa">
                                        <img src="<?= $row['imagem'] ?>" alt="">
                                        <p><?= htmlspecialchars($row['titulo']) ?></p>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>Nenhuma notícia encontrada.</p>
                        <?php endif; ?>
                    </div>

                    <button class="carousel-btn right">&#10095;</button>
                </div>

            <?php endforeach; ?>
        </section>

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            /* ===============================
               DESTAQUE
            =============================== */
            const destaqueCarousel = document.querySelector(".destaque-carousel");
            const destaqueItens = document.querySelectorAll(".destaque-item");
            const destaqueLeft = document.querySelector(".destaque-btn.left");
            const destaqueRight = document.querySelector(".destaque-btn.right");

            let destaquePos = 0;

            function atualizarDestaque() {
                destaqueCarousel.style.transform = `translateX(-${destaquePos * 100}%)`;
            }

            destaqueRight.onclick = () => {
                destaquePos = (destaquePos + 1) % destaqueItens.length;
                atualizarDestaque();
            };

            destaqueLeft.onclick = () => {
                destaquePos = (destaquePos - 1 + destaqueItens.length) % destaqueItens.length;
                atualizarDestaque();
            };

            setInterval(() => {
                destaquePos = (destaquePos + 1) % destaqueItens.length;
                atualizarDestaque();
            }, 4000);

            /* ===============================
               CARROSSEIS DE CATEGORIA
            =============================== */
            document.querySelectorAll(".carousel-container").forEach(container => {

                const carousel = container.querySelector(".carousel");
                const btnLeft = container.querySelector(".carousel-btn.left");
                const btnRight = container.querySelector(".carousel-btn.right");

                if (!carousel || !btnLeft || !btnRight) return;

                const scrollAmount = 320;

                btnRight.onclick = () => {
                    carousel.scrollBy({ left: scrollAmount, behavior: "smooth" });
                };

                btnLeft.onclick = () => {
                    carousel.scrollBy({ left: -scrollAmount, behavior: "smooth" });
                };
            });

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