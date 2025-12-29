<?php include_once("config.php"); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Quiz - DnNerds</title>
    <style>
        body { font-family: Arial; padding: 20px; background:#111; color:#fff }
        input, textarea, select, button {
            width:100%; margin:5px 0; padding:8px;
        }
        .pergunta {
            border:1px solid #444;
            padding:10px;
            margin-top:15px;
        }
    </style>
</head>

<body>

<h1>Criar Quiz</h1>

<form action="salvarQuiz.php" method="POST">

    <label>Título do Quiz</label>
    <input type="text" name="titulo" required>

    <label>Descrição</label>
    <textarea name="descricao" required></textarea>

    <label>Categoria</label>
    <select name="categoria">
        <option value="Games">Games</option>
        <option value="Anime">Anime</option>
        <option value="Series">Séries</option>
        <option value="Filmes">Filmes</option>
        <option value="Livros">Livros</option>
        <option value="Variados">Variados</option>

    </select>

    <label>Imagem (URL ou caminho)</label>
    <input type="text" name="imagem">

    <hr>

    <div id="perguntas"></div>

    <button type="button" onclick="addPergunta()">➕ Adicionar Pergunta</button>
    <br><br>
    <button type="submit">🚀 Salvar Quiz</button>

</form>

<script>
let count = 0;

function addPergunta() {
    count++;

    const div = document.createElement("div");
    div.className = "pergunta";

    div.innerHTML = `
        <h3>Pergunta ${count}</h3>
        <input type="text" name="perguntas[${count}][texto]" placeholder="Pergunta" required>

        <input type="text" name="perguntas[${count}][respostas][0][texto]" placeholder="Resposta A" required>
        <input type="radio" name="perguntas[${count}][correta]" value="0"> Correta

        <input type="text" name="perguntas[${count}][respostas][1][texto]" placeholder="Resposta B" required>
        <input type="radio" name="perguntas[${count}][correta]" value="1"> Correta

        <input type="text" name="perguntas[${count}][respostas][2][texto]" placeholder="Resposta C" required>
        <input type="radio" name="perguntas[${count}][correta]" value="2"> Correta

        <input type="text" name="perguntas[${count}][respostas][3][texto]" placeholder="Resposta D" required>
        <input type="radio" name="perguntas[${count}][correta]" value="3"> Correta
    `;

    document.getElementById("perguntas").appendChild(div);
}
</script>

</body>
</html>
