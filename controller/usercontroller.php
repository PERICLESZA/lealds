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
        $idlogin = $_GET['idlogin'] ?? 0;
        $result = deleteUserById($conn, $idlogin);
        echo json_encode($result);
        break;
    case 'getRule':
        $idlogin = $_GET['idlogin'] ?? 0;
        getRule($conn, $idlogin);
        break;
    case 'updateRule':
        updateRule($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

// ❌ Exclusão de lançamento
function deleteUserById($conn, $id)
{
    try {
        $stmt = $conn->prepare("UPDATE login SET excluido = 1 WHERE idlogin = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function listUsers($conn)
{
    $sql = "SELECT idlogin, login, nome, email, perfil, active FROM login 
    WHERE excluido=0
    ORDER BY nome ASC";
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

function getRule($conn, $idlogin)
{
    $stmt = $conn->prepare("SELECT * FROM rule WHERE idlogin = ?");
    $stmt->execute([$idlogin]);
    $rule = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($rule ?: []);
}

function updateRule($conn)
{
    $idlogin = $_POST['idlogin'];

    $sql = "UPDATE rule SET 
        Exchange = :Exchange,
        City = :City,
        Bank = :Bank,
        Overwiew = :Overwiew,
        MonthlyOverview = :MonthlyOverview,
        ClassCustomer = :ClassCustomer,
        Customer = :Customer,
        Identification = :Identification,
        User = :User,
        Report = :Report
        WHERE idlogin = :idlogin";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':Exchange' => $_POST['Exchange'],
        ':City' => $_POST['City'],
        ':Bank' => $_POST['Bank'],
        ':Overwiew' => $_POST['Overwiew'],
        ':MonthlyOverview' => $_POST['MonthlyOverview'],
        ':ClassCustomer' => $_POST['ClassCustomer'],
        ':Customer' => $_POST['Customer'],
        ':Identification' => $_POST['Identification'],
        ':User' => $_POST['User'],
        ':Report' => $_POST['Report'],
        ':idlogin' => $idlogin
    ]);

    echo json_encode(['success' => true]);
}
