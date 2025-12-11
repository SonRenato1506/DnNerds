<?php
include_once('config.php');

// Define as categorias que você quer exibir
$categorias = [
    'Recente' => 'Notícias Recentes',
    'Jogos' => 'Games',
    'Animes' => 'Animes',
    'Series/Filmes' => 'Séries/Filmes',
    'Livros' => 'Livros',
    'Tecnologia' => 'Tecnologia',
    'RPG' => 'RPG'
];

$destaques = ['astrobot', 'round6', 'batman']; // Palavras-chave

$sqlDestaque = "SELECT * FROM noticias WHERE palavrachave IN ('" . implode("','", $destaques) . "')";
$resultDestaque = $conexao->query($sqlDestaque);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DnNerds</title>
    <link rel="stylesheet" href="../Styles/Noticias.css?v=26">
    <link rel="stylesheet" href="../Styles/Header.css?v=31">
</head>

<body>

    <header>
        <nav class="navbar">
            <h1 class="title">DnNerds <img src="../Imagens/favicon.png?v=2" alt=""></h1>
            <ul>
                <li><a href="../HTML/Noticias.php">Notícias</a></li>
                <li><a href="">NerdList</a></li>
                <li><a href="../HTML/Quizzes.php">Quizzes</a></li>
                <li><a href="">IA</a></li>
            </ul>

            <div class="search-container">
                <form method="GET" action="Noticias.php">
                    <input type="search" name="q" id="search" placeholder="Buscar..."
                        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>

            <button class="btn-navbar"><a href="../HTML/FazerLogin.php">Fazer Login</a></button>
        </nav>
    </header>

    <main>

        <?php
        // BUSCA
        if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
            $termo = $conexao->real_escape_string(trim($_GET['q']));
            echo '<h2 class="classe">Resultados da busca por: ' . htmlspecialchars($termo) . '</h2>';

            $sqlBusca = "SELECT * FROM noticias WHERE titulo LIKE '%$termo%' ORDER BY data_publicacao DESC";
            $resultBusca = $conexao->query($sqlBusca);

            if ($resultBusca && $resultBusca->num_rows > 0) {
                echo '<div class="carousel-container">';
                echo '<button class="carousel-btn left" type="button">&#10094;</button>';
                echo '<div class="carousel">';

                while ($row = $resultBusca->fetch_assoc()) {
                    echo '
                    <a href="./noticia.php?palavrachave=' . urlencode($row['palavrachave']) . '">
                        <div id="caixa">
                            <img src="' . $row['imagem'] . '" alt="">
                            <p>' . htmlspecialchars($row['titulo']) . '</p>
                        </div>
                    </a>';
                }

                echo '</div>';
                echo '<button class="carousel-btn right" type="button">&#10095;</button>';
                echo '</div>';
            } else {
                echo '<p>Nenhum resultado encontrado.</p>';
            }

            exit;
        }
        ?>

        <!-- DESTAQUES -->
        <section id="noticia-destaque">
            <div class="destaque-container">
                <button class="destaque-btn left" type="button">&#10094;</button>

                <!-- IMPORTANTE: classe alterada para não interferir no JS -->
                <div class="destaque-carousel">

                    <?php
                    if ($resultDestaque && $resultDestaque->num_rows > 0) {
                        while ($row = $resultDestaque->fetch_assoc()) {
                            echo '
                                <div class="destaque-item">
                                    <a href="./noticia.php?palavrachave=' . urlencode($row['palavrachave']) . '">
                                        <img src="' . $row['imagem'] . '" alt="">
                                        <div class="destaque-texto">
                                            <h3>' . htmlspecialchars($row['titulo']) . '</h3>
                                        </div>
                                    </a>
                                </div>';
                        }
                    } else {
                        echo "<p>Nenhuma notícia destaque encontrada.</p>";
                    }
                    ?>

                </div>

                <button class="destaque-btn right" type="button">&#10095;</button>
            </div>
        </section>

        <!-- CATEGORIAS -->
        <div id="noticias">

            <?php
            foreach ($categorias as $categoriaKey => $categoriaTitulo) {

                echo '<div class="classe"><h2>' . $categoriaTitulo . '</h2></div>';

                if ($categoriaKey == 'Recente') {
                    $sql = "SELECT * FROM noticias ORDER BY data_publicacao DESC LIMIT 25";
                } else {
                    $sql = "SELECT * FROM noticias WHERE categoria='$categoriaKey' ORDER BY data_publicacao DESC LIMIT 15";
                }

                $result = $conexao->query($sql);

                echo '<div class="carousel-container">';
                echo '<button class="carousel-btn left" type="button">&#10094;</button>';
                echo '<div class="carousel">';

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '
                        <a href="./noticia.php?palavrachave=' . urlencode($row['palavrachave']) . '">
                            <div id="caixa">
                                <img src="' . $row['imagem'] . '" alt="">
                                <p>' . $row['titulo'] . '</p>
                            </div>
                        </a>';
                    }
                } else {
                    echo '<p>Nenhuma notícia encontrada.</p>';
                }

                echo '</div>';
                echo '<button class="carousel-btn right" type="button">&#10095;</button>';
                echo '</div>';
            }
            ?>

        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // --- CARROSSEIS NORMAIS (categorias/busca) ---
            const carrosseis = document.querySelectorAll(".carousel-container .carousel");

            document.querySelectorAll(".carousel-btn.left").forEach((btn, i) => {
                btn.addEventListener("click", () => {
                    carrosseis[i].scrollBy({ left: -250, behavior: "smooth" });
                });
            });

            document.querySelectorAll(".carousel-btn.right").forEach((btn, i) => {
                btn.addEventListener("click", () => {
                    carrosseis[i].scrollBy({ left: 250, behavior: "smooth" });
                });
            });


            // --- CARROSSEL DE DESTAQUES (loop + autoplay) ---
            const destaqueCarousel = document.querySelector(".destaque-carousel");
            const itens = document.querySelectorAll(".destaque-item");
            const total = itens.length;

            let pos = 0;
            let intervalo;

            function atualizarCarrossel() {
                destaqueCarousel.style.transform = `translateX(-${pos * 100}%)`;
            }

            function proximo() {
                pos = (pos + 1) % total; // loop infinito
                atualizarCarrossel();
            }

            function anterior() {
                pos = (pos - 1 + total) % total; // loop infinito para trás
                atualizarCarrossel();
            }

            // botões
            document.querySelector(".destaque-btn.right").addEventListener("click", () => {
                proximo();
                resetarAutoplay();
            });

            document.querySelector(".destaque-btn.left").addEventListener("click", () => {
                anterior();
                resetarAutoplay();
            });

            // autoplay a cada 6s
            function iniciarAutoplay() {
                intervalo = setInterval(proximo, 4000);
            }

            function resetarAutoplay() {
                clearInterval(intervalo);
                iniciarAutoplay();
            }

            iniciarAutoplay();

        });
    </script>


</body>

</html>