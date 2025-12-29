<?php
session_start();
if (!isset($_POST) || empty($_POST)) {
    $_SESSION['passo'] = 1;
    unset($_SESSION['personalidade_id']);
}

require_once __DIR__ . '/config.php';

$passo = $_SESSION['passo'] ?? 1;

/* =====================
   PROCESSAMENTOS
===================== */

// PASSO 1 – QUIZ
if (isset($_POST['criar_quiz'])) {
    $stmt = $conexao->prepare(
        "INSERT INTO personalidade (titulo, descricao, imagem, categoria)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss",
        $_POST['titulo'],
        $_POST['descricao'],
        $_POST['imagem'],
        $_POST['categoria']
    );
    $stmt->execute();

    $_SESSION['personalidade_id'] = $conexao->insert_id;
    $_SESSION['passo'] = 2;

    header("Location: criadorPersonalidade.php");
    exit;
}

// PASSO 2 – RESULTADOS
if (isset($_POST['criar_resultado'])) {
    $stmt = $conexao->prepare(
        "INSERT INTO personalidade_resultados
         (personalidade_id, titulo, descricao, imagem)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isss",
        $_SESSION['personalidade_id'],
        $_POST['titulo'],
        $_POST['descricao'],
        $_POST['imagem']
    );
    $stmt->execute();
}

if (isset($_POST['ir_perguntas'])) {
    $_SESSION['passo'] = 3;
    header("Location: criadorPersonalidade.php");
    exit;
}

// PASSO 3 – PERGUNTAS
if (isset($_POST['criar_pergunta'])) {
    $stmt = $conexao->prepare(
        "INSERT INTO personalidade_perguntas (personalidade_id, texto)
         VALUES (?, ?)"
    );
    $stmt->bind_param("is",
        $_SESSION['personalidade_id'],
        $_POST['texto']
    );
    $stmt->execute();
}

if (isset($_POST['ir_respostas'])) {
    $_SESSION['passo'] = 4;
    header("Location: criadorPersonalidade.php");
    exit;
}

// PASSO 4 – RESPOSTAS
if (isset($_POST['criar_resposta'])) {
    $stmt = $conexao->prepare(
        "INSERT INTO personalidade_respostas (pergunta_id, texto)
         VALUES (?, ?)"
    );
    $stmt->bind_param("is",
        $_POST['pergunta_id'],
        $_POST['texto']
    );
    $stmt->execute();

    $resposta_id = $conexao->insert_id;

    foreach ($_POST['pontos'] as $resultado_id => $pontos) {
        $stmt = $conexao->prepare(
            "INSERT INTO personalidade_respostas_pontuacao
             (resposta_id, resultado_id, pontos)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iii",
            $resposta_id,
            $resultado_id,
            $pontos
        );
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Criador de Quiz</title>
<style>
body { font-family: Arial; background:#f4f4f4; padding:40px }
.box { background:#fff; padding:25px; max-width:600px; margin:auto; border-radius:8px }
input, textarea, select, button {
    width:100%; padding:10px; margin-top:10px
}
button { background:#222; color:#fff; border:none; cursor:pointer }
</style>
</head>
<body>

<?php if ($passo == 1): ?>
<!-- PASSO 1 -->
<div class="box">
<h2>Passo 1 – Criar Quiz</h2>
<form method="post">
    <input name="titulo" placeholder="Título do quiz" required>
    <textarea name="descricao" placeholder="Descrição"></textarea>
    <input name="imagem" placeholder="URL da imagem">
    <select name="categoria" required>
        <option>Anime</option>
        <option>Games</option>
        <option>Filmes</option>
        <option>Series</option>
        <option>Livros</option>
        <option>Variados</option>
    </select>
    <button name="criar_quiz">Próximo</button>
</form>
</div>

<?php elseif ($passo == 2): ?>
<!-- PASSO 2 -->
<div class="box">
<h2>Passo 2 – Resultados</h2>
<form method="post">
    <input name="titulo" placeholder="Título do resultado" required>
    <textarea name="descricao"></textarea>
    <input name="imagem" placeholder="URL da imagem">
    <button name="criar_resultado">Adicionar Resultado</button>
</form>

<form method="post">
    <button name="ir_perguntas">Ir para Perguntas</button>
</form>
</div>

<?php elseif ($passo == 3): ?>
<!-- PASSO 3 -->
<div class="box">
<h2>Passo 3 – Perguntas</h2>
<form method="post">
    <input name="texto" placeholder="Digite a pergunta" required>
    <button name="criar_pergunta">Adicionar Pergunta</button>
</form>

<form method="post">
    <button name="ir_respostas">Ir para Respostas</button>
</form>
</div>

<?php elseif ($passo == 4): ?>
<!-- PASSO 4 -->
<?php
$perguntas = $conexao->query(
    "SELECT id, texto FROM personalidade_perguntas
     WHERE personalidade_id = {$_SESSION['personalidade_id']}"
);

$resultados = $conexao->query(
    "SELECT id, titulo FROM personalidade_resultados
     WHERE personalidade_id = {$_SESSION['personalidade_id']}"
);
?>
<div class="box">
<h2>Passo 4 – Respostas</h2>

<form method="post">
    <select name="pergunta_id" required>
        <?php while ($p = $perguntas->fetch_assoc()): ?>
            <option value="<?= $p['id'] ?>"><?= $p['texto'] ?></option>
        <?php endwhile; ?>
    </select>

    <input name="texto" placeholder="Resposta" required>

    <h4>Pontuação</h4>
    <?php while ($r = $resultados->fetch_assoc()): ?>
        <label>
            <?= $r['titulo'] ?>
            <input type="number" name="pontos[<?= $r['id'] ?>]" min="-1" max="3" value="0">
        </label>
    <?php endwhile; ?>

    <button name="criar_resposta">Salvar Resposta</button>
</form>
</div>
<?php endif; ?>

</body>
</html>
