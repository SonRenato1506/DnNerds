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
$sqlQuiz = "SELECT * FROM quizzes WHERE id = $quiz_id LIMIT 1";
$resultQuiz = $conexao->query($sqlQuiz);

if (!$resultQuiz || $resultQuiz->num_rows === 0) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz = $resultQuiz->fetch_assoc();
$categoria = $quiz['categoria'];

/* ===============================
   QUIZZES RELACIONADOS
================================ */
$sqlRelacionados = "
    SELECT * FROM quizzes
    WHERE categoria = '$categoria'
      AND id != $quiz_id
    ORDER BY id DESC
    LIMIT 6
";
$relacionados = $conexao->query($sqlRelacionados);

/* ===============================
   PERGUNTAS E RESPOSTAS
================================ */
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

/* ===============================
   ORGANIZAÇÃO DOS DADOS
================================ */
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
            'id' => $row['resposta_id'],
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
    <title><?= htmlspecialchars($quiz['titulo']) ?> - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/quiz.css?v=8">
    <link rel="stylesheet" href="../Styles/Header.css?v=29">

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
                <li>
                    <?php if (isset($quiz_id)): ?>
                        <a href="EditorQuiz.php?id=<?= $quiz_id ?>">Editar</a>
                    <?php else: ?>
                        <a href="EditorQuiz.php">Editor</a>
                    <?php endif; ?>
                </li>
                <li><a href="copinhas.php" class="ativo">Copinhas</a></li>

            </ul>

            <button class="btn-navbar">
                <a href="FazerLogin.php">Fazer Login</a>
            </button>
        </nav>
    </header>

    <main class="conteudo">

        <!-- QUIZ -->
        <article class="quiz">

            <img class="quiz_img" src="<?= htmlspecialchars($quiz['imagem'] ?: 'quizdefault.jpg') ?>"
                alt="<?= htmlspecialchars($quiz['titulo']) ?>">

            <p><?= htmlspecialchars($quiz['descricao']) ?></p>

            <div id="quiz-container"></div>

        </article>

    </main>

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
        const cores = ["ps-blue", "ps-pink", "ps-red", "ps-green"];

        function mostrarPergunta() {
            container.innerHTML = "";

            const pergunta = perguntas[indice];

            // 🔀 Embaralha as respostas
            const respostasEmbaralhadas = embaralhar([...pergunta.respostas]);

            const h2 = document.createElement("h2");
            h2.textContent = pergunta.texto;
            container.appendChild(h2);

            respostasEmbaralhadas.forEach((resposta, i) => {
                const btn = document.createElement("button");
                btn.textContent = resposta.texto;
                btn.classList.add(cores[i % cores.length]);

                btn.onclick = () => {
                    const botoes = container.querySelectorAll("button");
                    botoes.forEach(b => b.disabled = true);

                    if (resposta.correta == 1) {
                        btn.style.backgroundColor = "green";
                        pontuacao++;
                    } else {
                        btn.style.backgroundColor = "red";
                    }

                    setTimeout(() => {
                        indice++;
                        indice < perguntas.length
                            ? mostrarPergunta()
                            : mostrarResultado();
                    }, 1000);
                };

                container.appendChild(btn);
            });
        }


        function mostrarResultado() {
            container.innerHTML = `
        <h2>Você acertou ${pontuacao} de ${perguntas.length} perguntas!</h2>
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