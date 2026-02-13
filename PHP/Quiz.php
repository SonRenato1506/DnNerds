<?php
include_once('config.php');
include_once("header.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz_id = (int) $_GET['id'];

$sqlQuiz = "SELECT * FROM quizzes WHERE id = $quiz_id LIMIT 1";
$resultQuiz = $conexao->query($sqlQuiz);

if (!$resultQuiz || $resultQuiz->num_rows === 0) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz = $resultQuiz->fetch_assoc();

/* PERGUNTAS */
$sqlPerguntas = "
    SELECT 
        p.id   AS pergunta_id,
        p.texto AS pergunta_texto,
        r.id   AS resposta_id,
        r.texto AS resposta_texto,
        r.correta
    FROM perguntas p
    JOIN respostas r ON r.pergunta_id = p.id
    WHERE p.quizz_id = $quiz_id
    ORDER BY p.id
";

$resultPerguntas = $conexao->query($sqlPerguntas);

$perguntas = [];

if ($resultPerguntas && $resultPerguntas->num_rows > 0) {
    while ($row = $resultPerguntas->fetch_assoc()) {
        $pid = $row['pergunta_id'];

        if (!isset($perguntas[$pid])) {
            $perguntas[$pid] = [
                'texto' => $row['pergunta_texto'],
                'respostas' => []
            ];
        }

        $perguntas[$pid]['respostas'][] = [
            'texto' => $row['resposta_texto'],
            'correta' => $row['correta']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($quiz['titulo']) ?></title>
    <link rel="stylesheet" href="../Styles/quiz.css?v=3">
</head>

<body>
    <main class="conteudo">
        <article class="quiz">
            <img class="quiz_img" src="<?= htmlspecialchars($quiz['imagem']) ?>">
            <p><?= htmlspecialchars($quiz['descricao']) ?></p>
            <div id="quiz-container"></div>
        </article>
    </main>

    <a href="editorQuiz.php?id=<?= $quiz['id'] ?>">
        <button id="editor">
            Edite esse Quiz
        </button>
    </a>

    <script>
        function embaralhar(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        const perguntas = <?= json_encode(array_values($perguntas)) ?>;

        let indice = 0;
        let pontuacao = 0;

        const container = document.getElementById("quiz-container");

        function mostrarPergunta() {

            container.classList.remove("quiz-animar");
            void container.offsetWidth; // reset da animação
            container.classList.add("quiz-animar");

            container.innerHTML = "";

            const pergunta = perguntas[indice];
            const respostas = embaralhar([...pergunta.respostas]);

            const h2 = document.createElement("h2");
            h2.textContent = pergunta.texto;
            container.appendChild(h2);

            const estilosPS = ["ps-blue", "ps-pink", "ps-red", "ps-green"];

            respostas.forEach((resposta, i) => {
                const btn = document.createElement("button");
                btn.textContent = resposta.texto;

                // ✅ adiciona estilo PlayStation
                btn.classList.add(estilosPS[i % estilosPS.length]);

                btn.onclick = () => {
                    const botoes = container.querySelectorAll("button");
                    botoes.forEach(b => b.disabled = true);

                    if (resposta.correta == 1) {
                        btn.classList.add("correta");
                        pontuacao++;
                    } else {
                        btn.classList.add("errada");

                        // ✅ destaca a correta
                        botoes.forEach(b => {
                            const texto = b.textContent;

                            respostas.forEach(r => {
                                if (r.texto === texto && r.correta == 1) {
                                    b.classList.add("correta");
                                }
                            });
                        });
                    }

                    setTimeout(() => {
                        indice++;
                        indice < perguntas.length ? mostrarPergunta() : mostrarResultado();
                    }, 700);
                };

                container.appendChild(btn);
            });

        }


        function mostrarResultado() {

            container.innerHTML = `
        <div class="vitoria">
            <h2>🏆 Você acertou ${pontuacao} de ${perguntas.length}!</h2>
            <div id="ranking"></div>
            <div class="botoes">
                <button onclick="location.reload()">Refazer</button>
                <button onclick="history.back()">Voltar</button>
            </div>

        </div>
    `;

            salvarResultado();
        }


        function salvarResultado() {
            fetch("salvar_resultado.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    quiz_id: <?= $quiz_id ?>,
                    pontuacao: pontuacao,
                    total: perguntas.length
                })
            })
                .then(res => res.json())
                .then(data => mostrarRanking(data.ranking));
        }

        function mostrarRanking(ranking) {
            if (!ranking.length) return;

            let html = "<h3>🏆 Top 3 Global</h3>";

            ranking.forEach((player, i) => {
                html += `<p>${i + 1}º ${player.nome} — ${player.pontuacao}/${player.total}</p>`;
            });

            document.getElementById("ranking").innerHTML = html;
        }

        perguntas.length > 0
            ? mostrarPergunta()
            : container.innerHTML = "<h2>Quiz sem perguntas</h2>";
    </script>
</body>

</html>