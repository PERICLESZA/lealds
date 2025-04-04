<?php

include '../connection/lealds.php';
include '../connection/connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listCities($conn);
        break;
    case 'create':
        createCity($conn);
        break;
    case 'update':
        updateCity($conn);
        break;
    case 'delete':
        deleteCity($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

function listCities($conn)
{
    $sql = "SELECT idcity, name_city FROM city ORDER BY name_city ASC";
    $stmt = $conn->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function createCity($conn)
{
    $name_city = $_POST['name_city'] ?? '';
    if (empty($name_city)) {
        echo json_encode(["error" => "O nome da cidade não pode estar vazio"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO city (name_city) VALUES (:name_city)");
    $stmt->bindParam(":name_city", $name_city);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Cidade adicionada com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao adicionar cidade"]);
    }
}

function updateCity($conn)
{
    $idcity = $_POST['idcity'] ?? '';
    $name_city = $_POST['name_city'] ?? '';

    if (empty($idcity) || empty($name_city)) {
        echo json_encode(["error" => "Dados incompletos"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE city SET name_city = :name_city WHERE idcity = :idcity");
    $stmt->bindParam(":name_city", $name_city);
    $stmt->bindParam(":idcity", $idcity);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Cidade atualizada com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao atualizar cidade"]);
    }
}

function deleteCity($conn)
{
    $idcity = $_POST['idcity'] ?? '';
    if (empty($idcity)) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM city WHERE idcity = :idcity");
    $stmt->bindParam(":idcity", $idcity);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Cidade excluída com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao excluir cidade"]);
    }
}
