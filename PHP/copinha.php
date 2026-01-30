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
    <link rel="stylesheet" href="../Styles/copinha.css?v=3">
</head>

<body>

    <header>
        <nav class="navbar">
            <h2 class="title">
                DnNerds <img src="../Imagens/anfitriao.png?v=2" alt="">
            </h2>

            <ul>
                <li><a href="Noticias.php">Notícias</a></li>
                <li><a href="nerdlists.php">NerdList</a></li>
                <li><a href="Quizzes.php">Quizzes</a></li>
                <li><a href="copinhas.php" class="ativo">Copinhas</a></li>
                <li>
                    <?php if (isset($copinha_id)): ?>
                        <a href="editorCopinha.php?id=<?= $copinha_id ?>">Editar</a>
                    <?php else: ?>
                        <a href="EditorCopinha.php">Editor</a>
                    <?php endif; ?>
                </li>

            </ul>

            <button class="btn-navbar">
                <a href="FazerLogin.php">Fazer Login</a>
            </button>
        </nav>
    </header>

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
        const TITULO = <?= json_encode($copinha['titulo']) ?>;
        const ITENS = <?= json_encode($itens) ?>;

        let fila = [];
        let vencedores = [];
        let fase = 'primeira';
        let confrontosIniciais = 0;
        let indiceInicial = 0;

        /* UTIL */
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

        /* MIDIA */
        function isYouTube(url) {
            return url.includes('youtube.com') || url.includes('youtu.be');
        }

        function getEmbed(url) {
            if (url.includes('youtu.be'))
                return url.split('youtu.be/')[1].split('?')[0];
            return url.split('watch?v=')[1].split('&')[0];
        }

        function renderMidia(p) {
            return isYouTube(p.imagem)
                ? `<iframe src="https://www.youtube.com/embed/${getEmbed(p.imagem)}" allowfullscreen></iframe><h2>${p.nome}</h2>`
                : `<img src="${p.imagem}"><h2>${p.nome}</h2>`;
        }

        /* INICIAR */
        function iniciar() {
            fila = [...ITENS];
            shuffle(fila);

            vencedores = [];
            indiceInicial = 0;

            const pot = maiorPotenciaDe2(fila.length);
            confrontosIniciais = fila.length - pot;

            fase = confrontosIniciais > 0 ? 'primeira' : 'mata';
            c = 1
            console.log(fila.length)
            console.log(pot)
            console.log(confrontosIniciais)


            document.getElementById('titulo').innerHTML = `
        ${TITULO}
        <small>${fase === 'primeira' ? `Rodada com ${String(fila.length)}` : 'Mata-mata'}</small>
    
    `;

            render();
        }

        /* RENDER */
        function render() {


            if (fila.length === 1) {
                mostrarCampeao(fila[0]);
                return;
            }

            // terminou rodada inicial
            if (fase === 'primeira' && vencedores.length === confrontosIniciais) {
                const resto = fila.slice(confrontosIniciais * 2);

                // junta quem não lutou + vencedores
                fila = [...resto, ...vencedores];

                // 🔀 EMBARALHA ANTES DO MATA-MATA
                shuffle(fila);

                vencedores = [];
                fase = 'mata';

                document.querySelector('#titulo small').innerText = 'Mata-mata';
                c = 1;
                totalConfrontos = fila.length / 2;
            }


            let p1, p2;

            if (fase === 'primeira') {
                document.getElementById('rodada').innerHTML = String(c) + "/" + confrontosIniciais
                c++;
                // ORDEM FIXA: 1x2, 3x4, 5x6...
                p1 = fila[indiceInicial];
                p2 = fila[indiceInicial + 1];
            } else {
                document.getElementById('rodada').innerHTML = `${c}/${totalConfrontos}`;
                c++
                // MATA-MATA: DE TRÁS PRA FRENTE
                p1 = fila.pop();
                p2 = fila.pop();
            }


            document.getElementById('btn1').innerHTML = renderMidia(p1);
            document.getElementById('btn2').innerHTML = renderMidia(p2);

            document.getElementById('btn1').onclick = () => escolher(p1);
            document.getElementById('btn2').onclick = () => escolher(p2);
        }

        /* ESCOLHER */
        function escolher(v) {
            vencedores.push(v);

            if (fase === 'primeira') {
                indiceInicial += 2;
            }

            if (fase === 'mata' && vencedores.length * 2 === fila.length + vencedores.length * 2) {
                fila = [...vencedores];
                vencedores = [];
                totalConfrontos = fila.length / 2;
                c = 1;
            }

            render();
        }

        /* CAMPEÃO */
        function mostrarCampeao(c) {
            document.getElementById('batalha').style.display = 'none';
            const div = document.getElementById('campeao');
            div.style.display = 'block';

            div.innerHTML = `
        <h1>🏆 CAMPEÃO 🏆</h1>
        <h2>${c.nome}</h2>
        ${renderMidia(c)}
    `;
        }

        /* RESET */
        function reiniciar() {
            document.getElementById('campeao').style.display = 'none';
            document.getElementById('batalha').style.display = 'flex';
            iniciar();
        }



        iniciar();
    </script>


</body>

</html>