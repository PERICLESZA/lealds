<?php

include '../controller/auth.php';
include '../controller/dsmonthcontroller.php';

$start_date = $_GET['start_date'] ?? '2025-01-01';
$end_date = $_GET['end_date'] ?? '2025-12-31';

$monthly_totals = getDataForDsMonth($connections, $start_date, $end_date);

// Função para converter nome do banco
function getDbName($db)
{
    $db_names = [
        "cedroibr"  => "Leal",
        "cedroibr2" => "Leal Filial",
        "cedroibr3" => "MB",
        "cedroibr4" => "Leal4"
    ];
    return $db_names[$db] ?? $db;
}

// Função para gerar os meses do período
function generateMonths($start_date, $end_date)
{
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $months = [];

    while ($start <= $end) {
        $months[] = $start->format('Y-m');
        $start->modify('+1 month');
    }

    return $months;
}

$months = generateMonths($start_date, $end_date);

// Calcular totais por mês
$monthly_sums = array_fill_keys($months, 0); // Inicializar soma dos meses com zero

foreach ($monthly_totals as $db_name => $monthly_data) {
    foreach ($months as $month) {
        $value = isset($monthly_data[$month]) ? (float) $monthly_data[$month] : 0;
        $monthly_sums[$month] += $value; // Soma corretamente
    }
}


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Totais Mensais</title>
    <link rel="stylesheet" href="../css/ds.css">
</head>

<body>
    <div class="dashboard-container">
        <h1>Fluxo Mensal</h1>

        <div class="form-container">
            <label for="start_date">Data de Início:</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>

            <label for="end_date">Data de Fim:</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>


            <div class="button-group">
                <button id="fetchButton">Recalcular</button>
                <!-- <button id="recalcularButton" onclick="fetchData()">Recalcular</button> -->
                <button id="backButton">Voltar ao Menu Principal</button>
            </div>

        </div>

        <!-- <h2>Relatório de Fluxo de <?= htmlspecialchars($start_date) ?> à <?= htmlspecialchars($end_date) ?></h2> -->

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Loja</th>
                        <?php foreach ($months as $month): ?>
                            <th><?= date('M', strtotime($month)) ?></th>
                        <?php endforeach; ?>
                        <th>Total</th> <!-- Coluna total -->
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($monthly_totals as $db_name => $monthly_data): ?>
                        <tr>
                            <td><?= htmlspecialchars(getDbName($db_name)) ?></td>
                            <?php foreach ($months as $month): ?>
                                <td><?= isset($monthly_data[$month]) ? formatCurrency((float) $monthly_data[$month]) : formatCurrency(0) ?></td>
                            <?php endforeach; ?>
                            <!-- Total por loja -->
                            <td><?= formatCurrency(array_sum(array_map(fn($value) => (float) ($value ?? 0), $monthly_data))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <!-- Linha de totais -->
                <tfoot>
                    <td><strong>Total Geral</strong></td>
                    <?php foreach ($months as $month): ?>
                        <td><strong><?= formatCurrency($monthly_sums[$month]) ?></strong></td> <!-- Total por mês -->
                    <?php endforeach; ?>
                    <!-- Total geral -->
                    <td><strong><?= formatCurrency(array_sum(array_map(fn($value) => (float) ($value ?? 0), $monthly_sums))) ?></strong></td>
                </tfoot>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById("backButton").addEventListener("click", function() {
            window.location.href = "../view/menuprincipal.php";
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const fetchButton = document.getElementById('fetchButton');
            const tableBody = document.getElementById('tableBody');

            function fetchData() {
                const start_date = startDateInput.value;
                const end_date = endDateInput.value;

                fetch(`dsmonth.php?start_date=${start_date}&end_date=${end_date}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTableBody = doc.querySelector("#tableBody");
                        const newTableFoot = doc.querySelector("tfoot"); // Pega o novo tfoot

                        //                      const newHeader = doc.querySelector("h2"); // Pega o novo cabeçalho
                        //                      if (newHeader) {
                        //                          document.querySelector("h2").innerHTML = newHeader.innerHTML; // Atualiza o cabeçalho
                        //                      }

                        if (newTableBody) {
                            tableBody.innerHTML = newTableBody.innerHTML; // Atualiza tbody
                        }

                        if (newTableFoot) {
                            document.querySelector("tfoot").innerHTML = newTableFoot.innerHTML; // Atualiza tfoot
                        }
                    })
                    .catch(error => console.error('Erro ao carregar dados:', error));
            }

            fetchButton.addEventListener('click', fetchData);

            // Timeout de inatividade
            let timeout;

            function resetTimeout() {
                clearTimeout(timeout);
                timeout = setTimeout(() => window.location.href = "../index.php", 50000);
            }

            [startDateInput, endDateInput, fetchButton].forEach(element => {
                element.addEventListener('change', resetTimeout);
                element.addEventListener('click', resetTimeout);
            });

            resetTimeout();
        });
    </script>
</body>

</html>