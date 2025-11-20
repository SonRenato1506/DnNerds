<?php
include_once('../config.php');

// Buscar a notícia principal (Batman)
$sql = "SELECT * FROM noticias WHERE palavrachave = 'astrobot' LIMIT 1";
$result = $conexao->query($sql);

if ($result && $result->num_rows > 0) {
    $noticia = $result->fetch_assoc();
    $categoria = $noticia['categoria'];
} else {
    echo "Notícia não encontrada.";
    exit;
}

// Buscar outras notícias da mesma categoria (excluindo a atual)
$sqlRelacionadas = "SELECT * FROM noticias 
                    WHERE categoria = '$categoria' 
                    AND palavrachave != 'astrobot'
                    ORDER BY data_publicacao DESC 
                    LIMIT 6";
$relacionadas = $conexao->query($sqlRelacionadas);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $noticia['titulo']; ?> - DnNerds</title>
    <link rel="stylesheet" href="../../Styles/Header.css?v=27">
    <link rel="stylesheet" href="../../Styles/Noticia.css?v=12">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Anonymous+Pro:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:wght@300;600;800&display=swap"
        rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar">
            <h2 class="title">DnNerds <img src="../../Imagens/favicon.png?v=2" alt=""></h2>
            <ul>
                <li><a href="../Noticias.php">Notícias</a></li>
                <li><a href="">NerdList</a></li>
                <li><a href="">Quizzes</a></li>
                <li><a href="">IA</a></li>
            </ul>
            <button class="btn-navbar"><a href="../FazerLogin.php">Fazer Login</a></button>
        </nav>
    </header>

    <main class="conteudo">
        <!-- Notícia principal -->
        <article class="noticia-detalhe">
            <img src="../<?php echo $noticia['imagem']; ?>" alt="<?php echo $noticia['titulo']; ?>">
            <h1><?php echo $noticia['titulo']; ?></h1>
            <p><?php echo nl2br($noticia['texto']); ?></p>
            <small>Publicado em: <?php echo date("d/m/Y", strtotime($noticia['data_publicacao'])); ?></small>
        </article>

        <!-- Lateral com notícias da mesma categoria -->
        <aside class="noticias-relacionadas">
            <h2>Mais em <?php echo htmlspecialchars($categoria); ?></h2>
            <div class="relacionadas-grid">
                <?php
                if ($relacionadas && $relacionadas->num_rows > 0) {
                    while ($row = $relacionadas->fetch_assoc()) {
                        echo '
                        <a href="' . $row['palavrachave'] . '.php" class="relacionada-item">
                            <div class="caixa-relacionada">
                                <img src="../' . $row['imagem'] . '" alt="' . htmlspecialchars($row['titulo'], ENT_QUOTES, 'UTF-8') . '">
                                <p>' . htmlspecialchars($row['titulo'], ENT_QUOTES, 'UTF-8') . '</p>
                            </div>
                        </a>';
                    }
                } else {
                    echo "<p>Nenhuma notícia relacionada encontrada.</p>";
                }
                ?>
            </div>
        </aside>
    </main>
</body>
</html>
