<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acesso inválido.");
}

$titulo    = $_POST['titulo'];
$descricao = $_POST['descricao'];
$categoria = $_POST['categoria'];
$imagem    = $_POST['imagem'] ?? null;
$itens     = $_POST['itens'] ?? [];

if (empty($itens)) {
    die("Adicione ao menos um item ao ranking.");
}

/* ===============================
   SALVAR QUIZ RANK
================================ */
$stmt = $conexao->prepare(
    "INSERT INTO quizzes_rank (titulo, descricao, categoria, imagem)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $titulo, $descricao, $categoria, $imagem);
$stmt->execute();

$quiz_id = $stmt->insert_id;

/* ===============================
   SALVAR ITENS
================================ */
$stmtItem = $conexao->prepare(
    "INSERT INTO quiz_rank_itens (quiz_id, posicao, nome, dica)
     VALUES (?, ?, ?, ?)"
);

foreach ($itens as $item) {
    $posicao = (int)$item['posicao'];
    $nome    = trim($item['nome']);
    $dica    = !empty($item['dica']) ? trim($item['dica']) : null;

    $stmtItem->bind_param("iiss", $quiz_id, $posicao, $nome, $dica);
    $stmtItem->execute();
}

header("Location: quizRank.php?id=$quiz_id");
exit;
