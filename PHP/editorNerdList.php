<?php
include_once("config.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("NerdList inválida");
}

$id = (int) $_GET['id'];

/* NerdList */
$sql = "SELECT * FROM nerdlist WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$nerdlist = $stmt->get_result()->fetch_assoc();

if (!$nerdlist) die("NerdList não encontrada");

/* Tiers */
$tiers = $conexao->query("
    SELECT * FROM nerdlist_tiers 
    WHERE nerdlist_id = $id 
    ORDER BY ordem
");

/* Itens */
$itens = $conexao->query("
    SELECT * FROM nerdlist_itens 
    WHERE nerdlist_id = $id
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar NerdList - <?= htmlspecialchars($nerdlist['titulo']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Header.css">
    <link rel="stylesheet" href="../Styles/Criador.css">
</head>

<body>

<header>
    <nav class="navbar">
        <h2 class="title">DnNerds</h2>
        <ul>
            <li><a href="nerdlists.php">NerdList</a></li>
            <li><a href="Editor.php" class="ativo">Editor</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h2>✏️ Editar NerdList</h2>

    <form action="salvarEditorNerdList.php" method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">

        <label>Título</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($nerdlist['titulo']) ?>" required>

        <label>Descrição</label>
        <textarea name="descricao"><?= htmlspecialchars($nerdlist['descricao']) ?></textarea>

        <label>Imagem (URL)</label>
        <input type="text" name="imagem" value="<?= htmlspecialchars($nerdlist['imagem']) ?>">

        <label>Categoria</label>
        <select name="categoria" required>
            <?php
            $cats = ['Animes','Games','Filmes','Series','Livros','Variados'];
            foreach ($cats as $c):
            ?>
                <option value="<?= $c ?>" <?= $nerdlist['categoria']==$c?'selected':'' ?>>
                    <?= $c ?>
                </option>
            <?php endforeach; ?>
        </select>

        <hr>

        <h3>🏷️ Tiers</h3>
        <div id="tiers">

            <?php while ($t = $tiers->fetch_assoc()): ?>
                <div class="item">
                    <input type="hidden" name="tier_id[]" value="<?= $t['id'] ?>">

                    <label>Nome</label>
                    <input type="text" name="tier_nome[]" value="<?= htmlspecialchars($t['nome']) ?>" required>

                    <label>Cor</label>
                    <select name="tier_cor[]">
                        <?php
                        $cores = [
                            'Vermelho'=>'#e74c3c','Laranja'=>'#e67e22','Amarelo'=>'#f1c40f',
                            'Verde claro'=>'#2ecc71','Verde escuro'=>'#27ae60',
                            'Azul claro'=>'#3498db','Azul escuro'=>'#2c3e50',
                            'Rosa'=>'#fd79a8','Roxo'=>'#9b59b6',
                            'Marrom'=>'#8e6e53','Cinza'=>'#7f8c8d','Branco'=>'#ecf0f1'
                        ];
                        foreach ($cores as $nome=>$hex):
                        ?>
                            <option value="<?= $hex ?>" <?= $t['cor']==$hex?'selected':'' ?>>
                                <?= $nome ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="button" onclick="removerLinha(this)">🗑️</button>
                </div>
            <?php endwhile; ?>

        </div>

        <button type="button" onclick="addTier()">➕ Novo Tier</button>

        <hr>

        <h3>🎮 Itens</h3>
        <div id="itens">

            <?php while ($i = $itens->fetch_assoc()): ?>
                <div class="item">
                    <input type="hidden" name="item_id[]" value="<?= $i['id'] ?>">

                    <label>Nome</label>
                    <input type="text" name="item_nome[]" value="<?= htmlspecialchars($i['nome']) ?>" required>

                    <label>Imagem (URL)</label>
                    <input type="text" name="item_imagem[]" value="<?= htmlspecialchars($i['imagem']) ?>" required>

                    <button type="button" onclick="removerLinha(this)">🗑️</button>
                </div>
            <?php endwhile; ?>

        </div>

        <button type="button" onclick="addItem()">➕ Novo Item</button>

        <br><br>
        <button type="submit">💾 Salvar Alterações</button>
    </form>
</div>

<script>
function removerLinha(btn) {
    btn.parentElement.remove();
}

function addTier() {
    document.getElementById("tiers").insertAdjacentHTML("beforeend", `
        <div class="item">
            <input type="hidden" name="tier_id[]" value="">

            <label>Nome</label>
            <input type="text" name="tier_nome[]" required>

            <label>Cor</label>
            <select name="tier_cor[]">
                <option value="#e74c3c">Vermelho</option>
                <option value="#e67e22">Laranja</option>
                <option value="#f1c40f">Amarelo</option>
                <option value="#2ecc71">Verde claro</option>
                <option value="#27ae60">Verde escuro</option>
                <option value="#3498db">Azul claro</option>
                <option value="#2c3e50">Azul escuro</option>
                <option value="#fd79a8">Rosa</option>
                <option value="#9b59b6">Roxo</option>
                <option value="#8e6e53">Marrom</option>
                <option value="#7f8c8d">Cinza</option>
                <option value="#ecf0f1">Branco</option>
            </select>
        </div>
    `);
}

function addItem() {
    document.getElementById("itens").insertAdjacentHTML("beforeend", `
        <div class="item">
            <input type="hidden" name="item_id[]" value="">
            <label>Nome</label>
            <input type="text" name="item_nome[]" required>
            <label>Imagem</label>
            <input type="text" name="item_imagem[]" required>
        </div>
    `);
}
</script>

</body>
</html>
