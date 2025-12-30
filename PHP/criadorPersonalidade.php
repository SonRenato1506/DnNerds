<?php
session_start();
require_once __DIR__ . '/config.php';

/* =====================
   INICIALIZAÇÃO DO PASSO
===================== */
if (!isset($_SESSION['passo'])) {
    $_SESSION['passo'] = 1;
}

$passo = $_SESSION['passo'];

/* =====================
   RESET MANUAL (opcional)
===================== */
if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: criadorPersonalidade.php");
    exit;
}

/* =====================
   PROCESSAMENTOS
===================== */

/* PASSO 1 – CRIAR QUIZ */
if (isset($_POST['criar_quiz'])) {

    $stmt = $conexao->prepare(
        "INSERT INTO personalidade (titulo, descricao, imagem, categoria)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssss",
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

/* PASSO 2 – RESULTADOS */
if (isset($_POST['criar_resultado'])) {

    $stmt = $conexao->prepare(
        "INSERT INTO personalidade_resultados
         (personalidade_id, titulo, descricao, imagem)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "isss",
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

/* PASSO 3 – PERGUNTAS */
if (isset($_POST['criar_pergunta'])) {

    $stmt = $conexao->prepare(
        "INSERT INTO personalidade_perguntas (personalidade_id, texto)
         VALUES (?, ?)"
    );
    $stmt->bind_param(
        "is",
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

/* PASSO 4 – RESPOSTAS */
if (isset($_POST['criar_resposta'])) {

    $stmt = $conexao->prepare(
        "INSERT INTO personalidade_respostas (pergunta_id, texto)
         VALUES (?, ?)"
    );
    $stmt->bind_param(
        "is",
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
        $stmt->bind_param(
            "iii",
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
<title>Criador de Quiz - DnNerds</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../Styles/Header.css">
<link rel="stylesheet" href="../Styles/Criador.css">
</head>

<body>

<!-- ===================== -->
<!-- 🔥 HEADER DNNERDS -->
<!-- ===================== -->
<header>
    <nav class="navbar">
        <h2 class="title">
            DnNerds <img src="../Imagens/anfitriao.png?v=2" alt="DnNerds">
        </h2>
        <ul>
            <li><a href="Noticias.php">Notícias</a></li>
            <li><a href="nerdlists.php">NerdList</a></li>
            <li><a href="Quizzes.php">Quizzes</a></li>
            <li><a href="#">IA</a></li>
        </ul>
        <button class="btn-navbar">
            <a href="FazerLogin.php">Fazer Login</a>
        </button>
    </nav>
</header>

<!-- ===================== -->
<!-- 🧠 CONTAINER -->
<!-- ===================== -->
<div class="container">

<?php if ($passo == 1): ?>

<h2>🧠 Passo 1 – Criar Quiz</h2>
<form method="post">
    <label>Título</label>
    <input name="titulo" required>

    <label>Descrição</label>
    <textarea name="descricao"></textarea>

    <label>Imagem</label>
    <input name="imagem">

    <label>Categoria</label>
    <select name="categoria" required>
        <option value="">Selecione</option>
        <option>Anime</option>
        <option>Games</option>
        <option>Filmes</option>
        <option>Series</option>
        <option>Livros</option>
        <option>Variados</option>
    </select>

    <button name="criar_quiz">Próximo</button>
</form>

<?php elseif ($passo == 2): ?>

<h2>🎯 Passo 2 – Resultados</h2>

<form method="post">
    <input name="titulo" placeholder="Título do resultado" required>
    <textarea name="descricao" placeholder="Descrição"></textarea>
    <input name="imagem" placeholder="Imagem">
    <button name="criar_resultado">Adicionar Resultado</button>
</form>

<form method="post">
    <button name="ir_perguntas">Ir para Perguntas</button>
</form>

<?php elseif ($passo == 3): ?>

<h2>❓ Passo 3 – Perguntas</h2>

<form method="post">
    <input name="texto" placeholder="Digite a pergunta" required>
    <button name="criar_pergunta">Adicionar Pergunta</button>
</form>

<form method="post">
    <button name="ir_respostas">Ir para Respostas</button>
</form>

<?php elseif ($passo == 4): ?>

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

<h2>📝 Passo 4 – Respostas</h2>

<form method="post">

    <label>Pergunta</label>
    <select name="pergunta_id" required>
        <?php while ($p = $perguntas->fetch_assoc()): ?>
            <option value="<?= $p['id'] ?>"><?= $p['texto'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Resposta</label>
    <input name="texto" required>

    <h4>Pontuação</h4>

    <?php while ($r = $resultados->fetch_assoc()): ?>
        <label>
            <?= $r['titulo'] ?>
            <input type="number" name="pontos[<?= $r['id'] ?>]" min="-1" max="3" value="0">
        </label>
    <?php endwhile; ?>

    <button name="criar_resposta">Salvar Resposta</button>
</form>

<?php endif; ?>

</div>

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
