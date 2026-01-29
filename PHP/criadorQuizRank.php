<?php include_once("config.php"); ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Quiz Rank - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Header.css">
    <link rel="stylesheet" href="../Styles/Criador.css">
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
                <li><a href="Quizzes.php?tipo=rank">QuizRank</a></li>
                <li><a href="copinhas.php" class="ativo">Copinhas</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">

        <h2>🏆 Criar Quiz Rank</h2>

        <form action="salvarQuizRank.php" method="POST">

            <label>Título</label>
            <input type="text" name="titulo" required>

            <label>Descrição</label>
            <textarea name="descricao" required></textarea>

            <label>Categoria</label>
            <select name="categoria" required>
                <option value="">Selecione</option>
                <option value="jogos">Jogos</option>
                <option value="animes">Animes</option>
                <option value="filmes/series">Filmes / Séries</option>
                <option value="futebol">Futebol</option>
                <option value="basquete">Basquete</option>
                <option value="variados">Variados</option>
            </select>

            <label>Imagem</label>
            <input type="text" name="imagem" placeholder="../Imagens/quizdefault.jpg">

            <hr>

            <h3>📋 Itens do Rank</h3>

            <div id="itens"></div>

            <button type="button" onclick="addItem()">➕ Adicionar Item</button>
            <button type="submit">🚀 Salvar Quiz Rank</button>

        </form>

    </div>

    <script>
        let posicao = 0;

        function addItem() {
            posicao++;

            const div = document.createElement("div");
            div.className = "pergunta";

            div.innerHTML = `
        <h4>#${posicao}</h4>

        <input type="hidden" name="itens[${posicao}][posicao]" value="${posicao}">

        <label>Nome</label>
        <input type="text" name="itens[${posicao}][nome]" required>

        <label>Dica (opcional)</label>
        <input type="text" name="itens[${posicao}][dica]">

        <hr>
    `;

            document.getElementById("itens").appendChild(div);
        }
    </script>

</body>

</html>