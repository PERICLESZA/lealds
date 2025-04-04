<?php

include '../connection/lealds.php';
include '../connection/connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listBanks($conn);
        break;
    case 'create':
        createBank($conn);
        break;
    case 'update':
        updateBank($conn);
        break;
    case 'delete':
        deleteBank($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

// Função para listar bancos
function listBanks($conn)
{
    $sql = "SELECT idbank, namebank, agency, count FROM bank ORDER BY namebank ASC";
    $stmt = $conn->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// Função para criar um novo banco
function createBank($conn)
{
    $namebank = $_POST['namebank'] ?? '';
    $agency = $_POST['agency'] ?? '';
    $count = $_POST['count'] ?? '';

    if (empty($namebank) || empty($agency) || empty($count)) {
        echo json_encode(["error" => "Todos os campos são obrigatórios"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO bank (namebank, agency, count) VALUES (:namebank, :agency, :count)");
    $stmt->bindParam(":namebank", $namebank);
    $stmt->bindParam(":agency", $agency);
    $stmt->bindParam(":count", $count);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Banco adicionado com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao adicionar banco"]);
    }
}

// Função para atualizar banco
function updateBank($conn)
{
    $idbank = $_POST['idbank'] ?? '';
    $namebank = $_POST['namebank'] ?? '';
    $agency = $_POST['agency'] ?? '';
    $count = $_POST['count'] ?? '';

    if (empty($idbank) || empty($namebank) || empty($agency) || empty($count)) {
        echo json_encode(["error" => "Dados incompletos"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE bank SET namebank = :namebank, agency = :agency, count = :count WHERE idbank = :idbank");
    $stmt->bindParam(":namebank", $namebank);
    $stmt->bindParam(":agency", $agency);
    $stmt->bindParam(":count", $count);
    $stmt->bindParam(":idbank", $idbank);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Banco atualizado com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao atualizar banco"]);
    }
}

// Função para excluir banco
function deleteBank($conn)
{
    $idbank = $_POST['idbank'] ?? '';
    if (empty($idbank)) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM bank WHERE idbank = :idbank");
    $stmt->bindParam(":idbank", $idbank);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Banco excluído com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao excluir banco"]);
    }
}
