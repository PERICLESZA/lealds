<?php

include '../connection/lealds.php';
include '../connection/connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listUsers($conn);
        break;
    case 'create':
        createUser($conn);
        break;
    case 'update':
        updateUser($conn);
        break;
    case 'delete':
        deleteUser($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

function listUsers($conn)
{
    $sql = "SELECT idlogin, login, nome, email, perfil, active FROM login ORDER BY nome ASC";
    $stmt = $conn->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function createUser($conn)
{
    $login = $_POST['login'] ?? '';
    $senha = md5(trim($_POST['senha']));
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $perfil = $_POST['perfil'] ?? '';
    $active = $_POST['active'] ?? '0';

    if (empty($login) || empty($_POST['senha']) || empty($nome) || empty($email)) {
        echo json_encode(["error" => "Todos os campos são obrigatórios"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO login (login, senha, nome, email, perfil, active) VALUES (:login, :senha, :nome, :email, :perfil, :active)");
    $stmt->bindParam(":login", $login);
    $stmt->bindParam(":senha", $senha);
    $stmt->bindParam(":nome", $nome);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":perfil", $perfil);
    $stmt->bindParam(":active", $active);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Usuário cadastrado com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao cadastrar usuário"]);
    }
}

function updateUser($conn)
{
    $idlogin = $_POST['idlogin'] ?? '';
    $login = $_POST['login'] ?? '';
    $senha = md5(trim($_POST['senha']));
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $perfil = $_POST['perfil'] ?? '';
    $active = $_POST['active'] ?? '0';

    if (empty($idlogin) || empty($login) || empty($nome) || empty($email)) {
        echo json_encode(["error" => "Dados incompletos"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE login SET login = :login, senha = :senha, nome = :nome, email = :email, perfil = :perfil, active = :active WHERE idlogin = :idlogin");
    $stmt->bindParam(":login", $login);
    $stmt->bindParam(":senha", $senha);
    $stmt->bindParam(":nome", $nome);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":perfil", $perfil);
    $stmt->bindParam(":active", $active);
    $stmt->bindParam(":idlogin", $idlogin);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Usuário atualizado com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao atualizar usuário"]);
    }
}

function deleteUser($conn)
{
    $idlogin = $_POST['idlogin'] ?? '';
    if (empty($idlogin)) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM login WHERE idlogin = :idlogin");
    $stmt->bindParam(":idlogin", $idlogin);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Usuário excluído com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao excluir usuário"]);
    }
}
