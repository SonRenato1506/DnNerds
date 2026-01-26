<?php
include_once('config.php');

/* ===============================
   CATEGORIAS
================================ */
$categorias = [
    'games' => 'Games',
    'animes' => 'Animes',
    'filmes_series' => 'Filmes & Séries',
    'rpg' => 'RPG',
    'esporte' => 'Esporte',
    'musica' => 'Música',
    'outros' => 'Outros'
];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Copinhas - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Noticias.css?v=3">
    <link rel="stylesheet" href="../Styles/Header.css?v=35">
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
                <li><a href="criadorCopinhas.php">Criar</a></li>
                <li><a href="copinhas.php">Copinhas</a></li>
            </ul>

            <form method="GET" action="copinhas.php" class="search-container">
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
           BUSCA DE COPINHAS
        ================================ */
        if (!empty($_GET['q'])) {

            $termo = $conexao->real_escape_string(trim($_GET['q']));

            echo "<div class='classe'>
            <h2>Resultados da busca por: " . htmlspecialchars($termo) . "</h2>
          </div>";

            $sqlBusca = "
        SELECT * FROM copinha
        WHERE titulo LIKE '%$termo%'
        ORDER BY id DESC
    ";

            $resultBusca = $conexao->query($sqlBusca);

            if ($resultBusca && $resultBusca->num_rows > 0) {

                echo '<div class="carousel-container">
                <button class="carousel-btn left">&#10094;</button>
                <div class="carousel">';

                while ($row = $resultBusca->fetch_assoc()) {
                    ?>
                    <a href="copinha.php?id=<?= $row['id'] ?>">
                        <div id="caixa">
                            <img src="<?= htmlspecialchars($row['imagem']) ?>" alt="">
                            <p><?= htmlspecialchars($row['titulo']) ?></p>
                        </div>
                    </a>
                    <?php
                }

                echo '</div>
              <button class="carousel-btn right">&#10095;</button>
              </div>';

            } else {
                echo "<p>Nenhuma copinha encontrada.</p>";
            }

            // Impede que as categorias apareçam junto da busca
            exit;
        }
        ?>


        <!-- COPINHAS POR CATEGORIA -->
        <?php foreach ($categorias as $key => $titulo): ?>

            <div class="classe">
                <h2><?= $titulo ?></h2>
            </div>

            <?php
            $sql = "SELECT * FROM copinha WHERE categoria = '$key' ORDER BY id DESC LIMIT 15";
            $result = $conexao->query($sql);
            ?>

            <div class="carousel-container">
                <button class="carousel-btn left">&#10094;</button>

                <div class="carousel">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <a href="copinha.php?id=<?= $row['id'] ?>">
                                <div id="caixa">
                                    <img src="<?= htmlspecialchars($row['imagem']) ?>" alt="">
                                    <p><?= htmlspecialchars($row['titulo']) ?></p>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Nenhuma copinha encontrada.</p>
                    <?php endif; ?>
                </div>

                <button class="carousel-btn right">&#10095;</button>
            </div>

        <?php endforeach; ?>

        </section>

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

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
            <p>2025 DnNerds — Renato Matos, Natalia Macedo, Arthur Simões, Diego Toscano, Yuri Reis, Enzo Niglia</p>
        </div>
    </footer>

</body>

</html>