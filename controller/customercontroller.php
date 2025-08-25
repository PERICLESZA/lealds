<?php

include '../connection/lealds.php';
include '../connection/connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listCustomers($conn);
        break;
    case 'create':
        createCustomer($conn);
        break;
    case 'update':
        updateCustomer($conn);
        break;
    case 'delete':
        deleteCustomer($conn);
        break;
    case 'search':
        searchCustomers($conn);
        break;
    case 'getCustomer':
        getCustomerById($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

function listCustomers($conn)
{
    $sql = "SELECT 
                c.idcustomer, 
                c.name 
            FROM 
                customer c
            LEFT JOIN classcustomer cc ON c.fk_idclasscustomer = cc.idclasscustomer
            WHERE name IS NOT NULL
            AND c.name <> ''
            AND cc.seeincompany = 1
            ORDER BY name ASC";

    $stmt = $conn->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function createCustomer($conn)
{
    $name = $_POST['name'] ?? '';
    $fk_idclasscustomer = $_POST['fk_idclasscustomer'] ?? null;
    $andress = $_POST['andress'] ?? '';
    $zipcode = $_POST['zipcode'] ?? '';
    $fk_idcity = $_POST['fk_idcity'] ?? null;
    $state = $_POST['state'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $phone2 = $_POST['phone2'] ?? '';
    $email = $_POST['email'] ?? '';
    $dtbirth = $_POST['dtbirth'] ?? null;
    $fk_ididentification = $_POST['fk_ididentification'] ?? null;
    $numidentification = $_POST['numidentification'] ?? '';
    $fk_idcustomer = $_POST['fk_idcustomer'] ?? null;
    $comissionpercent = $_POST['comissionpercent'] ?? 0;
    $active = $_POST['active'] ?? 1;
    $restriction = $_POST['restriction'] ?? 0;
    $attention = $_POST['attention'] ?? '';

    if (empty($name) || empty($email)) {
        echo json_encode(["error" => "Nome e e-mail são obrigatórios"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO customer 
        (name, fk_idclasscustomer, andress, zipcode, fk_idcity, state, phone, phone2, email, dtbirth, fk_ididentification, numidentification, fk_idcustomer, comissionpercent, active, restriction, attention) 
        VALUES 
        (:name, :fk_idclasscustomer, :andress, :zipcode, :fk_idcity, :state, :phone, :phone2, :email, :dtbirth, :fk_ididentification, :numidentification, :fk_idcustomer, :comissionpercent, :active, :restriction, :attention)");

    $stmt->execute([
        ':name' => $name,
        ':fk_idclasscustomer' => $fk_idclasscustomer,
        ':andress' => $andress,
        ':zipcode' => $zipcode,
        ':fk_idcity' => $fk_idcity,
        ':state' => $state,
        ':phone' => $phone,
        ':phone2' => $phone2,
        ':email' => $email,
        ':dtbirth' => $dtbirth,
        ':fk_ididentification' => $fk_ididentification,
        ':numidentification' => $numidentification,
        ':fk_idcustomer' => $fk_idcustomer,
        ':comissionpercent' => $comissionpercent,
        ':active' => $active,
        ':restriction' => $restriction,
        ':attention' => $attention
    ]);

    echo json_encode(["success" => "Cliente cadastrado com sucesso"]);
}

function updateCustomer($conn)
{
    $idcustomer = $_POST['idcustomer'] ?? null;

    if (!$idcustomer) {
        echo json_encode(["error" => "ID do cliente não informado"]);
        return;
    }

    // Os mesmos campos de create...
    // Aqui você pode copiar os mesmos campos e lógica de `createCustomer` e adicionar `:idcustomer` no WHERE

    $stmt = $conn->prepare("UPDATE customer SET 
        name = :name, 
        fk_idclasscustomer = :fk_idclasscustomer, 
        andress = :andress, 
        zipcode = :zipcode, 
        fk_idcity = :fk_idcity, 
        state = :state, 
        phone = :phone, 
        phone2 = :phone2, 
        email = :email, 
        dtbirth = :dtbirth, 
        fk_ididentification = :fk_ididentification, 
        numidentification = :numidentification, 
        fk_idcustomer = :fk_idcustomer, 
        comissionpercent = :comissionpercent, 
        active = :active, 
        restriction = :restriction, 
        attention = :attention 
        WHERE idcustomer = :idcustomer");

    $stmt->execute([
        ':idcustomer' => $idcustomer,
        ':name' => $_POST['name'],
        ':fk_idclasscustomer' => $_POST['fk_idclasscustomer'],
        ':andress' => $_POST['andress'],
        ':zipcode' => $_POST['zipcode'],
        ':fk_idcity' => $_POST['fk_idcity'],
        ':state' => $_POST['state'],
        ':phone' => $_POST['phone'],
        ':phone2' => $_POST['phone2'],
        ':email' => $_POST['email'],
        ':dtbirth' => $_POST['dtbirth'],
        ':fk_ididentification' => $_POST['fk_ididentification'],
        ':numidentification' => $_POST['numidentification'],
        ':fk_idcustomer' => $_POST['fk_idcustomer'],
        ':comissionpercent' => $_POST['comissionpercent'],
        ':active' => $_POST['active'],
        ':restriction' => $_POST['restriction'],
        ':attention' => $_POST['attention']
    ]);

    echo json_encode(["success" => "Cliente atualizado com sucesso"]);
}

// ❌ Exclusão de User (campo excluido)
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
function deleteCustomer($conn)
{
    $idcustomer = $_GET['idcustomer'] ?? null;

    if (!$idcustomer) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }
    try {
        $stmt = $conn->prepare("UPDATE customer SET active = '0' WHERE idcustomer = ?");
        $stmt->execute([$idcustomer]);
        echo json_encode(['success' => true, 'idcustomer' => $idcustomer]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function searchCustomers($conn)
{
    $term = $_GET['term'] ?? '';

    $sql = "SELECT 
                c.idcustomer, 
                c.name, 
                cc.description,
                c.andress, 
                c.email, 
                id.nameidentification,
                -- cp.name,
                c.phone, 
                ci.name_city, 
                c.state, 
                c.active, 
                c.fk_idcity 
            FROM 
                customer c
            LEFT JOIN city ci ON c.fk_idcity = ci.idcity
            LEFT JOIN classcustomer cc ON c.fk_idclasscustomer = cc.idclasscustomer
            LEFT JOIN identification id ON c.fk_ididentification = id.ididentification
            -- LEFT JOIN customer cp ON c.fk_idcustomer = cp.idcustomer
            WHERE 
                c.name LIKE :term 
                AND c.name IS NOT NULL 
                AND c.name <> ''
            ORDER BY c.name ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':term' => "%$term%"]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function getCustomerById($conn)
{

    $idcustomer = $_POST['idcustomer'] ?? null;

    if (!$idcustomer) {
        echo json_encode(["error" => "ID do cliente não informado"]);
        return;
    }

    $stmt = $conn->prepare("SELECT * FROM customer WHERE idcustomer = :id");
    $stmt->execute([':id' => $idcustomer]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($customer);
    
}
