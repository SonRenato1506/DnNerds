<?php
include_once("config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titulo = $_POST['titulo'];
    $texto = $_POST['texto'];
    $imagem = $_POST['imagem'];
    $categoria = $_POST['categoria'];
    $palavrachave = $_POST['palavrachave'];

    // 📅 DATA GERADA PELO PHP
    date_default_timezone_set('America/Sao_Paulo');
    $data_publicacao = date("Y-m-d H:i:s");

    $sql = "INSERT INTO noticias 
            (titulo, texto, imagem, categoria, palavrachave, data_publicacao)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param(
        "ssssss",
        $titulo,
        $texto,
        $imagem,
        $categoria,
        $palavrachave,
        $data_publicacao
    );

    if ($stmt->execute()) {
        echo "<script>alert('✅ Notícia publicada com sucesso!'); window.location.href='Noticias.php';</script>";
    } else {
        echo "Erro ao salvar notícia.";
    }
}
?>
