<?php include_once("config.php"); ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Quiz - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Header.css">
    <link rel="stylesheet" href="../Styles/Criador.css">
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
            <li><a href="#">IA</a></li>
        </ul>
        <button class="btn-navbar">
            <a href="FazerLogin.php">Fazer Login</a>
        </button>
    </nav>
</header>

<!-- ===================== -->
<!-- 🧠 Criador de Quiz -->
<!-- ===================== -->

<div class="container">

    <h2>🧠 Criar Novo Quiz</h2>

    <form action="salvarQuiz.php" method="POST">

        <label>Título do Quiz</label>
        <input type="text" name="titulo" required>

        <label>Descrição</label>
        <textarea name="descricao" required></textarea>

        <label>Categoria</label>
        <select name="categoria" required>
            <option value="">Selecione</option>
            <option value="Games">Games</option>
            <option value="Anime">Anime</option>
            <option value="Series">Séries</option>
            <option value="Filmes">Filmes</option>
            <option value="Livros">Livros</option>
            <option value="Variados">Variados</option>
        </select>

        <label>Imagem (URL ou caminho)</label>
        <input type="text" name="imagem" placeholder="https://site.com/imagem.jpg">

        <hr>

        <!-- Perguntas -->
        <div id="perguntas"></div>

        <button type="button" onclick="addPergunta()">➕ Adicionar Pergunta</button>

        <button type="submit">🚀 Salvar Quiz</button>

    </form>

</div>

<script>
let count = 0;

function addPergunta() {
    count++;

    const div = document.createElement("div");
    div.className = "pergunta";

    div.innerHTML = `
        <h3>Pergunta ${count}</h3>

                <div class="opcao">
        <input type="text" name="perguntas[${count}][texto]" placeholder="Pergunta" required>
        </div>

        <div class="opcao">
            <input type="text" name="perguntas[${count}][respostas][0][texto]" placeholder="Resposta A" required>
            <label><input type="radio" name="perguntas[${count}][correta]" value="0" required> Correta</label>
        </div>

        <div class="opcao">
            <input type="text" name="perguntas[${count}][respostas][1][texto]" placeholder="Resposta B" required>
            <label><input type="radio" name="perguntas[${count}][correta]" value="1"> Correta</label>
        </div>

        <div class="opcao">
            <input type="text" name="perguntas[${count}][respostas][2][texto]" placeholder="Resposta C" required>
            <label><input type="radio" name="perguntas[${count}][correta]" value="2"> Correta</label>
        </div>

        <div class="opcao">
            <input type="text" name="perguntas[${count}][respostas][3][texto]" placeholder="Resposta D" required>
            <label><input type="radio" name="perguntas[${count}][correta]" value="3"> Correta</label>
        </div>
    `;

    document.getElementById("perguntas").appendChild(div);
}
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
