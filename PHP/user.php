<?php
include_once("config.php");
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: FazerLogin.php");
    exit;
}

$user_id = $_SESSION['id'];
$mensagem = "";

/* =========================
   LOGOUT
========================= */
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: FazerLogin.php");
    exit;
}

/* =========================
   EXCLUIR CONTA
========================= */
if (isset($_POST['delete'])) {

    $sqlDelete = "DELETE FROM usuarios WHERE id = ?";
    $stmtDelete = $conexao->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $user_id);

    if ($stmtDelete->execute()) {
        session_destroy();
        header("Location: CriarConta.php");
        exit;
    } else {
        $mensagem = "❌ Erro ao excluir conta!";
    }
}

/* =========================
   ATUALIZA PERFIL
========================= */
if (isset($_POST['update'])) {

    $novoNome = trim($_POST['nome'] ?? '');
    $novaFoto = trim($_POST['foto'] ?? '');

    if (empty($novoNome)) {
        $mensagem = "❌ Nome não pode ficar vazio!";
    } else {

        $sql = "UPDATE usuarios SET nome = ?, foto = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssi", $novoNome, $novaFoto, $user_id);

        if ($stmt->execute()) {

            $_SESSION['nome'] = $novoNome;
            $mensagem = "✅ Perfil atualizado com sucesso!";

        } else {
            $mensagem = "❌ Erro ao atualizar!";
        }
    }
}

/* =========================
   BUSCA USUÁRIO
========================= */
$sqlUser = "SELECT nome, email, foto FROM usuarios WHERE id = ?";
$stmtUser = $conexao->prepare($sqlUser);
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$usuario = $resultUser->fetch_assoc();

include_once("header.php");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Meu Perfil</title>
    <link rel="stylesheet" href="../Styles/user.css">
</head>

<body>

<main class="container">

    <div class="perfil-box">

        <h1>🎮 Meu Perfil</h1>

        <?php if (!empty($mensagem)) : ?>
            <p class="mensagem"><?= $mensagem ?></p>
        <?php endif; ?>

        <div class="foto-perfil">
            <img src="<?= !empty($usuario['foto']) ? $usuario['foto'] : 'https://via.placeholder.com/150' ?>">
        </div>

        <form method="POST">

            <label>Nome</label>
            <input type="text" name="nome"
                   value="<?= htmlspecialchars($usuario['nome']) ?>" required>

            <label>Email</label>
            <input type="email"
                   value="<?= htmlspecialchars($usuario['email']) ?>" disabled>

            <label>Foto (link)</label>
            <input type="text" name="foto"
                   value="<?= htmlspecialchars($usuario['foto'] ?? '') ?>"
                   placeholder="Cole a URL da imagem">

            <button name="update" class="btn salvar">Salvar Alterações</button>

            <div class="actions">

                <button name="logout" class="btn logout">Sair da Conta</button>

                <button name="delete" class="btn delete"
                        onclick="return confirm('Tem certeza que deseja excluir sua conta?');">
                    Excluir Conta
                </button>

            </div>

        </form>

    </div>

</main>

</body>
</html>
