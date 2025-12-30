<?php include_once("config.php"); ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Quiz - DnNerds</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #220f2cff, #4b0b51c7, #6a1d72);
            color: #fff;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        form {
            max-width: 800px;
            margin: auto;
            background: rgba(0, 0, 0, 0.6);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: none;
            background: #1e1e1e;
            color: #fff;
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        input::placeholder {
            color: #aaa;
        }

        hr {
            margin: 30px 0;
            border: none;
            height: 1px;
            background: #444;
        }

        .pergunta {
            background: #161616;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            border-left: 5px solid #00c6ff;
            animation: fadeIn 0.3s ease-in-out;
        }

        .pergunta h3 {
            margin-top: 0;
            color: #00c6ff;
        }

        .pergunta input[type="radio"] {
            width: auto;
            margin-right: 6px;
        }

        .pergunta input[type="text"] {
            margin-bottom: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button[type="button"] {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            color: #fff;
            margin-top: 10px;
        }

        button[type="submit"] {
            background: linear-gradient(135deg, #34ac4cff, #137428ff);
            color: #000;
            margin-top: 20px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        }

        .btn-remover {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(255, 65, 108, 0.4);
        }

        .btn-remover:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 18px rgba(255, 65, 108, 0.6);
        }

        .btn-remover:active {
            transform: scale(0.97);
        }

        .btn-remover::before {
            content: "⚠ ";
        }


        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 600px) {
            form {
                padding: 15px;
            }
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