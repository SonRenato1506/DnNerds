<?php
session_start();
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ? LIMIT 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $usuario = $result->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {

            session_regenerate_id(true);

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['email'] = $usuario['email'];

            header("Location: ../PHP/noticias.php");
            exit;
        }

        $_SESSION['erro'] = "Senha incorreta!";
    } else {
        $_SESSION['erro'] = "Usuário não encontrado!";
    }

    header("Location: FazerLogin.php");
    exit;
}

include_once("header.php");
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DnNerds</title>
    <link rel="stylesheet" href="../Styles/FazerLogin.css?v=7">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Anonymous+Pro:ital,wght@0,400;0,700;1,400;1,700&family=Caveat&family=Open+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Poppins:wght@300;600;800&display=swap"
        rel="stylesheet">

</head>

<body>

    <main class="container"></main>

    <div id="login">
        <h1>Bem-Vindo!</h1>
        <p>Novo no DnNerds? <a href="../PHP/CriarConta.php">Crie uma conta!</a></p>

        <form action="FazerLogin.php" method="POST">
            <label for="email">Email: </label>
            <input type="email" name="email" placeholder="Digite seu e-mail" class="forms" required>
            <br><br>
            <label for="senha">Senha:</label>
            <input id="senha" type="password" name="senha" placeholder="Digite sua senha" class="forms" required>
            <br><br>
            <a href="/">Esqueci minha senha!</a>

            <button type="submit" id="btn-entrar">Entrar</button>

    </div>
    </form>



    </main>
</body>

</html>

<!-- 833918725600-jrmajifbdtsncpejrn57g8hcbphp9q5n.apps.googleusercontent.com -->
<!-- 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 -->