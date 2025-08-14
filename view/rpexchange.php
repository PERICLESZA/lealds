<?php
include '../connection/lealds.php';
include '../connection/connect.php';
date_default_timezone_set('America/Sao_Paulo');

$conn = $connections['cedroibr7'];
$conn->exec("SET time_zone = '-03:00'");

// Período
$dataInicio = $_GET['inicio'] ?? date('Y-m-01');
$dataFim = $_GET['fim'] ?? date('Y-m-t');

// Consulta
$sql = "SELECT 
    c.idcashflow,
    cust.name AS name,
    c.dtcashflow,
    c.tchaflow,
    c.valueflow,
    c.centsflow,
    c.percentflow,
    c.valuepercentflow,
    c.cents2flow,
    c.totalflow,
    c.totaltopay,
    c.cashflowok,
    c.idlogin
FROM cashflow c
LEFT JOIN customer cust ON cust.idcustomer = c.fk_idcustomer
WHERE c.dtcashflow BETWEEN :inicio AND :fim
ORDER BY c.dtcashflow, c.tchaflow";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':inicio', $dataInicio);
$stmt->bindValue(':fim', $dataFim);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Exchange Chronological Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: right;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr.red-row {
            color: red;
        }

        td:first-child,
        td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>

    <h2>Exchange Chronological Report<br>
        Period <?= date('d/m/Y', strtotime($dataInicio)) ?> to <?= date('d/m/Y', strtotime($dataFim)) ?></h2>

    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Date</th>
                <th>Time</th>
                <th>N</th>
                <th>Value</th>
                <th>Cents1</th>
                <th>%</th>
                <th>% Value</th>
                <th>Cents 2</th>
                <th>Receive</th>
                <th>Pay</th>
                <th>OK</th>
                <th>Operator</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): ?>
                <tr class="<?= ($row['cashflowok'] == 4 || $row['cashflowok'] == 5) ? 'red-row' : '' ?>">
                    <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
                    <td><?= date('d/m/Y', strtotime($row['dtcashflow'])) ?></td>
                    <td><?= htmlspecialchars($row['tchaflow']) ?></td>
                    <td><?= $row['idcashflow'] ?></td>
                    <td><?= number_format($row['valueflow'], 2, ',', '.') ?></td>
                    <td><?= number_format($row['centsflow'], 2, ',', '.') ?></td>
                    <td><?= number_format($row['percentflow'], 2, ',', '.') ?></td>
                    <td><?= number_format($row['valuepercentflow'], 2, ',', '.') ?></td>
                    <td><?= number_format($row['cents2flow'], 2, ',', '.') ?></td>
                    <td><?= number_format($row['totalflow'], 2, ',', '.') ?></td>
                    <td><?= number_format($row['totaltopay'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['cashflowok']) ?></td>
                    <td><?= htmlspecialchars($row['idlogin'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>