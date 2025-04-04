<?php

include '../connection/lealds.php';
include '../connection/connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listClassCustomers($conn);
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

function listClassCustomers($conn)
{
    $sql = "SELECT idclasscustomer, description, seeincompany FROM classcustomer ORDER BY description ASC";
    $stmt = $conn->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function createClassCustomer($conn)
{
    $description = $_POST['description'] ?? '';
    $seeincompany = $_POST['seeincompany'] ?? '0';

    if (empty($description)) {
        echo json_encode(["error" => "A descrição não pode estar vazia"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO classcustomer (description, seeincompany) VALUES (:description, :seeincompany)");
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":seeincompany", $seeincompany);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Categoria de cliente adicionada com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao adicionar categoria de cliente"]);
    }
}

function updateClassCustomer($conn)
{
    $idclasscustomer = $_POST['idclasscustomer'] ?? '';
    $description = $_POST['description'] ?? '';
    $seeincompany = $_POST['seeincompany'] ?? '0';

    if (empty($idclasscustomer) || empty($description)) {
        echo json_encode(["error" => "Dados incompletos"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE classcustomer SET description = :description, seeincompany = :seeincompany WHERE idclasscustomer = :idclasscustomer");
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":seeincompany", $seeincompany);
    $stmt->bindParam(":idclasscustomer", $idclasscustomer);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Categoria de cliente atualizada com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao atualizar categoria de cliente"]);
    }
}

function deleteClassCustomer($conn)
{
    $idclasscustomer = $_POST['idclasscustomer'] ?? '';
    if (empty($idclasscustomer)) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM classcustomer WHERE idclasscustomer = :idclasscustomer");
    $stmt->bindParam(":idclasscustomer", $idclasscustomer);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Categoria de cliente excluída com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao excluir categoria de cliente"]);
    }
}
