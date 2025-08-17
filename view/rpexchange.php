<?php
include '../connection/lealds.php';
include '../connection/connect.php';
date_default_timezone_set('America/Sao_Paulo');

$conn = $connections['cedroibr7'];
$conn->exec("SET time_zone = '-03:00'");

// Recebe período do formulário
$dataInicio = $_GET['inicio'] ?? date('Y-m-01');
$dataFim = $_GET['fim'] ?? date('Y-m-t');

// Ajusta para incluir o dia final inteiro
$dataInicioCompleta = $dataInicio . ' 00:00:00';
$dataFimCompleta = $dataFim . ' 23:59:59';

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
    c.idlogin,
    c.fk_idstatus
FROM cashflow c
LEFT JOIN customer cust ON cust.idcustomer = c.fk_idcustomer
WHERE c.dtcashflow BETWEEN :inicio AND :fim
ORDER BY c.dtcashflow DESC, c.tchaflow DESC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':inicio', $dataInicioCompleta);
$stmt->bindValue(':fim', $dataFimCompleta);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Totais
$totalValue = 0;
$totalCents1 = 0;
$totalValuePercent = 0;
$totalCents2 = 0;
$totalReceive = 0;
$totalPay = 0;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Exchange Chronological Report</title>
    <style>
        <?php include '../css/cadastro.css'; ?>
    </style>
</head>

<body>

    <div class="dashboard-container">
        <h2>Exchange Chronological Report</h2>

        <form method="get" class="form-container">
            <div class="cad-group">
                <label>Start date:
                    <input type="date" name="inicio" value="<?= htmlspecialchars($dataInicio) ?>">
                </label>
                <label>End date:
                    <input type="date" name="fim" value="<?= htmlspecialchars($dataFim) ?>">
                </label>
            </div>
            <button type="submit">Filter</button>
        </form>

        <h3 style="text-align:center;">
            Range
            <?= date('d/m/Y', strtotime($dataInicio)) ?> a
            <?= date('d/m/Y', strtotime($dataFim)) ?>
        </h3>

        <div class="table-container">
            <table class="table">
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
                        <?php
                        $totalValue += (float) $row['valueflow'];
                        $totalCents1 += (float) $row['centsflow'];
                        $totalValuePercent += (float) $row['valuepercentflow'];
                        $totalCents2 += (float) $row['cents2flow'];
                        $totalReceive += (float) $row['totalflow'];
                        $totalPay += (float) $row['totaltopay'];
                        ?>
                        <tr class="<?= ($row['fk_idstatus'] == 4 || $row['fk_idstatus'] == 5) ? 'red-row' : '' ?>">
                            <td style="text-align:left;">
                                <?= htmlspecialchars($row['name'] ?? '') ?>
                            </td>
                            <td>
                                <?= $row['dtcashflow'] ?>
                            </td>
                            <td>
                                <?= !empty($row['tchaflow']) ? date('H:i', strtotime($row['tchaflow'])) : '' ?>
                            </td>
                            <td>
                                <?= $row['idcashflow'] ?>
                            </td>
                            <td>
                                <?= number_format((float) $row['valueflow'], 2, ',', '.') ?>
                            </td>
                            <td>
                                <?= number_format((float) $row['centsflow'], 2, ',', '.') ?>
                            </td>
                            <td>
                                <?= number_format((float) $row['percentflow'], 2, ',', '.') ?>
                            </td>
                            <td>
                                <?= number_format((float) $row['valuepercentflow'], 2, ',', '.') ?>
                            </td>
                            <td>
                                <?= number_format((float) $row['cents2flow'], 2, ',', '.') ?>
                            </td>
                            <td>
                                <?= number_format((float) $row['totalflow'], 2, ',', '.') ?>
                            </td>
                            <td>
                                <?= number_format((float) $row['totaltopay'], 2, ',', '.') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['cashflowok']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['idlogin'] ?? '') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align:left;">TOTAL</th>
                        <th>
                            <?= number_format($totalValue, 2, ',', '.') ?>
                        </th>
                        <th>
                            <?= number_format($totalCents1, 2, ',', '.') ?>
                        </th>
                        <th></th>
                        <th>
                            <?= number_format($totalValuePercent, 2, ',', '.') ?>
                        </th>
                        <th>
                            <?= number_format($totalCents2, 2, ',', '.') ?>
                        </th>
                        <th>
                            <?= number_format($totalReceive, 2, ',', '.') ?>
                        </th>
                        <th>
                            <?= number_format($totalPay, 2, ',', '.') ?>
                        </th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</body>

</html>