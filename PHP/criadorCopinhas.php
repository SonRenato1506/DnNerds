<?php
include_once("config.php");
include_once("header.php");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Copinha - DnNerds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../Styles/Criador.css">
</head>

<body>

    <div class="container">
        <h2>🏆 Criar Nova Copinha</h2>

        <form action="salvarCopinha.php" method="POST">

            <label>Título da Copinha</label>
            <input type="text" name="titulo" required>

            <label>Imagem da Copinha (URL)</label>
            <input type="text" name="imagem">

            <label>Categoria</label>
            <select name="categoria" required>
                <option value="">Selecione</option>
                <option value="games">Games</option>
                <option value="animes">Animes</option>
                <option value="filmes_series">Filmes & Séries</option>
                <option value="rpg">RPG</option>
                <option value="esporte">Esporte</option>
                <option value="musica">Música</option>
                <option value="outros">Outros</option>
            </select>

            <hr>

            <h3>🎮 Itens da Copinha</h3>

            <div id="itens">
                <div class="item">
                    <label>Nome do Item</label>
                    <input type="text" name="item_nome[]" required>

                    <label>Imagem do Item (URL)</label>
                    <input type="text" name="item_imagem[]">
                </div>
            </div>

            <button type="button" onclick="adicionarItem()">➕ Adicionar Item</button>

            <br><br>

            <button type="submit">Criar Copinha</button>
        </form>
    </div>

    <script>
        function adicionarItem() {
            const div = document.createElement("div");
            div.classList.add("item");

            div.innerHTML = `
        <label>Nome do Item</label>
        <input type="text" name="item_nome[]" required>

        <label>Imagem do Item (URL)</label>
        <input type="text" name="item_imagem[]">
    `;

            document.getElementById("itens").appendChild(div);
        }
    </script>

    <footer class="footer">
        <div class="footer-container">
            <p>2025 DnNerds — Renato Matos, Natalia Macedo, Arthur Simões, Diego Toscano, Yuri Reis, Enzo Niglia</p>
        </div>
    </footer>

</body>

</html>