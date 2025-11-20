<?php
include_once('config.php');

// 🟩 Verifica se recebeu um ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz_id = intval($_GET['id']);

// 🟩 Busca o quiz principal
$sqlQuiz = "SELECT * FROM quizzes WHERE id = $quiz_id LIMIT 1";
$resultQuiz = $conexao->query($sqlQuiz);

if (!$resultQuiz || $resultQuiz->num_rows === 0) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz = $resultQuiz->fetch_assoc();
$categoria = $quiz['categoria'];

// 🟩 Busca outros quizzes da mesma categoria (para lateral)
$sqlRelacionados = "
    SELECT * FROM quizzes
    WHERE categoria = '$categoria' 
    AND id != $quiz_id
    ORDER BY id DESC
    LIMIT 6
";
$relacionados = $conexao->query($sqlRelacionados);

// 🟩 Busca perguntas e respostas do quiz atual
$sqlPerguntas = "
    SELECT p.id AS pergunta_id, p.texto AS pergunta_texto, 
           r.id AS resposta_id, r.texto AS resposta_texto, r.correta
    FROM perguntas p
    JOIN respostas r ON r.pergunta_id = p.id
    WHERE p.quizz_id = $quiz_id
    ORDER BY p.id
";

$resultPerguntas = $conexao->query($sqlPerguntas);

// 🟩 Organiza perguntas e respostas
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['titulo']); ?> - DnNerds</title>
    <link rel="stylesheet" href="../Styles/quiz.css?v=6">
    <link rel="stylesheet" href="../Styles/Header.css?v=27">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Anonymous+Pro:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:wght@300;600;800&display=swap"
        rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar">
            <h2 class="title">DnNerds <img src="../../Imagens/favicon.png?v=2" alt=""></h2>
            <ul>
                <li><a href="Noticias.php">Notícias</a></li>
                <li><a href="">NerdList</a></li>
                <li><a href="Quizzes.php" class="ativo">Quizzes</a></li>
                <li><a href="">IA</a></li>
            </ul>
            <button class="btn-navbar"><a href="../FazerLogin.php">Fazer Login</a></button>
        </nav>
    </header>

    <main class="conteudo">
        <!-- 🟦 Quiz principal -->
        <article class="quiz">
            <img class="quiz_img"
                src="<?php echo !empty($quiz['imagem']) ? htmlspecialchars($quiz['imagem']) : 'quizdefault.jpg'; ?>"
                alt="<?php echo htmlspecialchars($quiz['titulo']); ?>">

            <!-- <h1><?php echo htmlspecialchars($quiz['titulo']); ?></h1> -->
            <p><?php echo htmlspecialchars($quiz['descricao']); ?></p>

            <div id="quiz-container"></div>
        </article>


    </main>

    <script>
        const perguntas = <?php echo json_encode(array_values($perguntas)); ?>;
        let indice = 0;
        let pontuacao = 0;

        const container = document.getElementById("quiz-container");

        function mostrarPergunta() {
            container.innerHTML = "";
            const pergunta = perguntas[indice];

            const h2 = document.createElement("h2");
            h2.textContent = pergunta.texto;
            container.appendChild(h2);

            pergunta.respostas.forEach((r, i) => {
                const btn = document.createElement("button");
                btn.textContent = r.texto;

                const classes = ["ps-blue", "ps-pink", "ps-red", "ps-green"];
                btn.classList.add(classes[i]);

                btn.onclick = () => {
                    // Evita clicar novamente
                    const botoes = container.querySelectorAll("button");
                    botoes.forEach(b => b.disabled = true);

                    // COR CERTA OU ERRADA
                    if (r.correta == 1) {
                        btn.style.backgroundColor = "green";
                        btn.style.borderColor = "darkgreen";
                        btn.style.color = "white";
                        pontuacao++;
                    } else {
                        btn.style.backgroundColor = "red";
                        btn.style.borderColor = "darkred";
                        btn.style.color = "white";
                    }

                    // Espera 1 segundo e vai para próxima pergunta
                    setTimeout(() => {
                        indice++;
                        if (indice < perguntas.length) {
                            mostrarPergunta();
                        } else {
                            mostrarResultado();
                        }
                    }, 1000);
                };

                container.appendChild(btn);
            });
        }


        function mostrarResultado() {
            container.innerHTML = `<h2>Você acertou ${pontuacao} de ${perguntas.length} perguntas!</h2> <button onclick="location.reload()">Refazer o quiz</button>  <button onclick="history.back()">Voltar</button>`;
        }

        if (perguntas.length > 0) {
            mostrarPergunta();
        } else {
            container.innerHTML = "<h2>Este quiz ainda não possui perguntas.</h2>";
        }
    </script>
</body>

</html>