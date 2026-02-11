<?php
include_once("config.php");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Notícia - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Criador.css">




</head>

<body>

    <div class="container">
        <h2>📰 Criar Nova Notícia</h2>

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
    </div>

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