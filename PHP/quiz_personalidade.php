<?php
include_once('config.php');

/* ===============================
   VALIDAÇÃO DO ID
================================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz_id = (int) $_GET['id'];

/* ===============================
   QUIZ PRINCIPAL
================================ */
$sqlQuiz = "SELECT * FROM personalidade WHERE id = $quiz_id LIMIT 1";
$resultQuiz = $conexao->query($sqlQuiz);

if (!$resultQuiz || $resultQuiz->num_rows === 0) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz = $resultQuiz->fetch_assoc();

/* ===============================
   RESULTADOS POSSÍVEIS
================================ */
$sqlResultados = "
    SELECT * FROM personalidade_resultados
    WHERE personalidade_id = $quiz_id
";
$resultResultados = $conexao->query($sqlResultados);

$resultados = [];
if ($resultResultados && $resultResultados->num_rows > 0) {
    while ($r = $resultResultados->fetch_assoc()) {
        $resultados[$r['id']] = $r;
    }
}

/* ===============================
   PERGUNTAS E RESPOSTAS
================================ */
$sqlPerguntas = "
    SELECT 
        p.id    AS pergunta_id,
        p.texto AS pergunta_texto,
        r.id    AS resposta_id,
        r.texto AS resposta_texto
    FROM personalidade_perguntas p
    JOIN personalidade_respostas r ON r.pergunta_id = p.id
    WHERE p.personalidade_id = $quiz_id
    ORDER BY p.id
";

$resultPerguntas = $conexao->query($sqlPerguntas);

/* ===============================
   ORGANIZAÇÃO DOS DADOS
================================ */
$perguntas = [];

if ($resultPerguntas && $resultPerguntas->num_rows > 0) {
    while ($row = $resultPerguntas->fetch_assoc()) {

        $pid = $row['pergunta_id'];

        if (!isset($perguntas[$pid])) {
            $perguntas[$pid] = [
                'id' => $pid,
                'texto' => $row['pergunta_texto'],
                'respostas' => []
            ];
        }

        /* Pontuação da resposta */
        $sqlPont = "
            SELECT resultado_id, pontos
            FROM personalidade_respostas_pontuacao
            WHERE resposta_id = {$row['resposta_id']}
        ";
        $pontData = $conexao->query($sqlPont);

        $pontos = [];
        if ($pontData && $pontData->num_rows > 0) {
            while ($p = $pontData->fetch_assoc()) {
                $pontos[$p['resultado_id']] = (int) $p['pontos'];
            }
        }

        $perguntas[$pid]['respostas'][] = [
            'id' => $row['resposta_id'],
            'texto' => $row['resposta_texto'],
            'pontos' => $pontos
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($quiz['titulo']) ?> - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/quiz.css?v=1">
    <link rel="stylesheet" href="../Styles/Header.css?v=27">
</head>

<body>

    <header>
        <nav class="navbar">
            <h2 class="title">
                DnNerds <img src="../Imagens/favicon.png?v=2" alt="">
            </h2>

            <ul>
                <li><a href="Noticias.php">Notícias</a></li>
                <li><a href="nerdlists.php">NerdList</a></li>
                <li><a href="Quizzes.php" class="ativo">Quizzes</a></li>
                <li><a href="#">IA</a></li>
            </ul>

            <button class="btn-navbar">
                <a href="../PHP/FazerLogin.php">Fazer Login</a>
            </button>
        </nav>
    </header>

    <main class="conteudo">

        <article class="quiz">

            <img class="quiz_img" src="<?= htmlspecialchars($quiz['imagem'] ?: '../Imagens/quizdefault.jpg') ?>"
                alt="<?= htmlspecialchars($quiz['titulo']) ?>">

            <p><?= htmlspecialchars($quiz['descricao']) ?></p>

            <div id="quiz-container"></div>

        </article>

    </main>

    <script>
        const perguntas = <?= json_encode(array_values($perguntas)) ?>;
        const resultados = <?= json_encode($resultados) ?>;

        let indice = 0;
        let pontos = {};

        const container = document.getElementById("quiz-container");
        const cores = ["ps-blue", "ps-pink", "ps-red", "ps-green"];

        function mostrarPergunta() {
            container.innerHTML = "";

            const pergunta = perguntas[indice];
            const h2 = document.createElement("h2");
            h2.textContent = pergunta.texto;
            container.appendChild(h2);

            pergunta.respostas.forEach((resposta, i) => {
                const btn = document.createElement("button");
                btn.textContent = resposta.texto;
                btn.classList.add(cores[i % cores.length]);

                btn.onclick = () => {
                    container.querySelectorAll("button")
                        .forEach(b => b.disabled = true);

                    for (const resultado_id in resposta.pontos) {
                        pontos[resultado_id] =
                            (pontos[resultado_id] || 0) + resposta.pontos[resultado_id];
                    }

                    btn.style.opacity = "0.6";

                    setTimeout(() => {
                        indice++;
                        indice < perguntas.length
                            ? mostrarPergunta()
                            : mostrarResultado();
                    }, 600);
                };

                container.appendChild(btn);
            });
        }

        function mostrarResultado() {
            let melhorResultado = null;
            let maiorPontuacao = -Infinity;

            for (const id in pontos) {
                if (pontos[id] > maiorPontuacao) {
                    maiorPontuacao = pontos[id];
                    melhorResultado = id;
                }
            }

            const r = resultados[melhorResultado];

            container.innerHTML = `
        <h2>${r.titulo}</h2>
        <img src="${r.imagem}" class="quiz_img" style="max-width:300px;border-radius:20px;">
        <p>${r.descricao}</p>
        <button onclick="location.reload()">Refazer o quiz</button>
        <button onclick="history.back()">Voltar</button>
    `;
        }

        perguntas.length > 0
            ? mostrarPergunta()
            : container.innerHTML = "<h2>Este quiz ainda não possui perguntas.</h2>";
    </script>

    <footer class="footer">
        <div class="footer-container">
            <p>2025 DnNerds — Renato Matos, Natalia Macedo, Arthur Simões, Diego Toscano, Yuri Reis, Enzo Niglia </p>
            <div class="footer-links"> <a href="https://www.youtube.com/" target="_blank" title="YouTube"><img
                        src="../Imagens/youtube.png" alt="YouTube"></a> <a href="https://www.instagram.com/DnNerds"
                    target="_blank" title="Instagram"><img src="../Imagens/instagram.jpeg" alt="Instagram"></a> <a
                    href="https://www.facebook.com/" target="_blank" title="Facebook"><img src="../Imagens/facebook.png"
                        alt="Facebook"></a> <a href="https://www.tiktok.com/" target="_blank" title="TikTok"><img
                        src="../Imagens/tiktok.jpeg" alt="TikTok"></a> </div>
        </div>
    </footer>

</body>

</html>