<?php
session_start();
include_once('config.php');

if (empty($_GET['palavrachave'])) {
    die("Notícia inválida.");
}

$chave = $_GET['palavrachave'];

/* ===============================
   BUSCAR NOTÍCIA + CRIADOR
================================ */
$stmt = $conexao->prepare(
    "SELECT n.*, u.nome AS criador_nome
     FROM noticias n
     JOIN usuarios u ON u.id = n.criador
     WHERE n.palavrachave = ?
     LIMIT 1"
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


/* ===============================
   INSERIR COMENTÁRIO
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {

    if (!isset($_SESSION['id'])) {
        die("❌ Você precisa estar logado para comentar.");
    }

    $comentario = trim($_POST['comentario']);

    if (!empty($comentario)) {

        $stmtComent = $conexao->prepare(
            "INSERT INTO comentarios (noticia_id, usuario_id, comentario)
             VALUES (?, ?, ?)"
        );

        $stmtComent->bind_param(
            "iis",
            $noticia['id'],
            $_SESSION['id'],
            $comentario
        );

        $stmtComent->execute();
    }

    header("Location: noticia.php?palavrachave=" . urlencode($chave));
    exit;
}

include_once("header.php");


/* ===============================
   BUSCAR COMENTÁRIOS
================================ */
$stmtComents = $conexao->prepare(
    "SELECT c.*, u.nome, u.foto
     FROM comentarios c
     JOIN usuarios u ON u.id = c.usuario_id
     WHERE c.noticia_id = ?
     ORDER BY c.data_comentario DESC"
);

$stmtComents->bind_param("i", $noticia['id']);
$stmtComents->execute();
$comentarios = $stmtComents->get_result();

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <title>
        <?= htmlspecialchars($noticia['titulo']) ?> - DnNerds
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Noticia.css?v=18">

</head>

<body>

<main class="conteudo">

<div class="coluna-principal">

<article class="noticia-detalhe">

<img
src="<?= htmlspecialchars($noticia['imagem']) ?>"
alt="<?= htmlspecialchars($noticia['titulo']) ?>"
>

<h1>
<?= htmlspecialchars($noticia['titulo']) ?>
</h1>


<p class="autor">
Criado por
<?= htmlspecialchars($noticia['criador_nome']) ?>
</p>


<p>
<?= nl2br(htmlspecialchars($noticia['texto'])) ?>
</p>


<time>
Publicado em:
<?= date("d/m/Y", strtotime($noticia['data_publicacao'])) ?>
</time>

</article>


<section class="comentarios">

<h2>💬 Comentários</h2>

<?php if (isset($_SESSION['id'])): ?>

<form method="POST" class="comentario-form">

<textarea
name="comentario"
placeholder="Escreva seu comentário..."
required
></textarea>

<button type="submit">
Comentar
</button>

</form>

<?php else: ?>

<p>
👉 <a href="FazerLogin.php">Faça login</a>
para comentar.
</p>

<?php endif; ?>


<div class="lista-comentarios">

<?php if ($comentarios->num_rows > 0): ?>

<?php while ($coment = $comentarios->fetch_assoc()): ?>

<div class="comentario-item">

<img
src="<?= !empty($coment['foto']) ? $coment['foto'] : '../Imagens/user.png' ?>"
>

<div class="comentario-conteudo">

<strong>
<?= htmlspecialchars($coment['nome']) ?>
</strong>

<p>
<?= nl2br(htmlspecialchars($coment['comentario'])) ?>
</p>

<span>
<?= date("d/m/Y H:i", strtotime($coment['data_comentario'])) ?>
</span>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>Nenhum comentário ainda.</p>

<?php endif; ?>

</div>

</section>

</div>



<aside class="noticias-relacionadas">

<h2>
Mais em
<?= htmlspecialchars($categoria) ?>
</h2>

<div class="relacionadas-grid">

<?php if ($relacionadas->num_rows > 0): ?>

<?php while ($row = $relacionadas->fetch_assoc()): ?>

<a
href="noticia.php?palavrachave=<?= urlencode($row['palavrachave']) ?>"
class="relacionada-item"
>

<div class="caixa-relacionada">

<img
src="<?= htmlspecialchars($row['imagem']) ?>"
alt="<?= htmlspecialchars($row['titulo']) ?>"
>

<p>
<?= htmlspecialchars($row['titulo']) ?>
</p>

</div>

</a>

<?php endwhile; ?>

<?php else: ?>

<p>Nenhuma notícia relacionada.</p>

<?php endif; ?>

</div>

</aside>

</main>



<?php
if (isset($_SESSION['id'])) {

    $ehCriador =
        $_SESSION['id'] == $noticia['criador'];

    $ehAdmin =
        isset($_SESSION['adm']) &&
        $_SESSION['adm'] == 1;

    if ($ehCriador || $ehAdmin):
?>

<a href="editorNoticia.php?id=<?= $noticia['id'] ?>">
<button id="editor">
Editar Notícia
</button>
</a>

<?php
    endif;
}
?>


</body>

</html>

<?php include_once("footer.php"); ?>