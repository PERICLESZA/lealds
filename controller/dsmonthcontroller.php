<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); // Redireciona para o login se não estiver autenticado
    exit();
}
// Incluir a configuração do lealds.php

include '../connection/lealds.php';  // Incluindo a configuração de conexões com os bancos

// Função para formatar valores em dólares
function formatCurrency($value)
{
    return "$" . number_format((float)$value, 2, '.', ',');
}

// Função para buscar os totais por mês e por banco
function getMonthlyTotals($connection, $start_date, $end_date)
{
    try {
        // Consultar os totais por mês e por banco
        $stmt = $connection->prepare("
            SELECT 
                YEAR(dtcashflow) AS year,
                MONTH(dtcashflow) AS month,
                COALESCE(SUM(totalflow), 0) AS totalflow
            FROM cashflow 
            WHERE fk_idcustomer IS NOT NULL 
            AND dtcashflow BETWEEN :start_date AND :end_date
            GROUP BY YEAR(dtcashflow), MONTH(dtcashflow)
            ORDER BY YEAR(dtcashflow), MONTH(dtcashflow)
        ");

        $stmt->execute([
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return "Erro: " . $e->getMessage();
    }
}

// Função para obter os totais mensais por banco de dados
function getTotalsByBank($connections, $start_date, $end_date)
{
    $totals_by_bank = [];
    $months = generateMonths($start_date, $end_date); // Garante que temos todos os meses

    foreach ($connections as $db_name => $connection) {
        $monthly_data = getMonthlyTotals($connection, $start_date, $end_date);
        $monthly_data_by_month = array_fill_keys($months, 0); // Inicializa todos os meses com 0

        foreach ($monthly_data as $data) {
            $month_key = $data['year'] . '-' . str_pad($data['month'], 2, '0', STR_PAD_LEFT);
            $monthly_data_by_month[$month_key] = (float) ($data['totalflow'] ?? 0);
        }

        $totals_by_bank[$db_name] = $monthly_data_by_month;
    }

    return $totals_by_bank;
}

// Função principal para obter dados e preparar para a view
function getDataForDsMonth($connections, $start_date, $end_date)
{
    return getTotalsByBank($connections, $start_date, $end_date);
}
?>