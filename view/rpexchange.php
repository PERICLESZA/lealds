<?php
include '../connection/lealds.php';
include '../connection/connect.php';
date_default_timezone_set('America/Sao_Paulo');

$conn = $connections['cedroibr7'];
$conn->exec("SET time_zone = '-03:00'");

// Recebe parâmetros do formulário
$dataInicio = $_GET['inicio'] ?? date('Y-m-01');
$dataFim = $_GET['fim'] ?? date('Y-m-t');
$statusSelecionado = $_GET['fk_idstatus'] ?? '';
$deleted = $_GET['deleted'] ?? '';


// Ajusta datas
$dataInicioCompleta = $dataInicio . ' 00:00:00';
$dataFimCompleta = $dataFim . ' 23:59:59';

// Monta SQL base
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
    c.fk_idstatus,
    s.description,
    c.excluido
FROM cashflow c
LEFT JOIN customer cust ON cust.idcustomer = c.fk_idcustomer
LEFT JOIN status s ON s.idstatus = c.fk_idstatus
WHERE c.dtcashflow BETWEEN :inicio AND :fim";

// Se o status foi informado, filtra também
if (!empty($statusSelecionado)) {
    $sql .= " AND c.fk_idstatus = :status";
}

if ($deleted !== '') {
    $sql .= " AND c.excluido = :deleted";
}

$sql .= " ORDER BY c.dtcashflow DESC, c.tchaflow DESC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':inicio', $dataInicioCompleta);
$stmt->bindValue(':fim', $dataFimCompleta);

if (!empty($statusSelecionado)) {
    $stmt->bindValue(':status', $statusSelecionado, PDO::PARAM_INT);
}

if ($deleted !== '') {
    $stmt->bindValue(':deleted', $deleted, PDO::PARAM_INT);
}
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar todos os status para preencher o <select>
$sqlStatus = "SELECT idstatus, description FROM status ORDER BY description";
$statusStmt = $conn->query($sqlStatus);
$statusList = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

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
        <h2>Exchange Chronological Report - 
            <?= date('d/m/Y', strtotime($dataInicio)) ?> a
            <?= date('d/m/Y', strtotime($dataFim)) ?>
        </h2>

        <form method="get" class="form-container">
            <div class="cad-group">
                <div class="input-container">
                    <label>Start date</label>
                    <input type="date" name="inicio" value="<?= htmlspecialchars($dataInicio) ?>">
                </div>
                <div class="input-container">
                    <label>End date</label>
                    <input type="date" name="fim" value="<?= htmlspecialchars($dataFim) ?>">
                </div>
                <div class="input-container">
                    <label for="fk_idstatus">Status</label>
                    <select id="fk_idstatus" name="fk_idstatus">
                        <option value="">-- All --</option>
                        <?php foreach ($statusList as $status): ?>
                            <option value="<?= $status['idstatus'] ?>" <?= ($statusSelecionado == $status['idstatus']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['description']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-container">
                    <label for="deleted">Deleted</label>
                    <select id="deleted" name="deleted">
                        <option value="">-- All --</option>
                        <option value="1" <?= ($deleted === '1') ? 'selected' : '' ?>>Yes</option>
                        <option value="0" <?= ($deleted === '0') ? 'selected' : '' ?>>No</option>
                    </select>

                </div>
            </div>
            <button type="submit">Filter</button>
        </form>

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
                        <th>Status</th>
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
                        <tr class="<?= ($row['fk_idstatus'] == 4 || $row['fk_idstatus'] == 5 || $row['fk_idstatus'] == 7) ? 'red-row' : '' ?>">
                            <td style="text-align:left;"><?= htmlspecialchars($row['name'] ?? '') ?></td>
                            <td><?= $row['dtcashflow'] ?></td>
                            <td><?= !empty($row['tchaflow']) ? date('H:i', strtotime($row['tchaflow'])) : '' ?></td>
                            <td><?= $row['idcashflow'] ?></td>
                            <td><?= number_format((float) $row['valueflow'], 2, ',', '.') ?></td>
                            <td><?= number_format((float) $row['centsflow'], 2, ',', '.') ?></td>
                            <td><?= number_format((float) $row['percentflow'], 2, ',', '.') ?></td>
                            <td><?= number_format((float) $row['valuepercentflow'], 2, ',', '.') ?></td>
                            <td><?= number_format((float) $row['cents2flow'], 2, ',', '.') ?></td>
                            <td><?= number_format((float) $row['totalflow'], 2, ',', '.') ?></td>
                            <td><?= number_format((float) $row['totaltopay'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['cashflowok']) ?></td>
                            <td><?= htmlspecialchars($row['idlogin'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['description'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align:left;">TOTAL</th>
                        <th><?= number_format($totalValue, 2, ',', '.') ?></th>
                        <th><?= number_format($totalCents1, 2, ',', '.') ?></th>
                        <th></th>
                        <th><?= number_format($totalValuePercent, 2, ',', '.') ?></th>
                        <th><?= number_format($totalCents2, 2, ',', '.') ?></th>
                        <th><?= number_format($totalReceive, 2, ',', '.') ?></th>
                        <th><?= number_format($totalPay, 2, ',', '.') ?></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</body>

</html>