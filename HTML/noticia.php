<?php
include_once('config.php');

if (!isset($_GET['palavrachave']) || empty($_GET['palavrachave'])) {
    echo "Notícia inválida.";
    exit;
}

$chave = $conexao->real_escape_string($_GET['palavrachave']);

// Buscar notícia pela palavra-chave
$sql = "SELECT * FROM noticias WHERE palavrachave = '$chave' LIMIT 1";
$result = $conexao->query($sql);

if ($result->num_rows == 0) {
    echo "Notícia não encontrada.";
    exit;
}

$noticia = $result->fetch_assoc();
$categoria = $noticia['categoria'];

// Buscar relacionadas
$sqlRelacionadas = "
    SELECT * FROM noticias
    WHERE categoria = '$categoria'
    AND palavrachave != '$chave'
    ORDER BY data_publicacao DESC
    LIMIT 6
";
$relacionadas = $conexao->query($sqlRelacionadas);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $noticia['titulo']; ?> - DnNerds</title>

    <link rel="stylesheet" href="../Styles/Header.css?v=27">
    <link rel="stylesheet" href="../Styles/Noticia.css?v=12">
</head>
<body>

<header>
    <nav class="navbar">
        <h2 class="title">DnNerds <img src="../Imagens/favicon.png" alt=""></h2>
        <ul>
            <li><a href="Noticias.php">Notícias</a></li>
            <li><a href="">NerdList</a></li>
            <li><a href="Quizzes.php">Quizzes</a></li>
            <li><a href="">IA</a></li>
        </ul>
        <button class="btn-navbar"><a href="../FazerLogin.php">Fazer Login</a></button>
    </nav>
</header>

<main class="conteudo">

    <article class="noticia-detalhe">
        <img src="<?php echo $noticia['imagem']; ?>" alt="<?php echo $noticia['titulo']; ?>">
        <h1><?php echo $noticia['titulo']; ?></h1>
        <p><?php echo nl2br($noticia['texto']); ?></p>
        <small>Publicado em: <?php echo date("d/m/Y", strtotime($noticia['data_publicacao'])); ?></small>
    </article>

    <aside class="noticias-relacionadas">
        <h2>Mais em <?php echo htmlspecialchars($categoria); ?></h2>
        <div class="relacionadas-grid">
            <?php
            if ($relacionadas->num_rows > 0) {
                while ($row = $relacionadas->fetch_assoc()) {
                    echo '
                    <a href="noticia.php?palavrachave=' . urlencode($row['palavrachave']) . '" class="relacionada-item">
                        <div class="caixa-relacionada">
                            <img src="' . $row['imagem'] . '" alt="' . $row['titulo'] . '">
                            <p>' . $row['titulo'] . '</p>
                        </div>
                    </a>';
                }
            } else {
                echo "<p>Nenhuma notícia relacionada.</p>";
            }
            ?>
        </div>
    </aside>

</main>

</body>
</html>
