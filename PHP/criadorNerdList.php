<?php
session_start();
require_once 'config.php';

/* =======================
   CRIAR NERDLIST
======================= */
if (isset($_POST['criar_nerdlist'])) {
    $stmt = $conexao->prepare(
        "INSERT INTO nerdlist (titulo, descricao, categoria, imagem)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssss",
        $_POST['titulo'],
        $_POST['descricao'],
        $_POST['categoria'],
        $_POST['imagem']
    );
    $stmt->execute();

    $_SESSION['nerdlist_id'] = $conexao->insert_id;
}

/* =======================
   CRIAR TIER
======================= */
if (isset($_POST['criar_tier'])) {
    $stmt = $conexao->prepare(
        "INSERT INTO nerdlist_tiers (nerdlist_id, nome, cor)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param(
        "iss",
        $_SESSION['nerdlist_id'],
        $_POST['nome'],
        $_POST['cor']
    );
    $stmt->execute();
}

/* =======================
   CRIAR ITEM
======================= */
if (isset($_POST['criar_item'])) {
    $stmt = $conexao->prepare(
        "INSERT INTO nerdlist_itens (nerdlist_id, nome, imagem)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param(
        "iss",
        $_SESSION['nerdlist_id'],
        $_POST['nome'],
        $_POST['imagem']
    );
    $stmt->execute();
}

/* =======================
   DADOS ATUAIS
======================= */
$nerdlist_id = $_SESSION['nerdlist_id'] ?? null;

$tiers = $nerdlist_id
    ? $conexao->query("SELECT * FROM nerdlist_tiers WHERE nerdlist_id = $nerdlist_id")
    : [];

$itens = $nerdlist_id
    ? $conexao->query("SELECT * FROM nerdlist_itens WHERE nerdlist_id = $nerdlist_id")
    : [];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editor de NerdList - DnNerds</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../Styles/Header.css">
<link rel="stylesheet" href="../Styles/Criador.css">
</head>

<body>

<header>
    <nav class="navbar">
        <h2 class="title">
            DnNerds <img src="../Imagens/anfitriao.png?v=2">
        </h2>
        <ul>
            <li><a href="Noticias.php">Notícias</a></li>
            <li><a href="nerdlists.php">NerdList</a></li>
            <li><a href="Quizzes.php">Quizzes</a></li>
        </ul>
    </nav>
</header>

<div class="container">

<h2>📋 Editor de NerdList</h2>

<!-- ===================== -->
<!-- CRIAR NERDLIST -->
<!-- ===================== -->
<?php if (!$nerdlist_id): ?>
<form method="post">
    <label>Título</label>
    <input name="titulo" required>

    <label>Descrição</label>
    <textarea name="descricao"></textarea>

    <label>Categoria</label>
    <select name="categoria">
        <option>Animes</option>
        <option>Games</option>
        <option>Filmes</option>
        <option>Series</option>
        <option>Livros</option>
        <option>Variados</option>
    </select>

    <label>Imagem</label>
    <input name="imagem">

    <button name="criar_nerdlist">Criar NerdList</button>
</form>

<?php else: ?>

<!-- ===================== -->
<!-- CRIAR TIER -->
<!-- ===================== -->
<h3>🎨 Criar Tier</h3>
<form method="post">
    <input name="nome" placeholder="Nome do Tier" required>
    <input type="color" name="cor" value="#6a1d72">
    <button name="criar_tier">Adicionar Tier</button>
</form>

<!-- ===================== -->
<!-- CRIAR ITEM -->
<!-- ===================== -->
<h3>🧩 Criar Item</h3>
<form method="post">
    <input name="nome" placeholder="Nome do Item" required>
    <input name="imagem" placeholder="URL da imagem">
    <button name="criar_item">Adicionar Item</button>
</form>

<hr>

<!-- ===================== -->
<!-- PREVIEW -->
<!-- ===================== -->
<h3>👁 Preview</h3>

<?php while ($t = $tiers->fetch_assoc()): ?>
<div class="pergunta">
    <h3><?= htmlspecialchars($t['nome']) ?></h3>
</div>
<?php endwhile; ?>

<div class="itens-pool">
<?php while ($i = $itens->fetch_assoc()): ?>
    <div class="opcao">
        <img src="<?= htmlspecialchars($i['imagem']) ?>" width="60">
        <span><?= htmlspecialchars($i['nome']) ?></span>
    </div>
<?php endwhile; ?>
</div>

<?php endif; ?>

</div>

</body>
</html>
