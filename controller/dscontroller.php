<?php

include '../controller/auth.php';
// Incluir a configuração do lealds.php
include '../connection/lealds.php';  // Incluindo a configuração de conexões com os bancos

// Pegando as datas do request (com valores padrão caso não sejam enviadas)
$start_date = $_GET['start_date'] ?? '2025-01-01';
$end_date = $_GET['end_date'] ?? '2025-01-31';

// Função para formatar valores em dólares
function formatCurrency($value)
{
    return "$" . number_format((float)$value, 2, '.', ',');
}

// Função para buscar os totais dos bancos
function getTotals($connection, $start_date, $end_date)
{
    try {
        $stmt = $connection->prepare("
            SELECT 
                SUM(valueflow) AS valueflow,
                SUM(centsflow) AS centsflow,
                SUM(valuepercentflow) AS valuepercentflow,
                SUM(cents2flow) AS cents2flow,
                SUM(totalflow) AS totalflow,
                SUM(totaltopay) AS totaltopay
            FROM cashflow 
            WHERE fk_idcustomer IS NOT NULL 
            AND dtcashflow BETWEEN :start_date AND :end_date
        ");

        $stmt->execute([
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'valueflow' => formatCurrency($result['valueflow'] ?? 0),
            'centsflow' => formatCurrency($result['centsflow'] ?? 0),
            'valuepercentflow' => formatCurrency($result['valuepercentflow'] ?? 0),
            'cents2flow' => formatCurrency($result['cents2flow'] ?? 0),
            'totalflow' => formatCurrency($result['totalflow'] ?? 0),
            'totaltopay' => formatCurrency($result['totaltopay'] ?? 0)
        ];
    } catch (PDOException $e) {
        return "Erro: " . $e->getMessage();
    }
}

// Obter os totais dos bancos a partir das conexões
$totals = [];
$grand_totals = ['valueflow' => 0, 'centsflow' => 0, 'valuepercentflow' => 0, 'cents2flow' => 0, 'totalflow' => 0, 'totaltopay' => 0];

foreach ($connections as $db_name => $connection) {
    $totals[$db_name] = getTotals($connection, $start_date, $end_date);

    // Somando os valores totais gerais (convertendo para número antes de somar)
    foreach ($totals[$db_name] as $key => $value) {
        $grand_totals[$key] += (float) str_replace(['$', ','], '', $value);
    }
}

// Formatar os valores totais gerais em dólar
foreach ($grand_totals as $key => $value) {
    $grand_totals[$key] = formatCurrency($value);
}

// Retornar os dados em JSON
header('Content-Type: application/json');
echo json_encode(['totals' => $totals, 'grand_totals' => $grand_totals]);
