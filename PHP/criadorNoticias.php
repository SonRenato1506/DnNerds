<?php
include_once("config.php");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Notícia - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body { font-family: Arial; background:#111; color:#fff; }
        form { max-width:600px; margin:40px auto; }
        input, textarea, select, button {
            width:100%;
            padding:10px;
            margin-bottom:15px;
        }
        button {
            background:#6c5ce7;
            color:#fff;
            border:none;
            cursor:pointer;
        }
    </style>
</head>

<body>

<h2 style="text-align:center;">📰 Criar Nova Notícia</h2>

<form action="salvarNoticia.php" method="POST">

    <label>Título</label>
    <input type="text" name="titulo" required>

    <label>Texto da notícia</label>
    <textarea name="texto" rows="8" required></textarea>

    <label>Imagem (URL)</label>
    <input type="text" name="imagem" placeholder="https://site.com/imagem.jpg ou ../Imagens/Thunderbolts.jpeg">

    <label>Categoria</label>
    <select name="categoria" required>
        <option value="">Selecione</option>
        <option value="Jogos">Jogos</option>
        <option value="Animes">Animes</option>
        <option value="Series/Filmes">Séries/Filmes</option>
        <option value="Livros">Livros</option>
        <option value="Tecnologia">Tecnologia</option>
        <option value="RPG">RPG</option>
    </select>

    <label>Palavra-chave (URL)</label>
    <input type="text" name="palavrachave" placeholder="ex: batman-novo-filme" required>

    <button type="submit">Publicar Notícia</button>

</form>

</body>
</html>
