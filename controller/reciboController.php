<?php
include '../connection/lealds.php';
include '../connection/connect.php';
$conn = $connections['cedroibr7'];

header('Content-Type: application/json');
echo json_encode(getReceiptData($conn));

function getReceiptData($conn)
{
    $id = $_GET['id'] ?? null;
    $cashflowok = $_GET['cashflowok'] ?? '2';
    if (!$id) return ['error' => 'ID inválido'];

    $stmt = $conn->prepare("SELECT name FROM customer WHERE idcustomer = :id");
    $stmt->execute([':id' => $id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    $sqlFlow = "SELECT valueflow, percentflow, subtotalflow FROM cashflow 
              WHERE fk_idcustomer = :id AND excluido = 0";
    if ($cashflowok === '0' || $cashflowok === '1') {
        $sqlFlow .= " AND cashflowok = :ok";
    }
    $sqlFlow .= " ORDER BY dtcashflow DESC";

    $stmt = $conn->prepare($sqlFlow);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    if ($cashflowok === '0' || $cashflowok === '1') {
        $stmt->bindValue(':ok', $cashflowok, PDO::PARAM_INT);
    }
    $stmt->execute();
    $cashflows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = array_sum(array_column($cashflows, 'subtotalflow'));
    return ['customer' => $customer, 'cashflows' => $cashflows, 'total' => $total];
}
