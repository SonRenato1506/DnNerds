<?php
include_once("config.php");

$id = (int)$_POST['id'];

/* NerdList */
$sql = "UPDATE nerdlist SET titulo=?, descricao=?, imagem=?, categoria=? WHERE id=?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param(
    "ssssi",
    $_POST['titulo'],
    $_POST['descricao'],
    $_POST['imagem'],
    $_POST['categoria'],
    $id
);
$stmt->execute();

/* Limpa tiers e itens */
$conexao->query("DELETE FROM nerdlist_tiers WHERE nerdlist_id = $id");
$conexao->query("DELETE FROM nerdlist_itens WHERE nerdlist_id = $id");

/* Reinsere tiers */
foreach ($_POST['tier_nome'] as $i=>$nome) {
    $cor = $_POST['tier_cor'][$i];
    $conexao->query("
        INSERT INTO nerdlist_tiers (nerdlist_id, nome, cor, ordem)
        VALUES ($id, '$nome', '$cor', $i)
    ");
}

/* Reinsere itens */
foreach ($_POST['item_nome'] as $i=>$nome) {
    $img = $_POST['item_imagem'][$i];
    $conexao->query("
        INSERT INTO nerdlist_itens (nerdlist_id, nome, imagem)
        VALUES ($id, '$nome', '$img')
    ");
}

header("Location: nerdlist.php?id=$id");
exit;
