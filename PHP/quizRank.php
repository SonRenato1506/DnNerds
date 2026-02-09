<?php
require_once __DIR__ . '/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Quiz não encontrado.");
}

$quiz_id = (int) $_GET['id'];

/* ===============================
   BUSCAR QUIZ
================================ */
$sqlQuiz = "SELECT titulo, descricao FROM quizzes_rank WHERE id = $quiz_id LIMIT 1";
$resultQuiz = $conexao->query($sqlQuiz);

if ($resultQuiz->num_rows === 0) {
    die("Quiz não encontrado.");
}

$quiz = $resultQuiz->fetch_assoc();

/* ===============================
   BUSCAR ITENS DO RANK
================================ */
$sqlItens = "
    SELECT posicao, nome, dica
    FROM quiz_rank_itens
    WHERE quiz_id = $quiz_id
    ORDER BY posicao ASC
";
$resultItens = $conexao->query($sqlItens);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($quiz['titulo']) ?></title>
    <link rel="stylesheet" href="../Styles/Header.css">

    <style>
        body {
            padding-top: 100px;
            font-family: Arial, Helvetica, sans-serif;
            background: #111;
            color: #fff;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        h1 {
            margin-bottom: 5px;
        }

        p {
            color: #aaa;
        }

        input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            margin: 15px 0;
            border: none;
            border-radius: 6px;
        }

        .linha {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #333;
            padding: 8px 0;
        }

        .posicao {
            width: 50px;
            color: #888;
        }

        .nome {
            flex: 1;
            text-align: center;
        }

        .dica {
            width: 220px;
            text-align: right;
            color: #666;
        }

        .acertou {
            color: #00ff88;
            font-weight: bold;
        }

        .topo-fixo {
            position: sticky;
            top: 90px;
            /* abaixo do header */
            background: #111;
            z-index: 10;
            padding-bottom: 10px;
        }

        #lista {
            max-height: 55vh;
            /* controla quanto pode rolar */
            overflow-y: auto;
            margin-top: 10px;
            padding-right: 5px;
        }

        /* scrollbar estilizada (opcional) */
        #lista::-webkit-scrollbar {
            width: 6px;
        }

        #lista::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 4px;
        }
    </style>
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
                    <a href="EditorQuizRank.php?id=<?= $quiz_id ?>">Editar</a>
                </li>
                <li><a href="copinhas.php" class="ativo">Copinhas</a></li>


            </ul>

            <button class="btn-navbar">
                <a href="FazerLogin.php">Fazer Login</a>
            </button>
        </nav>
    </header>

    <div class="container">
        <div class="topo-fixo">
            <h1><?= htmlspecialchars($quiz['titulo']) ?></h1>
            <p><?= htmlspecialchars($quiz['descricao']) ?></p>

            <input type="text" id="resposta" placeholder="Digite um nome..." autocomplete="off" autofocus>
        </div>

        <div id="lista">

            <?php while ($item = $resultItens->fetch_assoc()): ?>
                <div class="linha" data-nome="<?= htmlspecialchars(strtolower($item['nome'])) ?>">
                    <div class="posicao"><?= $item['posicao'] ?></div>
                    <div class="nome">---</div>
                    <div class="dica"><?= htmlspecialchars($item['dica']) ?></div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        const input = document.getElementById('resposta');
        const linhas = document.querySelectorAll('.linha');

        function normalizar(txt) {
            return txt
                .toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "");
        }

        input.addEventListener('keyup', () => {
            const valor = normalizar(input.value.trim());
            if (valor.length < 2) return;

            const palavrasDigitadas = valor.split(" ");

            linhas.forEach(linha => {
                const campoNome = linha.querySelector('.nome');
                if (campoNome.innerHTML !== '---') return;

                const nomeCompleto = normalizar(linha.dataset.nome);
                const palavrasNome = nomeCompleto.split(" ");

                const acertou = palavrasDigitadas.some(p =>
                    palavrasNome.includes(p)
                );

                if (acertou) {
                    campoNome.innerHTML = linha.dataset.nome.toUpperCase();
                    campoNome.classList.add('acertou');
                    input.value = '';
                }
            });
        });
    </script>

</body>

</html>