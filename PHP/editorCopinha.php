<?php
include_once('config.php');

/* ===============================
   VALIDAÇÃO DE ID
================================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Copinha não encontrada.";
    exit;
}

$copinha_id = (int) $_GET['id'];

/* ===============================
   BUSCAR COPINHA
================================ */
$stmt = $conexao->prepare("SELECT * FROM copinha WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $copinha_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Copinha não encontrada.";
    exit;
}

$copinha = $result->fetch_assoc();

/* ===============================
   SALVAR COPINHA
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* -------- SALVAR COPINHA -------- */
    if (isset($_POST['salvar_copinha'])) {
    $titulo = $_POST['titulo'];
    $imagem = $_POST['imagem'];

    $stmt = $conexao->prepare(
        "UPDATE copinha SET titulo=?, imagem=? WHERE id=?"
    );
    $stmt->bind_param("ssi", $titulo, $imagem, $copinha_id);
    $stmt->execute();

    header("Location: EditorCopinha.php?id=$copinha_id");
    exit;
}

    /* -------- SALVAR ITENS -------- */
    if (isset($_POST['salvar_itens'])) {
        foreach ($_POST['item_nome'] as $item_id => $nome) {
            $imagem = $_POST['item_imagem'][$item_id];

            $stmt = $conexao->prepare("
                UPDATE item_copinha 
                SET nome=?, imagem=? 
                WHERE id=? AND copinha_id=?
            ");
            $stmt->bind_param("ssii", $nome, $imagem, $item_id, $copinha_id);
            $stmt->execute();
        }

        header("Location: EditorCopinha.php?id=$copinha_id");
        exit;
    }

    if (isset($_POST['adicionar_item'])) {
    $nome = $_POST['novo_nome'];
    $imagem = $_POST['novo_imagem'];

    if (!empty($nome)) {
        $stmt = $conexao->prepare("
            INSERT INTO item_copinha (copinha_id, nome, imagem)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iss", $copinha_id, $nome, $imagem);
        $stmt->execute();
    }

    header("Location: EditorCopinha.php?id=$copinha_id");
    exit;
}
}

/* ===============================
   BUSCAR ITENS
================================ */
$itens = [];
$stmt = $conexao->prepare("
    SELECT id, nome, imagem 
    FROM item_copinha 
    WHERE copinha_id = ?
    ORDER BY id
");
$stmt->bind_param("i", $copinha_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $itens[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Copinha - <?= htmlspecialchars($copinha['titulo']) ?></title>
    <link rel="stylesheet" href="../Styles/Header.css">
    <link rel="stylesheet" href="../Styles/Criador.css">
</head>

<body>

<header>
    <nav class="navbar">
        <h2 class="title">
            DnNerds <img src="../Imagens/favicon.png" alt="">
        </h2>
        <ul>
            <li><a href="Noticias.php">Notícias</a></li>
            <li><a href="nerdlists.php">NerdList</a></li>
            <li><a href="Quizzes.php">Quizzes</a></li>
            <li><a href="copinhas.php">Copinhas</a></li>
        </ul>
        <button class="btn-navbar">
            <a href="FazerLogin.php">Fazer Login</a>
        </button>
    </nav>
</header>

<div class="container">

    <h2>Editar Copinha</h2>

    <!-- FORM COPINHA -->
    <form method="POST">
    <input type="hidden" name="salvar_copinha">

    <label>Título da Copinha</label>
    <input type="text" name="titulo" 
           value="<?= htmlspecialchars($copinha['titulo']) ?>" 
           required>

    <label>Imagem da Copinha (URL ou YouTube)</label>
    <input type="text" name="imagem" 
           value="<?= htmlspecialchars($copinha['imagem']) ?>" 
           placeholder="https://imagem.jpg ou https://youtube.com/watch?v=...">

    <button type="submit">Salvar Copinha</button>
</form>


    <hr>

    <!-- FORM ITENS -->
    <h3>Itens da Copinha</h3>

    <form method="POST">
        <input type="hidden" name="salvar_itens">

        <?php foreach ($itens as $item): ?>
            <div class="pergunta">
                <h4>Item</h4>

                <label>Nome</label>
                <input type="text" 
                       name="item_nome[<?= $item['id'] ?>]" 
                       value="<?= htmlspecialchars($item['nome']) ?>" 
                       required>

                <label>Imagem ou YouTube</label>
                <input type="text" 
                       name="item_imagem[<?= $item['id'] ?>]" 
                       value="<?= htmlspecialchars($item['imagem']) ?>" 
                       placeholder="https://imagem.jpg ou https://youtube.com/watch?v=...">
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn-secundario">Salvar Itens</button>
    </form>

    <hr>

<h3>Adicionar Novo Item</h3>

<form method="POST">
    <input type="hidden" name="adicionar_item">

    <label>Nome do Item</label>
    <input type="text" name="novo_nome" required>

    <label>Imagem ou YouTube</label>
    <input type="text" 
           name="novo_imagem"
           placeholder="https://imagem.jpg ou https://youtube.com/watch?v=...">

    <button type="submit">➕ Adicionar Item</button>
</form>


</div>

</body>
</html>
