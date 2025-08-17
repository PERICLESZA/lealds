<?php include '../controller/auth.php'; 

$start_date = $_GET['start_date'] ?? '2025-01-01';
$end_date = $_GET['end_date'] ?? '2025-12-31';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Fluxo Financeiro</title>
    <link rel="stylesheet" href="../css/ds.css">
    <link rel="stylesheet" href="../css/cadastro.css">

</head>

<body>
    <div class="dashboard-container">
        <!-- Título -->
        <h2>Dashboard Cashflow</h2>

        <!-- Formulário para entrada de dados -->
        <form method="get" class="form-container">
            <div class="cad-group">
                <label>Start date:
                    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
                </label>
                <label>End date:
                    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
                </label>
            </div>
            <button type="submit">Filter</button>
        </form>

        <!-- Tabela de dados -->
        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr class="cabecalho">
                        <th>Loja</th>
                        <th>Value Flow</th>
                        <th>Cents Flow</th>
                        <th>Value Percent Flow</th>
                        <th>Cents2 Flow</th>
                        <th>Total Flow</th>
                        <th>Total to Pay</th>
                    </tr>
                </thead>
                <tbody id="data"></tbody>
                <tfoot>
                    <tr>
                        <th>Total Geral</th>
                        <th id="total_valueflow">0</th>
                        <th id="total_centsflow">0</th>
                        <th id="total_valuepercentflow">0</th>
                        <th id="total_cents2flow">0</th>
                        <th id="total_totalflow">0</th>
                        <th id="total_totaltopay">0</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        document.getElementById("backButton").addEventListener("click", function() {
            window.location.href = "../view/menuprincipal.php";
        });
    </script>

    <script>
        function fetchData() {
            let startDate = document.getElementById("start_date").value;
            let endDate = document.getElementById("end_date").value;

            fetch(`../controller/dscontroller.php?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    let tableContent = "";
                    let totals = data.totals;
                    let grandTotals = data.grand_totals;

                    for (let db in totals) {
                        let dbName = db;
                        if (db === "cedroibr") {
                            dbName = "Leal";
                        } else if (db === "cedroibr2") {
                            dbName = "Leal_filial";
                        } else if (db === "cedroibr3") {
                            dbName = "MB";
                        } else if (db === "cedroibr4") {
                            dbName = "Leal4";
                        }
                        tableContent += `
                            <tr>
                                <td>${dbName}</td>
                                <td>${totals[db].valueflow.toLocaleString()}</td>
                                <td>${totals[db].centsflow.toLocaleString()}</td>
                                <td>${totals[db].valuepercentflow.toLocaleString()}</td>
                                <td>${totals[db].cents2flow.toLocaleString()}</td>
                                <td>${totals[db].totalflow.toLocaleString()}</td>
                                <td>${totals[db].totaltopay.toLocaleString()}</td>
                            </tr>
                        `;
                    }
                    document.getElementById("data").innerHTML = tableContent;

                    // Atualizando totais gerais
                    document.getElementById("total_valueflow").innerText = grandTotals.valueflow.toLocaleString();
                    document.getElementById("total_centsflow").innerText = grandTotals.centsflow.toLocaleString();
                    document.getElementById("total_valuepercentflow").innerText = grandTotals.valuepercentflow.toLocaleString();
                    document.getElementById("total_cents2flow").innerText = grandTotals.cents2flow.toLocaleString();
                    document.getElementById("total_totalflow").innerText = grandTotals.totalflow.toLocaleString();
                    document.getElementById("total_totaltopay").innerText = grandTotals.totaltopay.toLocaleString();
                })
                .catch(error => console.error('Erro ao buscar os dados:', error));
        }

        // Chamada inicial ao carregar a página
        fetchData();
    </script>

    <script>
        let timeout;

        function resetTimeout() {
            // Limpa o timeout anterior
            clearTimeout(timeout);
            // Define um novo timeout de 30 segundos
            timeout = setTimeout(function() {
                window.location.href = "../index.php"; // Redireciona para o login.php após 30 segundos de inatividade
            }, 50000);
        }

        // Adicionando event listeners para reiniciar o timer quando o usuário interagir
        document.getElementById("start_date").addEventListener("change", resetTimeout);
        document.getElementById("end_date").addEventListener("change", resetTimeout);
        document.querySelector("button").addEventListener("click", resetTimeout);

        // Chamada inicial ao carregar a página
        resetTimeout(); // Inicia o timer assim que a página é carregada
    </script>

    </b>

</html>