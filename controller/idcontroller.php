<?php

include '../connection/lealds.php';
include '../connection/connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listIdentifications($conn);
        break;
    case 'create':
        createIdentification($conn);
        break;
    case 'update':
        updateIdentification($conn);
        break;
    case 'delete':
        deleteIdentification($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

function listIdentifications($conn)
{
    $sql = "SELECT ididentification, nameidentification FROM identification ORDER BY nameidentification ASC";
    $stmt = $conn->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function createIdentification($conn)
{
    $nameidentification = $_POST['nameidentification'] ?? '';
    if (empty($nameidentification)) {
        echo json_encode(["error" => "O nome da identificação não pode estar vazio"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO identification (nameidentification) VALUES (:nameidentification)");
    $stmt->bindParam(":nameidentification", $nameidentification);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Identificação adicionada com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao adicionar identificação"]);
    }
}

function updateIdentification($conn)
{
    $ididentification = $_POST['ididentification'] ?? '';
    $nameidentification = $_POST['nameidentification'] ?? '';

    if (empty($ididentification) || empty($nameidentification)) {
        echo json_encode(["error" => "Dados incompletos"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE identification SET nameidentification = :nameidentification WHERE ididentification = :ididentification");
    $stmt->bindParam(":nameidentification", $nameidentification);
    $stmt->bindParam(":ididentification", $ididentification);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Identificação atualizada com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao atualizar identificação"]);
    }
}

function deleteIdentification($conn)
{
    $ididentification = $_POST['ididentification'] ?? '';
    if (empty($ididentification)) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM identification WHERE ididentification = :ididentification");
    $stmt->bindParam(":ididentification", $ididentification);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Identificação excluída com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao excluir identificação"]);
    }
}
