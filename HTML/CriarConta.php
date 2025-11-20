<?php
include_once('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmaSenha = $_POST['confirm-password'] ?? '';

    if ($senha !== $confirmaSenha) {
        echo "❌ As senhas não coincidem!";
        exit;
    }

    $check = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "❌ E-mail já cadastrado!";
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senhaHash);

    if ($stmt->execute()) {
        echo "✅ Usuário cadastrado com sucesso!";
    } else {
        echo "❌ Erro: " . $stmt->error;
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DnNerds - Criação de conta</title>
    <link rel="stylesheet" href="../Styles/CriarConta.css?v=9">
    <link rel="stylesheet" href="../Styles/Header.css?v=28">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anonymous+Pro&family=Poppins:wght@300;600;800&display=swap"
        rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar">
            <h2 class="title">DnNerds <img src="../Imagens/favicon.png?v=2" alt=""></h2>
            <ul>
                <li><a class="selecao" href="../HTML/noticias.php">Notícias</a></li>
                <li><a class="selecao" href="">NerdList</a></li>
                <li><a class="selecao" href="">Quizzes</a></li>
                <li><a class="selecao" href="">IA</a></li>
            </ul>

            <button class="btn-navbar"><a class="selecao" href="../HTML/FazerLogin.php">Fazer Login</a></button>
        </nav>
    </header>

    <main class="container">

        <form action="CriarConta.php" method="POST">
            <div id="login">
                <h1>Criar Conta!</h1>
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" name="nome" placeholder="Digite seu nome" class="forms" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" placeholder="Digite seu e-mail" class="forms" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input id="senha" type="password" name="senha" placeholder="Digite sua senha" class="forms"
                        required>
                </div>

                <div class="form-group">
                    <label for="confirme-senha">Confirme Senha:</label>
                    <input id="confirme-senha" type="password" name="confirm-password" placeholder="Confirme sua senha"
                        class="forms" required>
                </div>

                <div class="form-button">
                    <button type="submit" id="btn-entrar">Criar Conta</button>
                </div>
            </div>
        </form>
    </main>
</body>

</html>