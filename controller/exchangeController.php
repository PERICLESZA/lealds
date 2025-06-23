<?php
// exchangeController.php

include '../connection/lealds.php';
include '../connection/connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'search':
        searchCustomers($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

function searchCustomer($conn)
{
    $term = $_GET['term'] ?? '';

    $sql = "SELECT 
                c.idcustomer, 
                c.name, 
                c.phone
            FROM 
                customer c
            WHERE 
                (c.name LIKE :term OR c.phone LIKE :term)
                AND c.name IS NOT NULL 
                AND c.name <> ''
            ORDER BY c.name ASC
            LIMIT 10";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':term' => "%$term%"]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
