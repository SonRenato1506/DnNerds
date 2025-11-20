<?php
include_once('config.php');

// 🟩 Verifica se recebeu um ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz_id = intval($_GET['id']);

// 🟩 Busca o quiz principal
$sqlQuiz = "SELECT * FROM personalidade WHERE id = $quiz_id LIMIT 1";
$resultQuiz = $conexao->query($sqlQuiz);

if (!$resultQuiz || $resultQuiz->num_rows === 0) {
    echo "Quiz não encontrado.";
    exit;
}

$quiz = $resultQuiz->fetch_assoc();
$categoria = $quiz['categoria'];

// 🟩 Busca os resultados possíveis
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

// 🟩 Busca perguntas e respostas
$sqlPerguntas = "
    SELECT p.id AS pergunta_id, p.texto AS pergunta_texto,
           r.id AS resposta_id, r.texto AS resposta_texto
    FROM personalidade_perguntas p
    JOIN personalidade_respostas r ON r.pergunta_id = p.id
    WHERE p.personalidade_id = $quiz_id
    ORDER BY p.id
";

$resultPerguntas = $conexao->query($sqlPerguntas);

// 🟩 Armazena perguntas e respostas
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

        // Busca pontuação específica da resposta
        $sqlPont = "
            SELECT * FROM personalidade_respostas_pontuacao 
            WHERE resposta_id = {$row['resposta_id']}
        ";
        $pontuacaoData = $conexao->query($sqlPont);

        $pontos = [];
        if ($pontuacaoData && $pontuacaoData->num_rows > 0) {
            while ($p = $pontuacaoData->fetch_assoc()) {
                $pontos[$p['resultado_id']] = $p['pontos'];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['titulo']); ?> - DnNerds</title>
    <link rel="stylesheet" href="../Styles/quiz.css?v=1">
    <link rel="stylesheet" href="../Styles/Header.css?v=27">
</head>

<body>
<header>
        <nav class="navbar">
            <h2 class="title">DnNerds <img src="../Imagens/favicon.png?v=2" alt=""></h2>
            <ul>
                <li><a href="Noticias.php">Notícias</a></li>
                <li><a href="">NerdList</a></li>
                <li><a href="Quizzes.php" class="ativo">Quizzes</a></li>
                <li><a href="">IA</a></li>
            </ul>
            <button class="btn-navbar"><a href="../HTML/FazerLogin.php">Fazer Login</a></button>
        </nav>
    </header>

    <main class="conteudo">

        <article class="quiz">
            <img class="quiz_img"
                src="<?php echo !empty($quiz['imagem']) ? htmlspecialchars($quiz['imagem']) : '../Imagens/quizdefault.jpg'; ?>"
                alt="<?php echo htmlspecialchars($quiz['titulo']); ?>">

            <p><?php echo htmlspecialchars($quiz['descricao']); ?></p>

            <div id="quiz-container"></div>
        </article>

    </main>

<script>
const perguntas = <?php echo json_encode(array_values($perguntas)); ?>;
const resultados = <?php echo json_encode($resultados); ?>;

let indice = 0;
let pontos = {}; // armazena pontuação por resultado_id

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

        const colors = ["ps-blue", "ps-pink", "ps-red", "ps-green"];
        btn.classList.add(colors[i % 4]);

        btn.onclick = () => {
            const botoes = container.querySelectorAll("button");
            botoes.forEach(b => b.disabled = true);

            // Somar pontos desta resposta para todos os resultados
            for (const resultado_id in r.pontos) {
                const valor = parseInt(r.pontos[resultado_id]);
                pontos[resultado_id] = (pontos[resultado_id] || 0) + valor;
            }

            btn.style.opacity = "0.6";

            setTimeout(() => {
                indice++;
                if (indice < perguntas.length) {
                    mostrarPergunta();
                } else {
                    mostrarResultado();
                }
            }, 600);
        };

        container.appendChild(btn);
    });
}

function mostrarResultado() {
    let melhorResultado = null;
    let maiorPontuacao = -999999;

    for (const resultado_id in pontos) {
        if (pontos[resultado_id] > maiorPontuacao) {
            maiorPontuacao = pontos[resultado_id];
            melhorResultado = resultado_id;
        }
    }

    const r = resultados[melhorResultado];

    container.innerHTML = `
        <h2>${r.titulo}</h2>
        <img src="${r.imagem}" class="quiz_img" style="max-width:300px;border-radius:20px;">
        <p>${r.descricao}</p>
        <a href="Quizzes.php" class="btn-voltar">Voltar</a>
    `;
}

if (perguntas.length > 0) {
    mostrarPergunta();
} else {
    container.innerHTML = "<h2>Este quiz ainda não possui perguntas.</h2>";
}
</script>

</body>
</html>
