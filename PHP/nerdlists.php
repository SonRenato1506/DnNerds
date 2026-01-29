<?php
include_once('config.php');

/* ===============================
   CATEGORIAS
================================ */
$categorias = [
    'Games' => 'Games',
    'Animes' => 'Anime',
    'Series' => 'Séries',
    'Filmes' => 'Filmes',
    'Livros' => 'Livros',
    'Variados' => 'Variados'
];

/* ===============================
   BUSCA
================================ */
$busca = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>DnNerds - NerdLists</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS (MESMO DOS QUIZZES) -->
    <link rel="stylesheet" href="../Styles/Noticias.css?v=28">
    <link rel="stylesheet" href="../Styles/Header.css?v=32">

</head>

<body>

<header>
    <nav class="navbar">
        <h1 class="title">
            DnNerds <img src="../Imagens/favicon.png?v=2" alt="">
        </h1>

        <ul>
            <li><a href="Noticias.php">Notícias</a></li>
            <li><a href="NerdLists.php" class="ativo">NerdList</a></li>
            <li><a href="Quizzes.php">Quizzes</a></li>
            <li><a href="criadorNerdList.php">Criar</a></li>
        </ul>

        <form method="GET" class="search-container">
            <button type="submit" class="btn-lupa">🔍</button>
            <input type="search" name="q" placeholder="Buscar NerdList..."
                value="<?= htmlspecialchars($busca) ?>">
        </form>

        <button class="btn-navbar">
            <a href="FazerLogin.php">Fazer Login</a>
        </button>
    </nav>
</header>

<main>

<?php
/* ===============================
   RESULTADO DE BUSCA
================================ */
if (!empty($busca)) {
    $termo = $conexao->real_escape_string($busca);

    echo "<h2 class='classe'>Resultados para: " . htmlspecialchars($busca) . "</h2>";

    $sqlBusca = "
        SELECT * FROM nerdlist
        WHERE titulo LIKE '%$termo%'
           OR descricao LIKE '%$termo%'
        ORDER BY id DESC
    ";

    $resultBusca = $conexao->query($sqlBusca);

    if ($resultBusca && $resultBusca->num_rows > 0) {
        echo '<div class="carousel-container">
                <button class="carousel-btn left">&#10094;</button>
                <div class="carousel">';

        while ($list = $resultBusca->fetch_assoc()) {
            ?>
            <a href="nerdlist.php?id=<?= $list['id'] ?>">
                <div id="caixa">
                    <img src="<?= htmlspecialchars($list['imagem'] ?: 'nerdlistdefault.jpg') ?>">
                    <p><b><?= htmlspecialchars($list['titulo']) ?></b></p>
                    <small><?= htmlspecialchars(substr($list['descricao'], 0, 80)) ?>...</small>
                </div>
            </a>
            <?php
        }

        echo '</div>
              <button class="carousel-btn right">&#10095;</button>
              </div>';
    } else {
        echo "<p>Nenhuma NerdList encontrada.</p>";
    }

    echo "</main>";
    include_once("footer.php");
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
    $sql = "
        SELECT * FROM nerdlist
        WHERE categoria = '$key'
        ORDER BY id DESC
        LIMIT 10
    ";
    $result = $conexao->query($sql);
    ?>

    <div class="carousel-container">
        <button class="carousel-btn left">&#10094;</button>

        <div class="carousel">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($list = $result->fetch_assoc()): ?>
                    <a href="nerdlist.php?id=<?= $list['id'] ?>">
                        <div id="caixa">
                            <img src="<?= htmlspecialchars($list['imagem'] ?: 'nerdlistdefault.jpg') ?>">
                            <p><b><?= htmlspecialchars($list['titulo']) ?></b></p>
                            <small><?= htmlspecialchars(substr($list['descricao'], 0, 80)) ?>...</small>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhuma NerdList nesta categoria.</p>
            <?php endif; ?>
        </div>

        <button class="carousel-btn right">&#10095;</button>
    </div>

<?php endforeach; ?>

</section>

</main>

<!-- SCRIPT DO CARROSSEL (IGUAL AO QUIZ) -->
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
        <p>2025 DnNerds — Renato Matos e equipe</p>
    </div>
</footer>

</body>
</html>
