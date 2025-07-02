<?php
include '../connection/lealds.php';
include '../connection/connect.php';

$conn = $connections['cedroibr'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'search':
        searchCustomer($conn);
        break;
    case 'cashflow':
        getCashflowByCustomer($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

function searchCustomer($conn)
{
    $term = $_GET['term'] ?? '';
    $sql = "SELECT idcustomer, name, phone
            FROM customer
            WHERE (name LIKE :term OR phone LIKE :term)
              AND name IS NOT NULL AND name <> ''
            ORDER BY name ASC
            LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':term' => "%$term%"]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function getCashflowByCustomer($conn)
{
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(["error" => "ID não informado"]);
        return;
    }

    $sql = "SELECT valueflow, centsflow, percentflow, valuepercentflow, subtotalflow, cents2flow, wire, cashflowok
            FROM cashflow
            WHERE fk_idcustomer = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

