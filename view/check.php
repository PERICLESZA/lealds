<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h1>Check</h1>

        <div class="form-container">
            <div class="cad-group">
                <div class="input-container">
                    <label for="valueflow">Value</label>
                    <input type="text" id="valueflow">
                </div>
                <div class="input-container">
                    <label>Date</label>
                    <input type="date" id="dtcashflow" tabindex="-1" disabled />
                </div>
                <div class="input-container">
                    <label>Time</label>
                    <input type="text" id="tchaflow" tabindex="-1" disabled />
                </div>
            </div>
            <div class="cad-group">
                <div class="input-container">
                    <label for="fk_idstatus">Status</label>
                    <select id="fk_idstatus"></select>
                </div>
                <div class="input-container full-width">
                    <label for="attention">Obs</label>
                    <input type="text" id="attention" placeholder="Description">
                </div>
            </div>
            <input type="hidden" id="idcustomer">
            <div class="button-group">
                <button id="saveBtn" onclick="saveCustomer()">Adicionar Cliente</button>
                <button id="backButton">Voltar ao Menu Principal</button>
            </div>
        </div>

    </div>

    <script src="../js/check.js"></script>
</body>

</html>