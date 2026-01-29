<?php
include_once('config.php');

/* ===============================
   VALIDAÇÃO DE ID
================================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz_id = (int) $_GET['id'];

/* ===============================
   BUSCAR QUIZ EXISTENTE
================================ */
$stmt = $conexao->prepare("SELECT * FROM quizzes WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz = $result->fetch_assoc();

/* ===============================
   SALVAR ALTERAÇÕES DO QUIZ
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualizar quiz
    if (isset($_POST['salvar_quiz'])) {
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $categoria = $_POST['categoria'];
        $imagem = $_POST['imagem'];

        $stmt = $conexao->prepare("UPDATE quizzes SET titulo=?, descricao=?, categoria=?, imagem=? WHERE id=?");
        $stmt->bind_param("ssssi", $titulo, $descricao, $categoria, $imagem, $quiz_id);
        $stmt->execute();
        header("Location: EditorQuiz.php?id=$quiz_id");
        exit;
    }

    // Atualizar perguntas e respostas
    if (isset($_POST['salvar_perguntas'])) {
    foreach ($_POST['pergunta'] as $pid => $pergunta_texto) {
        // Atualiza pergunta
        $stmt = $conexao->prepare("UPDATE perguntas SET texto=? WHERE id=?");
        $stmt->bind_param("si", $pergunta_texto, $pid);
        $stmt->execute();

        // ID da resposta correta enviada pelo radio
        $resposta_correta_id = isset($_POST['correta'][$pid]) ? (int)$_POST['correta'][$pid] : 0;

        // Atualiza respostas
        foreach ($_POST['resposta_texto'][$pid] as $rid => $resposta_texto) {
            $correta = ($rid == $resposta_correta_id) ? 1 : 0;
            $stmt = $conexao->prepare("UPDATE respostas SET texto=?, correta=? WHERE id=?");
            $stmt->bind_param("sii", $resposta_texto, $correta, $rid);
            $stmt->execute();
        }
    }
    header("Location: EditorQuiz.php?id=$quiz_id");
    exit;
}

}

/* ===============================
   BUSCAR PERGUNTAS EXISTENTES
================================ */
$perguntas = [];
$sql = "
    SELECT p.id AS pergunta_id, p.texto AS pergunta_texto,
           r.id AS resposta_id, r.texto AS resposta_texto, r.correta
    FROM perguntas p
    JOIN respostas r ON r.pergunta_id = p.id
    WHERE p.quizz_id = $quiz_id
    ORDER BY p.id
";
$result = $conexao->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pid = $row['pergunta_id'];
        if (!isset($perguntas[$pid])) {
            $perguntas[$pid] = [
                'texto' => $row['pergunta_texto'],
                'respostas' => []
            ];
        }
        $perguntas[$pid]['respostas'][$row['resposta_id']] = [
            'texto' => $row['resposta_texto'],
            'correta' => $row['correta']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Quiz - <?= htmlspecialchars($quiz['titulo']) ?></title>
    <link rel="stylesheet" href="../Styles/Header.css?v=1">
    <link rel="stylesheet" href="../Styles/Criador.css?v=1">
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

    <div class="container">

        <h2>Editar Quiz: <?= htmlspecialchars($quiz['titulo']) ?></h2>

        <!-- FORMULÁRIO DO QUIZ -->
        <form method="POST">
            <input type="hidden" name="salvar_quiz">

            <label>Título</label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($quiz['titulo']) ?>" required>

            <label>Descrição</label>
            <textarea name="descricao"><?= htmlspecialchars($quiz['descricao']) ?></textarea>

            <label>Categoria</label>
            <input type="text" name="categoria" value="<?= htmlspecialchars($quiz['categoria']) ?>">

            <label>Imagem (URL ou nome do arquivo)</label>
            <input type="text" name="imagem" value="<?= htmlspecialchars($quiz['imagem']) ?>">

            <button type="submit">Salvar Quiz</button>
        </form>

        <!-- FORMULÁRIO DE PERGUNTAS E RESPOSTAS -->
        <h3>Perguntas e Respostas</h3>
        <form method="POST">
            <input type="hidden" name="salvar_perguntas">

            <?php foreach ($perguntas as $pid => $p): ?>
                <div class="pergunta">
                    <h3>Pergunta</h3>
                    <div class="opcao">

                        <input type="text" name="pergunta[<?= $pid ?>]" value="<?= htmlspecialchars($p['texto']) ?>"
                            required>
                            </div>

                        <?php foreach ($p['respostas'] as $rid => $r): ?>
                            <div class="opcao">
                                <input type="text" name="resposta_texto[<?= $pid ?>][<?= $rid ?>]"
                                    value="<?= htmlspecialchars($r['texto']) ?>" required>
                                <label>
                                    <input type="radio" name="correta[<?= $pid ?>]" value="<?= $rid ?>" <?= $r['correta'] ? 'checked' : '' ?>>
                                    Correta
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn-secundario">Salvar Perguntas</button>
        </form>

    </div>

</body>

</html>