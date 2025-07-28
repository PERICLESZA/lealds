<?php

include '../connection/lealds.php';
include '../connection/connect.php';
date_default_timezone_set('America/Sao_Paulo');

$conn = $connections['cedroibr7'];
$conn->exec("SET time_zone = '-03:00'"); // <- AGORA SIM NA CONEXÃO CERTA

$verifica = $conn->query("SELECT NOW() AS agora")->fetch();
// file_put_contents('log_cashflow.txt', "NOW MySQL: {$verifica['agora']}" . PHP_EOL, FILE_APPEND);


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
    case 'search_bank':
        searchBank($conn);
        break;
    case 'wire_value':
        echo getWireValue($conn);
        break;
    case 'exchangepercent':
        $p = getExchangeComission($conn);      // ou PDO dependendo da configuração
        echo json_encode(['percent' => $p]);
        break;
    case 'calculate':
        $value = isset($_POST['value']) ? floatval($_POST['value']) : 0;
        $percent = isset($_POST['percent']) ? floatval($_POST['percent']) : 0;
        $result = calculateCashflowValues($conn, $value, $percent);
        // header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    case 'delete':
        $idcashflow = $_GET['id'] ?? 0;
        $result = deleteCashflowById($conn, $idcashflow);
        echo json_encode($result);
        exit;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

// ❌ Exclusão de lançamento
function deleteCashflowById($conn, $id)
{
    try {
        $stmt = $conn->prepare("DELETE FROM cashflow WHERE idcashflow = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function getWireValue($conn)
{
    $stmt = $conn->query("SELECT exchange_vl_wire FROM parameters LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return json_encode(['wire' => $result['exchange_vl_wire'] ?? 0]);
}

function searchBank($conn)
{
    $term = $_GET['term'] ?? '';
    $sql = "SELECT idbank, namebank
            FROM bank
            WHERE namebank LIKE :term
            ORDER BY namebank ASC
            LIMIT 10";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':term' => "%$term%"]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
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

    $sql = "SELECT idcashflow, valueflow, centsflow, percentflow, 
                   valuepercentflow, subtotalflow, cents2flow, 
                   wire, cashflowok, dtcashflow, tchaflow,
                   totalflow, totaltopay
            FROM cashflow
            WHERE fk_idcustomer = :id
            ORDER BY dtcashflow DESC, tchaflow DESC";

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
function calculateCashflowValues($conn, float $value, float $percent): array
{
    $centsflow = $value - floor($value);
    $value_base = floor($value);
    $valuepercentflow = round($value_base * ($percent / 100), 2);
    $tfWire = getWireValue($conn);

    // $subtotalflow = $value_base - $valuepercentflow;
    

    if ($value <= 200) {
        $valuepercentflow = 3;
        $percent = 2;
    } else {
        $valuepercentflow = round(($value * ($percent / 100)), 2); // 2.36 * 1.50 % = 0.03
        $valuepercentflow = ($percent == 0) ? 3 : $valuepercentflow;
        $valuepercentflow = number_format($valuepercentflow, 2);
    }

    // $totalflow = $value - ($centsflow - $valuepercentflow);
    // $totaltopay = $value - $totalflow;

    $subtotalflow = number_format($value - ($centsflow + $valuepercentflow), 2); // 2.36 - 0.36 - 0.04 = 1.96
    
    $cents2flow = $subtotalflow - floor($subtotalflow);

    // Valor a receber
    $totalflow = $centsflow + $cents2flow + $valuepercentflow ;
    // Total a pagar
    $totaltopay = $value - ($centsflow + $valuepercentflow + $cents2flow);

    return [
        'valueflow' => $value,
        'centsflow' => $centsflow,
        'valuepercentflow' => $valuepercentflow,
        'cents2flow' => $cents2flow,
        'percentflow' => $percent,
        'subtotalflow' => $subtotalflow,
        'totalflow' => $totalflow,
        'totaltopay' => $totaltopay
    ];
}

// 💾 Insere os dados calculados no banco de dados
function insertCashflow(PDO $conn, array $data): bool
{
    // file_put_contents('log_cashflow.txt', "insertCashflow chamada em " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
    // Usa fuso horário explicitamente
    $dt = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    $dataAtual = $data['dtcashflow'] ?? $dt->format('Y-m-d'); // fallback se não vier nada
    $horaAtual = $dt->format('H:i:s');

    // echo "Data gerada: $dataAtual<br>Hora gerada: $horaAtual";
    // exit;

    $stmt = $conn->prepare("
        INSERT INTO cashflow 
        (
            valueflow, centsflow, valuepercentflow, cents2flow, 
            percentflow, totalflow, totaltopay, dtcashflow, tchaflow, subtotalflow,
            fk_idcustomer, fk_idbankmaster
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    // echo "<pre>";
    // print_r([
    //     'dataAtual' => $dataAtual,
    //     'horaAtual' => $horaAtual,
    //     'datetime PHP' => $dt->format('Y-m-d H:i:s')
    // ]);
    // echo "</pre>";
    // exit;

    return $stmt->execute([
        $data['valueflow'],
        $data['centsflow'],
        $data['valuepercentflow'],
        $data['cents2flow'],
        $data['percentflow'],
        $data['totalflow'],
        $data['totaltopay'],
        $dataAtual,  // <- agora correta
        $horaAtual,
        $data['subtotalflow'],
        $data['fk_idcustomer'],
        $data['fk_idbankmaster']
    ]);
}

// 🚀 Função principal que trata a action 'insert'
function handleInsertCashflow(PDO $conn): void
{
    $value = isset($_POST['value']) ? floatval($_POST['value']) : 0;
    
    $percent = getExchangeComission($conn);
    $cashflowData = calculateCashflowValues($conn, $value, $percent);

    // Gera a data e hora corretas no backend
    $dt = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    $cashflowData['dtcashflow'] = $dt->format('Y-m-d');
    $cashflowData['tchaflow'] = $dt->format('H:i:s');

    $cashflowData['fk_idcustomer'] = $_POST['fk_idcustomer'] ?? null;
    $cashflowData['fk_idbankmaster'] = $_POST['fk_idbankmaster'] ?? null;

    $success = insertCashflow($conn, $cashflowData);

    echo json_encode(['success' => $success]);
    exit;
}
