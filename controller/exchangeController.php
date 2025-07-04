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

    case 'insert':
        handleInsertCashflow($conn);
        break;

    default:
        echo json_encode(["error" => "Ação inválida"]);
}

// 🔍 Busca clientes por nome ou telefone
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

// 🔍 Busca cashflow por cliente
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

// 🔧 Busca o percentual de comissão da tabela parameters
function getExchangeComission(PDO $conn): float
{
    static $cachedPercent = null;

    if ($cachedPercent === null) {
        $stmt = $conn->prepare("SELECT exchange_comission FROM parameters LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cachedPercent = $row ? floatval($row['exchange_comission']) : 0.0;
    }

    return $cachedPercent;
}

// 🧮 Calcula os campos com base no valor e percentual
function calculateCashflowValues(float $value, float $percent): array
{
    $centsflow = round(($value - floor($value)) * 100);
    $value_base = floor($value);

    $valuepercentflow = round($value_base * (1 + $percent / 100), 2);
    $cents2flow = round(($valuepercentflow - floor($valuepercentflow)) * 100);

    $totalflow = $centsflow + $cents2flow + $valuepercentflow;
    $totaltopay = $value - $totalflow;

    return [
        'valueflow' => $value,
        'centsflow' => $centsflow,
        'valuepercentflow' => $valuepercentflow,
        'cents2flow' => $cents2flow,
        'percentflow' => $percent,
        'totalflow' => $totalflow,
        'totaltopay' => $totaltopay
    ];
}

// 💾 Insere os dados calculados no banco de dados
function insertCashflow(PDO $conn, array $data): bool
{
    $stmt = $conn->prepare("
        INSERT INTO cashflow 
        (valueflow, centsflow, valuepercentflow, cents2flow, percentflow, totalflow, totaltopay, dtcashflow, fk_idcustomer, fk_idbankmaster)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $data['valueflow'],
        $data['centsflow'],
        $data['valuepercentflow'],
        $data['cents2flow'],
        $data['percentflow'],
        $data['totalflow'],
        $data['totaltopay'],
        $data['dtcashflow'],
        $data['fk_idcustomer'],
        $data['fk_idbankmaster']
    ]);
}

// 🚀 Função principal que trata a action 'insert'
function handleInsertCashflow(PDO $conn): void
{
    $value = isset($_POST['value']) ? floatval($_POST['value']) : 0;

    $percent = getExchangeComission($conn);
    $cashflowData = calculateCashflowValues($value, $percent);

    $cashflowData['dtcashflow'] = $_POST['dtcashflow'] ?? null;
    $cashflowData['fk_idcustomer'] = $_POST['fk_idcustomer'] ?? null;
    $cashflowData['fk_idbankmaster'] = $_POST['fk_idbankmaster'] ?? null;

    $success = insertCashflow($conn, $cashflowData);

    echo json_encode(['success' => $success]);
    exit;
}
