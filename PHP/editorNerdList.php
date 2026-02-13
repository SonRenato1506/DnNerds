<?php
include_once("config.php");
include_once("header.php");

/* ===============================
   PROCESSAR EXCLUSÃO
================================ */
if (
    isset($_POST['excluir']) &&
    $_POST['excluir'] === '1' &&
    isset($_POST['id'])
) {
    $id = (int) $_POST['id'];

    $conexao->begin_transaction();
    try {
        $stmt = $conexao->prepare("DELETE FROM nerdlist_itens WHERE nerdlist_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt = $conexao->prepare("DELETE FROM nerdlist_tiers WHERE nerdlist_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt = $conexao->prepare("DELETE FROM nerdlist WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $conexao->commit();
        header("Location: nerdlists.php?msg=excluida");
        exit;
    } catch (Exception $e) {
        $conexao->rollback();
        die("Erro ao excluir NerdList");
    }
}

/* ===============================
   PROCESSAR SALVAR
================================ */
if (isset($_POST['salvar']) && $_POST['salvar'] === '1') {

    $id = (int) $_POST['id'];

    $stmt = $conexao->prepare("
        UPDATE nerdlist 
        SET titulo=?, descricao=?, imagem=?, categoria=?
        WHERE id=?
    ");
    $stmt->bind_param(
        "ssssi",
        $_POST['titulo'],
        $_POST['descricao'],
        $_POST['imagem'],
        $_POST['categoria'],
        $id
    );
    $stmt->execute();

    /* TIERS */
    $idsMantidos = [];

    foreach ($_POST['tier_nome'] as $i => $nome) {
        $cor = $_POST['tier_cor'][$i];
        $tierId = $_POST['tier_id'][$i];

        if ($tierId) {
            $stmt = $conexao->prepare("UPDATE nerdlist_tiers SET nome=?, cor=? WHERE id=?");
            $stmt->bind_param("ssi", $nome, $cor, $tierId);
            $stmt->execute();
            $idsMantidos[] = $tierId;
        } else {
            $stmt = $conexao->prepare("
                INSERT INTO nerdlist_tiers (nerdlist_id,nome,cor,ordem)
                VALUES (?,?,?,?)
            ");
            $ordem = $i + 1;
            $stmt->bind_param("issi", $id, $nome, $cor, $ordem);
            $stmt->execute();
            $idsMantidos[] = $stmt->insert_id;
        }
    }

    if ($idsMantidos) {
        $conexao->query("DELETE FROM nerdlist_tiers 
            WHERE nerdlist_id=$id AND id NOT IN (" . implode(',', $idsMantidos) . ")");
    }

    /* ITENS */
    $idsItens = [];

    foreach ($_POST['item_nome'] as $i => $nome) {
        $img = $_POST['item_imagem'][$i];
        $itemId = $_POST['item_id'][$i];

        if ($itemId) {
            $stmt = $conexao->prepare("
                UPDATE nerdlist_itens SET nome=?, imagem=? WHERE id=?
            ");
            $stmt->bind_param("ssi", $nome, $img, $itemId);
            $stmt->execute();
            $idsItens[] = $itemId;
        } else {
            $stmt = $conexao->prepare("
                INSERT INTO nerdlist_itens (nerdlist_id,nome,imagem)
                VALUES (?,?,?)
            ");
            $stmt->bind_param("iss", $id, $nome, $img);
            $stmt->execute();
            $idsItens[] = $stmt->insert_id;
        }
    }

    if ($idsItens) {
        $conexao->query("DELETE FROM nerdlist_itens 
            WHERE nerdlist_id=$id AND id NOT IN (" . implode(',', $idsItens) . ")");
    }

    header("Location: editorNerdList.php?id=$id");
    exit;
}

if (isset($_POST['salvar']) && $_POST['salvar'] === '1') {

    $id = (int) $_POST['id'];

    // ... UPDATE nerdlist
    // ... UPDATE / INSERT tiers
    // ... UPDATE / INSERT itens

    header("Location: nerdlist.php?id=$id");
    exit;
}

/* ===============================
   CARREGAR DADOS
================================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("NerdList inválida");
}

$id = (int) $_GET['id'];

$stmt = $conexao->prepare("SELECT * FROM nerdlist WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$nerdlist = $stmt->get_result()->fetch_assoc();

if (!$nerdlist)
    die("NerdList não encontrada");

$tiers = $conexao->query("SELECT * FROM nerdlist_tiers WHERE nerdlist_id=$id ORDER BY ordem");
$itens = $conexao->query("SELECT * FROM nerdlist_itens WHERE nerdlist_id=$id");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar NerdList</title>
    <link rel="stylesheet" href="../Styles/Criador.css?v=3">
</head>

<body>

    <div class="container">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="excluir" id="excluirFlag" value="0">

            <label>Título</label>
            <input name="titulo" value="<?= htmlspecialchars($nerdlist['titulo']) ?>" required>

            <label>Descrição</label>
            <textarea name="descricao"><?= htmlspecialchars($nerdlist['descricao']) ?></textarea>

            <label>Imagem</label>
            <input name="imagem" value="<?= htmlspecialchars($nerdlist['imagem']) ?>">

            <label>Categoria</label>
            <select name="categoria">
                <?php foreach (['Animes', 'Games', 'Filmes', 'Series', 'Livros', 'Variados'] as $c): ?>
                    <option <?= $nerdlist['categoria'] == $c ? 'selected' : '' ?>><?= $c ?></option>
                <?php endforeach; ?>
            </select>

            <h3>Tiers</h3>
            <div id="tiers">
                <?php while ($t = $tiers->fetch_assoc()): ?>
                    <div class="item">
                        <input type="hidden" name="tier_id[]" value="<?= $t['id'] ?>">
                        <input name="tier_nome[]" value="<?= htmlspecialchars($t['nome']) ?>" required>
                        <select name="tier_cor[]">
                            <?php
                            $cores = [
                                'Vermelho' => '#e74c3c',
                                'Laranja' => '#e67e22',
                                'Amarelo' => '#f1c40f',
                                'Verde claro' => '#2ecc71',
                                'Verde escuro' => '#27ae60',
                                'Azul claro' => '#3498db',
                                'Azul escuro' => '#2c3e50',
                                'Rosa' => '#fd79a8',
                                'Roxo' => '#9b59b6',
                                'Marrom' => '#8e6e53',
                                'Cinza' => '#7f8c8d',
                                'Branco' => '#ecf0f1'
                            ];

                            foreach ($cores as $nome => $hex):
                                ?>
                                <option value="<?= $hex ?>" <?= $t['cor'] === $hex ? 'selected' : '' ?>>
                                    <?= $nome ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="button" onclick="this.parentElement.remove()">🗑️</button>
                    </div>
                <?php endwhile; ?>
            </div>

            <h3>Itens</h3>
            <div id="itens">
                <?php while ($i = $itens->fetch_assoc()): ?>
                    <div class="item">
                        <input type="hidden" name="item_id[]" value="<?= $i['id'] ?>">
                        <input name="item_nome[]" value="<?= htmlspecialchars($i['nome']) ?>" required>
                        <input name="item_imagem[]" value="<?= htmlspecialchars($i['imagem']) ?>" required>
                        <button type="button" onclick="this.parentElement.remove()">🗑️</button>
                    </div>
                <?php endwhile; ?>
            </div>

            <button type="submit" name="salvar" value="1">💾 Salvar</button>
            <button type="button" onclick="confirmarExclusao()" style="background:#e74c3c;color:#fff">
                🗑️ Excluir
            </button>
        </form>
    </div>

    <script>
        function confirmarExclusao() {
            if (confirm("Excluir definitivamente?")) {
                document.getElementById("excluirFlag").value = "1";
                document.querySelector("form").submit();
            }
        }
    </script>

</body>

</html>