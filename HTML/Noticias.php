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
?>

<?php
$destaques = ['astrobot', 'round6', 'batman']; // Altere pela palavrachave

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Grid+Single:wght@100..900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Anonymous+Pro:ital,wght@0,400;0,700;1,400;1,700&family=Caveat&family=Open+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Poppins:wght@300;600;800&display=swap"
        rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar">
            <h1 class="title">DnNerds <img src="../Imagens/favicon.png?v=2?v=2" alt=""></h1>
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
        <h1></h1>
        <?php
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
                    echo '<a href="./Noticias/' . $row['palavrachave'] . '.php">
                    <div id="caixa">
                        <img src="' . $row['imagem'] . '" alt="">
                        <p>' . htmlspecialchars($row['titulo'], ENT_QUOTES, 'UTF-8') . '</p>
                    </div>
                  </a>';
                }
                echo '</div>'; // fecha carousel
                echo '<button class="carousel-btn right" type="button">&#10095;</button>';
                echo '</div>'; // fecha carousel-container
            } else {
                echo '<p>Nenhum resultado encontrado.</p>';
            }

            // Impede que as categorias abaixo apareçam junto com a busca
            exit;
        }

        ?>

        <section id="noticia-destaque">
            <!-- <h2>Notícia Destaque</h2> -->
            <div class="destaque-container">
                <button class="destaque-btn left" type="button">&#10094;</button>
                <div class="destaque-carousel">
                    <?php
                    if ($resultDestaque && $resultDestaque->num_rows > 0) {
                        while ($row = $resultDestaque->fetch_assoc()) {
                            $titulo = $row['titulo'];
                            $titulo_curto = mb_strlen($titulo, 'UTF-8') > 120 ? mb_substr($titulo, 0, 117, 'UTF-8') . '...' : $titulo;

                            echo '
                                <div class="destaque-item">
                                    <a href="./Noticias/' . $row['palavrachave'] . '.php">
                                        <img src="' . $row['imagem'] . '" alt="' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '">
                                        <div class="destaque-texto">
                                            <h3>' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '</h3>
                                            <p>' . htmlspecialchars($titulo_curto, ENT_QUOTES, 'UTF-8') . '</p>
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



        <div id="noticias">
            <?php
            foreach ($categorias as $categoriaKey => $categoriaTitulo) {
                echo '<div class="classe">';
                echo '<h2>' . $categoriaTitulo . '</h2>';
                echo '</div>';

                // Seleciona notícias da categoria
                if ($categoriaKey == 'Recente') {
                    $sql = "SELECT * FROM noticias ORDER BY data_publicacao DESC LIMIT 25";
                } else {
                    $sql = "SELECT * FROM noticias WHERE categoria='$categoriaKey' ORDER BY data_publicacao DESC LIMIT 15";
                }


                $result = $conexao->query($sql);

                echo '<div class="carousel-container">';
                echo '<button class="carousel-btn left" type="button">&#10094;</button>';
                echo '<div class="carousel">'; // não precisa mais da classe flex aqui, vamos aplicar o estilo na .carousel
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<a href="./Noticias/' . $row['palavrachave'] . '.php">
                <div id="caixa">
                    <img src="' . $row['imagem'] . '" alt="">
                    <p>' . $row['titulo'] . '</p>
                </div>
              </a>';
                    }
                } else {
                    echo '<p>Nenhuma notícia encontrada nesta categoria.</p>';
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

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const destaqueCarousel = document.querySelector(".destaque-carousel");
            const destaqueItems = document.querySelectorAll(".destaque-item");
            const btnLeft = document.querySelector(".destaque-btn.left");
            const btnRight = document.querySelector(".destaque-btn.right");

            let index = 0;

            function updateCarousel() {
                destaqueCarousel.style.transform = `translateX(-${index * 100}%)`;
            }

            btnLeft.addEventListener("click", () => {
                index = (index > 0) ? index - 1 : destaqueItems.length - 1;
                updateCarousel();
            });

            btnRight.addEventListener("click", () => {
                index = (index + 1) % destaqueItems.length;
                updateCarousel();
            });

            // Troca automática a cada 6 segundos
            setInterval(() => {
                index = (index + 1) % destaqueItems.length;
                updateCarousel();
            }, 6000);
        });
    </script>

    <footer class="footer">
        <div class="footer-container">
            <p>2025 DnNerds — Renato Matos, Natalia Macedo, Arthur Simões, Diego Toscano, Yuri Rei, Enzo Niglia </p>
            <div class="footer-links">
                <a href="https://www.youtube.com/" target="_blank" title="YouTube">
                    <img src="../Imagens/youtube.png" alt="YouTube">
                </a>
                <a href="https://www.instagram.com/" target="_blank" title="Instagram">
                    <img src="../Imagens/instagram.jpeg" alt="Instagram">
                </a>
                <a href="https://www.facebook.com/" target="_blank" title="Facebook">
                    <img src="../Imagens/facebook.png" alt="Facebook">
                </a>
                <a href="https://www.tiktok.com/" target="_blank" title="TikTok">
                    <img src="../Imagens/tiktok.jpeg" alt="TikTok">
                </a>
            </div>
        </div>
    </footer>



</body>

</html>