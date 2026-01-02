<?php
include_once('config.php');


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "NerdList não encontrada.";
    exit;
}

$nerdlist_id = (int) $_GET['id'];

$sqlList = "SELECT * FROM nerdlist WHERE id = $nerdlist_id LIMIT 1";
$resultList = $conexao->query($sqlList);

if (!$resultList || $resultList->num_rows === 0) {
    echo "NerdList não encontrada.";
    exit;
}

$nerdlist = $resultList->fetch_assoc();

$sqlTiers = "
    SELECT * FROM nerdlist_tiers
    WHERE nerdlist_id = $nerdlist_id
    ORDER BY id
";
$tiers = $conexao->query($sqlTiers);

$sqlItens = "
    SELECT * FROM nerdlist_itens
    WHERE nerdlist_id = $nerdlist_id
";
$itens = $conexao->query($sqlItens);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($nerdlist['titulo']) ?> - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Header.css?v=28">
    <link rel="stylesheet" href="../Styles/NerdList.css">


</head>

<body>

    <header>
        <nav class="navbar">
            <h2 class="title">
                DnNerds <img src="../Imagens/anfitriao.png?v=2" alt="DnNerds">
            </h2>
            <ul>
                <li><a href="Noticias.php">Notícias</a></li>
                <li><a href="nerdlists.php">NerdList</a></li>
                <li><a href="Quizzes.php">Quizzes</a></li>
                <li><a href="#">Editor</a></li>
            </ul>
            <button class="btn-navbar">
                <a href="FazerLogin.php">Fazer Login</a>
            </button>
        </nav>
    </header>

    <main class="tierlist-container">

        <h1><?= htmlspecialchars($nerdlist['titulo']) ?></h1>
        <p><?= htmlspecialchars($nerdlist['descricao']) ?></p>

        <!-- TIERS -->
        <?php while ($tier = $tiers->fetch_assoc()): ?>
            <div class="tier">
                <div class="tier-title" style="background: <?= htmlspecialchars($tier['cor']) ?>" contenteditable="true"
                    data-tier-id="<?= $tier['id'] ?>" onblur="salvarNomeTier(this)">
                    <?= htmlspecialchars($tier['nome']) ?>
                </div>

                <div class="tier-drop" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
            </div>
        <?php endwhile; ?>

        <!-- ITENS -->
        <h2 style="margin-top:30px;">Arraste os itens</h2>

        <div class="itens-pool" ondrop="drop(event)" ondragover="allowDrop(event)">
            <?php while ($item = $itens->fetch_assoc()): ?>
                <div class="item" draggable="true" ondragstart="drag(event)" id="item<?= $item['id'] ?>">
                    <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>"
                        title="<?= htmlspecialchars($item['nome']) ?>">
                </div>
            <?php endwhile; ?>
        </div>

    </main>

    <script>
        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("text/plain", ev.target.id);
        }

        function drop(ev) {
            ev.preventDefault();

            const id = ev.dataTransfer.getData("text/plain");
            const item = document.getElementById(id);

            let destino = ev.target;

            // garante que sempre seja um container válido
            while (destino &&
                !destino.classList.contains('tier-drop') &&
                !destino.classList.contains('itens-pool')) {
                destino = destino.parentElement;
            }

            if (destino) {
                destino.appendChild(item);
            }
        }

        document.addEventListener('dragover', function (e) {
            const margem = 100; // distância da borda
            const velocidade = 10;

            if (e.clientY < margem) {
                window.scrollBy(0, -velocidade);
            } else if (window.innerHeight - e.clientY < margem) {
                window.scrollBy(0, velocidade);
            }
        });
    </script>




    <footer class="footer">
        <div class="footer-container">
            <p>2025 DnNerds — Renato Matos e equipe</p>
        </div>
    </footer>

</body>

</html>