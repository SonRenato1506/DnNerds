<?php
include_once('config.php');

if (empty($_GET['palavrachave'])) {
    die("Notícia inválida.");
}

$chave = $_GET['palavrachave'];

/* ===============================
   BUSCAR NOTÍCIA
================================ */
$stmt = $conexao->prepare(
    "SELECT * FROM noticias WHERE palavrachave = ? LIMIT 1"
);
$stmt->bind_param("s", $chave);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Notícia não encontrada.");
}

$noticia = $result->fetch_assoc();
$categoria = $noticia['categoria'];

/* ===============================
   RELACIONADAS
================================ */
$stmtRel = $conexao->prepare(
    "SELECT * FROM noticias 
     WHERE categoria = ? AND palavrachave != ?
     ORDER BY data_publicacao DESC
     LIMIT 6"
);
$stmtRel->bind_param("ss", $categoria, $chave);
$stmtRel->execute();
$relacionadas = $stmtRel->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Header.css?v=27">
    <link rel="stylesheet" href="../Styles/Noticia.css?v=12">
</head>

<body>

    <header>
        <nav class="navbar">
            <h2 class="title">
                DnNerds <img src="../Imagens/favicon.png" alt="DnNerds">
            </h2>
            <ul>
                <li><a href="Noticias.php">Notícias</a></li>
                <li><a href="nerdlists.php">NerdList</a></li>
                <li><a href="Quizzes.php">Quizzes</a></li>
                <li><a href="copinhas.php" class="ativo">Copinhas</a></li>
                <li><a href="editorNoticia.php?id=<?= $noticia['id'] ?>" class="btn-editar-noticia">Editor</a></li>
            </ul>
            <button class="btn-navbar">
                <a href="FazerLogin.php">Fazer Login</a>
            </button>
        </nav>
    </header>

    <main class="conteudo">

        <article class="noticia-detalhe">
            <img src="<?= htmlspecialchars($noticia['imagem']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>">

            <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>

            <p><?= nl2br(htmlspecialchars($noticia['texto'])) ?></p>

            <time datetime="<?= $noticia['data_publicacao'] ?>">
                Publicado em: <?= date("d/m/Y", strtotime($noticia['data_publicacao'])) ?>
            </time>
        </article>

        <aside class="noticias-relacionadas">
            <h2>Mais em <?= htmlspecialchars($categoria) ?></h2>

            <div class="relacionadas-grid">
                <?php if ($relacionadas->num_rows > 0): ?>
                    <?php while ($row = $relacionadas->fetch_assoc()): ?>
                        <a href="noticia.php?palavrachave=<?= urlencode($row['palavrachave']) ?>" class="relacionada-item">

                            <div class="caixa-relacionada">
                                <img src="<?= htmlspecialchars($row['imagem']) ?>"
                                    alt="<?= htmlspecialchars($row['titulo']) ?>">
                                <p><?= htmlspecialchars($row['titulo']) ?></p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Nenhuma notícia relacionada.</p>
                <?php endif; ?>
            </div>
        </aside>

    </main>

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