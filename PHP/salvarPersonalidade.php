<?php
include_once("config.php");

$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$imagem = $_POST['imagem'];
$categoria = $_POST['categoria'];

$conexao->begin_transaction();

// Quiz
$stmt = $conexao->prepare(
"INSERT INTO personalidade (titulo, descricao, imagem, categoria)
 VALUES (?,?,?,?)");
$stmt->bind_param("ssss",$titulo,$descricao,$imagem,$categoria);
$stmt->execute();
$quiz_id = $stmt->insert_id;

// Resultados
$resultadoIds = [];
foreach($_POST['resultados'] as $r){
    $stmtR = $conexao->prepare(
    "INSERT INTO personalidade_resultados
     (personalidade_id,titulo,descricao,imagem)
     VALUES (?,?,?,?)");
    $stmtR->bind_param("isss",$quiz_id,$r['titulo'],$r['descricao'],$r['imagem']);
    $stmtR->execute();
    $resultadoIds[] = $stmtR->insert_id;
}

// Perguntas e respostas
foreach($_POST['perguntas'] as $p){
    $stmtP = $conexao->prepare(
    "INSERT INTO personalidade_perguntas (personalidade_id,texto)
     VALUES (?,?)");
    $stmtP->bind_param("is",$quiz_id,$p['texto']);
    $stmtP->execute();
    $pergunta_id = $stmtP->insert_id;

    foreach($p['respostas'] as $resp){
        $stmtResp = $conexao->prepare(
        "INSERT INTO personalidade_respostas (pergunta_id,texto)
         VALUES (?,?)");
        $stmtResp->bind_param("is",$pergunta_id,$resp['texto']);
        $stmtResp->execute();
        $resposta_id = $stmtResp->insert_id;

        $resultado_id = $resultadoIds[$resp['resultado']];
        $stmtPts = $conexao->prepare(
        "INSERT INTO personalidade_respostas_pontuacao
         (resposta_id,resultado_id,pontos)
         VALUES (?,?,?)");
        $stmtPts->bind_param("iii",$resposta_id,$resultado_id,$resp['pontos']);
        $stmtPts->execute();
    }
}

$conexao->commit();

echo "<script>alert('Quiz criado com sucesso!');</script>";
