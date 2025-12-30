<?php
include_once('config.php');

if (
    empty($_POST['titulo']) ||
    empty($_POST['descricao']) ||
    empty($_POST['categoria'])
) {
    echo "Dados inválidos.";
    exit;
}

$titulo = trim($_POST['titulo']);
$descricao = trim($_POST['descricao']);
$categoria = trim($_POST['categoria']);
$imagem = trim($_POST['imagem'] ?? 'nerdlistdefault.jpg');

/* ===============================
   INSERE NERDLIST
================================ */
$stmt = $conexao->prepare("
    INSERT INTO nerdlist (titulo, descricao, categoria, imagem)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("ssss", $titulo, $descricao, $categoria, $imagem);
$stmt->execute();

$nerdlist_id = $stmt->insert_id;

/* ===============================
   TIERS PADRÃO
================================ */
$tiers = [
    ['S', '#ff4757'],
    ['A', '#ffa502'],
    ['B', '#2ed573'],
    ['C', '#1e90ff'],
    ['D', '#57606f']
];

$stmtTier = $conexao->prepare("
    INSERT INTO nerdlist_tiers (nerdlist_id, nome, cor)
    VALUES (?, ?, ?)
");

foreach ($tiers as $tier) {
    $stmtTier->bind_param("iss", $nerdlist_id, $tier[0], $tier[1]);
    $stmtTier->execute();
}

/* ===============================
   REDIRECIONA
================================ */
header("Location: nerdlist.php?id=" . $nerdlist_id);
exit;
