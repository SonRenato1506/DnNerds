<?php
session_start();
require_once __DIR__ . '/config.php';
include_once("header.php");

/* =====================
   INICIALIZAÇÃO
===================== */
if (!isset($_SESSION['passo'])) {
    $_SESSION['passo'] = 1;
}

$passo = $_SESSION['passo'];

/* =====================
   RESET DO QUIZ
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
    <title>Criador de Quiz de Personalidade - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Criador.css?v=2">
</head>

<body>

    <!-- ===================== -->
    <!-- 🧠 CONTEÚDO -->
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
                <button name="criar_resultado">Salvar esse Resultado</button>
            </form>

            <form method="post">
                <button name="ir_perguntas">Ir para Perguntas</button>
            </form>

        <?php elseif ($passo == 3): ?>

            <h2>❓ Passo 3 – Perguntas</h2>

            <form method="post">
                <input name="texto" placeholder="Digite a pergunta" required>
                <button name="criar_pergunta">Salvar essa Pergunta</button>
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

                <h4>Escolha a Pergunta</h4>
                <div class="lista-perguntas">
                    <?php while ($p = $perguntas->fetch_assoc()): ?>
                        <label class="opcao-pergunta">
                            <input type="radio" name="pergunta_id" value="<?= $p['id'] ?>" required>
                            <?= htmlspecialchars($p['texto']) ?>
                            <br>
                        </label>
                    <?php endwhile; ?>
                </div>


                <label>Resposta</label>
                <input name="texto" required>

                <h4>Pontuação</h4>

                <?php while ($r = $resultados->fetch_assoc()): ?>
                    <label>
                        <?= htmlspecialchars($r['titulo']) ?>
                        <input type="number" name="pontos[<?= $r['id'] ?>]" min="-1" max="3" value="0">
                    </label>
                <?php endwhile; ?>

                <button name="criar_resposta">Salvar Resposta</button>
            </form>

            <hr style="margin:30px 0;">

            <a href="criadorPersonalidade.php?reset=1" class="btn-reset">
                ➕ Criar novo quiz de personalidade
            </a>

        <?php endif; ?>

    </div>

    <footer class="footer">
        <div class="footer-container">
            <p>2025 DnNerds — Renato Matos e equipe</p>
        </div>
    </footer>

</body>

</html>