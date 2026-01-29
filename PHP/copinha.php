<?php
include_once('config.php');

if (!isset($_GET['id'])) {
    header("Location: copinhas.php");
    exit;
}

$copinha_id = (int) $_GET['id'];

/* BUSCA COPINHA */
$sqlCopinha = "SELECT * FROM copinha WHERE id = $copinha_id";
$resCopinha = $conexao->query($sqlCopinha);
$copinha = $resCopinha->fetch_assoc();

if (!$copinha) {
    header("Location: copinhas.php");
    exit;
}

/* BUSCA ITENS */
$sqlItens = "SELECT id, nome, imagem FROM item_copinha WHERE copinha_id = $copinha_id";
$resItens = $conexao->query($sqlItens);

$itens = [];
while ($row = $resItens->fetch_assoc()) {
    $itens[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($copinha['titulo']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Header.css">
    <link rel="stylesheet" href="../Styles/copinha.css?v=2">
</head>

<body>

    <header>
        <nav class="navbar">
            <h2 class="title">
                DnNerds <img src="../Imagens/anfitriao.png" alt="">
            </h2>

            <ul>
                <li><a href="Noticias.php">Notícias</a></li>
                <li><a href="nerdlists.php">NerdList</a></li>
                <li><a href="Quizzes.php">Quizzes</a></li>
                <li><a href="copinhas.php" class="ativo">Copinhas</a></li>
                <li><a href="EditorCopinha.php?id=<?= (int) $_GET['id'] ?>">Editor</a></li>
            </ul>

            <button class="btn-navbar">
                <a href="FazerLogin.php">Fazer Login</a>
            </button>
        </nav>
    </header>

    <main>

        <h1 id="titulo"></h1>

        <div class="batalha" id="batalha">
            <button class="opcao1" id="btn1"></button>
            <h1>VS</h1>
            <button class="opcao2" id="btn2"></button>
        </div>

        <div class="campeao" id="campeao" style="display:none;"></div>

        <div style="display:flex; justify-content:center; gap:20px; margin-top:30px;">
            <button onclick="reiniciar()">🔁 Refazer</button>
            <a href="copinhas.php"><button>⬅️ Voltar</button></a>
        </div>

    </main>

    <script>
        /* ===============================
           DADOS
        ================================ */
        const COPINHA_TITULO = <?= json_encode($copinha['titulo']) ?>;
        const PARTICIPANTES = <?= json_encode($itens) ?>;

        /* ===============================
           VARIÁVEIS
        ================================ */
        let jogadores = [];
        let rodadaAtual = [];
        let filaAjuste = [];
        let fase = 'ajuste';

        /* ===============================
           UTIL
        ================================ */
        function shuffle(arr) {
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]];
            }
        }

        function ehPotenciaDe2(n) {
            return (n & (n - 1)) === 0;
        }

        /* ===============================
           YOUTUBE / IMAGEM
        ================================ */
        function isYouTube(url) {
            return url.includes('youtube.com') || url.includes('youtu.be');
        }

        function getYouTubeEmbed(url) {
            let id = '';

            if (url.includes('youtu.be')) {
                id = url.split('youtu.be/')[1];
            } else if (url.includes('watch?v=')) {
                id = url.split('watch?v=')[1];
            }

            return id ? `https://www.youtube.com/embed/${id.split('&')[0]}` : '';
        }

        function renderMidia(p) {
            if (isYouTube(p.imagem)) {
                return `
            <iframe 
                src="${getYouTubeEmbed(p.imagem)}"
                allowfullscreen
            ></iframe>
            <h2>${p.nome}</h2>
        `;
            } else {
                return `
            <img src="${p.imagem}" alt="${p.nome}">
            <h2>${p.nome}</h2>
        `;
            }
        }

        /* ===============================
           TEXTO DA RODADA
        ================================ */
        function nomeDaRodada(qtd) {
            if (qtd <= 16 && qtd >= 9) return 'Oitavas de Final';
            if (qtd <= 8 && qtd >= 5) return 'Quartas de Final';
            if (qtd <= 4 && qtd >= 3) return 'Semifinal';
            if (qtd === 2) return 'Final';
            return `Rodada inicial (${qtd} participantes)`;
        }

        /* ===============================
           INICIAR
        ================================ */
        function iniciar() {
            jogadores = [...PARTICIPANTES];
            shuffle(jogadores);

            fase = ehPotenciaDe2(jogadores.length) ? 'mata-mata' : 'ajuste';

            document.getElementById('titulo').innerHTML = `
        ${COPINHA_TITULO}
        (<small id="rodada"></small>)
    `;

            rodadaAtual = [...jogadores];

            if (fase === 'ajuste') iniciarRodadaAjuste();

            atualizarRodada();
            render();
        }

        /* ===============================
           AJUSTE
        ================================ */
        function iniciarRodadaAjuste() {
            filaAjuste = [...rodadaAtual];
            shuffle(filaAjuste);
            rodadaAtual = [];
        }

        /* ===============================
           RENDER
        ================================ */
        function render() {

            if (fase === 'mata-mata' && rodadaAtual.length <= 1) {
                mostrarCampeao(rodadaAtual[0]);
                return;
            }

            atualizarRodada();

            let p1, p2;

            if (fase === 'ajuste') {
                if (filaAjuste.length < 2) {

                    rodadaAtual = [...rodadaAtual, ...filaAjuste];
                    filaAjuste = [];

                    if (ehPotenciaDe2(rodadaAtual.length)) {
                        fase = 'mata-mata';
                        shuffle(rodadaAtual);
                    } else {
                        iniciarRodadaAjuste();
                    }

                    render();
                    return;
                }

                p1 = filaAjuste.shift();
                p2 = filaAjuste.shift();

            } else {
                p1 = rodadaAtual.shift();
                p2 = rodadaAtual.shift();
            }

            const btn1 = document.getElementById('btn1');
            const btn2 = document.getElementById('btn2');

            btn1.innerHTML = renderMidia(p1);
            btn2.innerHTML = renderMidia(p2);

            btn1.onclick = () => escolher(p1);
            btn2.onclick = () => escolher(p2);
        }

        /* ===============================
           ESCOLHER
        ================================ */
        function escolher(vencedor) {
            rodadaAtual.push(vencedor);
            render();
        }

        /* ===============================
           RODADA
        ================================ */
        function atualizarRodada() {
            const total = fase === 'ajuste'
                ? filaAjuste.length + rodadaAtual.length
                : rodadaAtual.length;

            document.getElementById('rodada').innerText = nomeDaRodada(total);
        }

        /* ===============================
           CAMPEÃO
        ================================ */
        function mostrarCampeao(c) {
            document.getElementById('batalha').style.display = 'none';
            const div = document.getElementById('campeao');
            div.style.display = 'block';

            let midia = isYouTube(c.imagem)
                ? `<iframe src="${getYouTubeEmbed(c.imagem)}" allowfullscreen></iframe>`
                : `<img src="${c.imagem}">`;

           

            div.innerHTML = `
        <h1>🏆 CAMPEÃO 🏆</h1>
        <h2>${c.nome}</h2>
        ${midia}
    `;
        }

        /* ===============================
           REINICIAR
        ================================ */
        function reiniciar() {
            document.getElementById('campeao').style.display = 'none';
            document.getElementById('batalha').style.display = 'flex';
            iniciar();
        }

        /* START */
        iniciar();
    </script>

</body>

</html>