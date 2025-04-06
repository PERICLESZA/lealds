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

    default:
        echo json_encode(["error" => "Ação inválida"]);
}


function listCustomers($conn)
{
    $sql = "SELECT 
                idcustomer, name, andress, email, phone, fk_idcity, state, active 
            FROM 
                customer 
            WHERE name IS NOT null AND name <> ''
            ORDER BY LOWER(name) ASC
            LIMIT 0";
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

function deleteCustomer($conn)
{
    $idcustomer = $_POST['idcustomer'] ?? null;
    if (!$idcustomer) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM customer WHERE idcustomer = :idcustomer");
    $stmt->bindParam(":idcustomer", $idcustomer);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Cliente excluído com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao excluir cliente"]);
    }
}

function searchCustomers($conn)
{
    $term = $_GET['term'] ?? '';

    $sql = "SELECT 
                idcustomer, name, andress, email, phone, fk_idcity, state, active 
            FROM 
                customer 
            WHERE 
                name LIKE :term 
                AND name IS NOT NULL 
                AND name <> ''
            ORDER BY name ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':term' => "%$term%"]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

