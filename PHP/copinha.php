<?php
include_once('config.php');
include_once("header.php");

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
    <link rel="stylesheet" href="../Styles/copinha.css?v=4">
</head>

<body>

    <main>

        <h1 id="titulo"></h1>
        <h2 id="rodada"></h2>

        <div class="batalha" id="batalha">
            <button id="btn1"></button>
            <h1>VS</h1>
            <button id="btn2"></button>
        </div>

        <div id="campeao" style="display:none;"></div>

        <div style="display:flex; justify-content:center; gap:20px; margin-top:30px;">
            <button onclick="reiniciar()">🔁 Refazer</button>
            <a href="copinhas.php"><button>⬅️ Voltar</button></a>
        </div>

    </main>

    <script>
        /* ===============================
           DADOS VINDOS DO PHP
        ================================ */
        const TITULO = <?= json_encode($copinha['titulo']) ?>;
        const ITENS = <?= json_encode($itens) ?>;

        /* ===============================
           VARIÁVEIS
        ================================ */
        let fila = [];
        let vencedores = [];
        let fase = 'primeira';
        let confrontosIniciais = 0;
        let totalConfrontos = 0;
        let indice = 0;
        let rodadaAtual = 1;
        let bloqueado = false;


        /* ===============================
           UTIL
        ================================ */
        function shuffle(arr) {
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]];
            }
        }

        function maiorPotenciaDe2(n) {
            let p = 1;
            while (p * 2 <= n) p *= 2;
            return p;
        }

        /* ===============================
           MÍDIA
        ================================ */
        function isYouTube(url) {
            return url.includes('youtube.com') || url.includes('youtu.be');
        }

        function getEmbed(url) {
            if (url.includes('youtu.be')) {
                return url.split('youtu.be/')[1].split('?')[0];
            }
            return url.split('watch?v=')[1].split('&')[0];
        }

        function renderMidia(item) {
            if (isYouTube(item.imagem)) {
                return `
            <iframe src="https://www.youtube.com/embed/${getEmbed(item.imagem)}" allowfullscreen></iframe>
            <h2>${item.nome}</h2>
        `;
            }
            return `
        <img src="${item.imagem}?v=2">
        <h2>${item.nome}</h2>
    `;
        }

        /* ===============================
           INICIAR
        ================================ */
        function iniciar() {
            fila = [...ITENS];
            shuffle(fila);

            vencedores = [];
            indice = 0;
            rodadaAtual = 1;

            document.getElementById('rodada').style.display = 'block';

            const pot = maiorPotenciaDe2(fila.length);
            confrontosIniciais = fila.length - pot;

            fase = confrontosIniciais > 0 ? 'primeira' : 'mata';
            totalConfrontos = fase === 'primeira'
                ? confrontosIniciais
                : Math.floor(fila.length / 2);

            document.getElementById('titulo').innerHTML = `
        ${TITULO}
        <small>
            ${fase === 'primeira'
                    ? `Rodada inicial (${fila.length})`
                    : 'Mata-mata'}
        </small>
    `;

            document.getElementById('batalha').style.display = 'flex';
            document.getElementById('campeao').style.display = 'none';

            render();
        }

        /* ===============================
           RENDER
        ================================ */
        function render() {

            // SE JÁ TEM CAMPEÃO, PARA TUDO
            if (fila.length === 1) {
                mostrarCampeao(fila[0]);
                return;
            }

            // FIM DA RODADA ATUAL
            if (indice >= totalConfrontos * 2) {

                if (fase === 'primeira') {
                    const restantes = fila.slice(totalConfrontos * 2);
                    fila = [...vencedores, ...restantes];
                    fase = 'mata';
                } else {
                    fila = [...vencedores];
                }

                vencedores = [];
                indice = 0;
                rodadaAtual = 1;
                totalConfrontos = Math.floor(fila.length / 2);

                // SE APÓS AJUSTE SOBRAR 1, FINALIZA
                if (fila.length === 1) {
                    mostrarCampeao(fila[0]);
                    return;
                }
            }

            document.getElementById('rodada').innerText =
                `${rodadaAtual}/${totalConfrontos}`;

            const p1 = fila[indice];
            const p2 = fila[indice + 1];

            document.getElementById('btn1').innerHTML = renderMidia(p1);
            document.getElementById('btn2').innerHTML = renderMidia(p2);

            document.getElementById('btn1').onclick = () => escolher(p1);
            document.getElementById('btn2').onclick = () => escolher(p2);
        }


        /* ===============================
           ESCOLHA
        ================================ */
        function escolher(vencedor) {
            // IMPEDE CLIQUE DUPLO
            if (bloqueado) return;

            bloqueado = true;

            // Desativa botões visualmente
            document.getElementById('btn1').disabled = true;
            document.getElementById('btn2').disabled = true;

            vencedores.push(vencedor);
            indice += 2;
            rodadaAtual++;

            // DELAY DE 2 SEGUNDOS
            setTimeout(() => {
                bloqueado = false;

                // Reativa botões
                document.getElementById('btn1').disabled = false;
                document.getElementById('btn2').disabled = false;

                render();
            }, 500);
        }


        /* ===============================
           CAMPEÃO
        ================================ */
        function mostrarCampeao(campeao) {
            document.getElementById('batalha').style.display = 'none';
            document.getElementById('rodada').style.display = 'none';
            const div = document.getElementById('campeao');
            div.style.display = 'block';

            div.innerHTML = `
        <h1>🏆 CAMPEÃO 🏆</h1>
        ${renderMidia(campeao)}
    `;
        }

        /* ===============================
           REINICIAR
        ================================ */
        function reiniciar() {
            iniciar();
        }

        /* ===============================
           START
        ================================ */
        iniciar();
    </script>




</body>

</html>