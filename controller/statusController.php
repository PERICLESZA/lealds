<?php

include '../connection/lealds.php';
include '../connection/connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listStatus($conn);
        break;
    case 'create':
        createClassCustomer($conn);
        break;
    case 'update':
        updateClassCustomer($conn);
        break;
    case 'delete':
        deleteClassCustomer($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

function listStatus($conn)
{
    $sql = "SELECT idstatus, description, emphasis FROM status ORDER BY description ASC";
    $stmt = $conn->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function createStatus($conn)
{
    $description = $_POST['description'] ?? '';
    $emphasis = $_POST['emphasis'] ?? '0';

    if (empty($description)) {
        echo json_encode(["error" => "A descrição não pode estar vazia"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO status (description, emphasis) VALUES (:description, :emphasis)");
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":emphasis", $emphasis);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Status adicionado com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao adicionar status"]);
    }
}

function updateStatus($conn)
{
    $idstatus = $_POST['idstatus'] ?? '';
    $description = $_POST['description'] ?? '';
    $emphasis = $_POST['emphasis'] ?? '0';

    if (empty($idstatus) || empty($description)) {
        echo json_encode(["error" => "Dados incompletos"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE status SET description = :description, emphasis = :emphasis WHERE idstatus = :idstatus");
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":emphasis", $emphasis);
    $stmt->bindParam(":idstatus", $idstatus);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Status atualizado com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao atualizar status"]);
    }
}

function deleteStatus($conn)
{
    $idstatus = $_POST['idstatus'] ?? '';
    if (empty($idstatus)) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM status WHERE idstatus = :idstatus");
    $stmt->bindParam(":idstatus", $idstatus);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Status excluído com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao excluir status"]);
    }
}
