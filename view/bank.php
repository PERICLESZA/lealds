<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Bancos</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <!-- Adicionar Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h1>Gerenciamento de Bancos</h1>

        <div class="form-container">
            <label for="bank_name">Nome do Banco:</label>
            <input type="text" id="bank_name" placeholder="Digite o nome do banco">
            <label for="agency">Agência:</label>
            <input type="text" id="agency" placeholder="Digite a agência">
            <label for="count">Conta:</label>
            <input type="text" id="count" placeholder="Digite a conta">

            <div class="button-group">
                <button id="saveBtn">Adicionar Banco</button>
                <button id="backButton">Voltar ao Menu Principal</button>
            </div>
        </div>

        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Banco</th>
                        <th>Agência</th>
                        <th>Conta</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="bank_data"></tbody>
            </table>
        </div>
    </div>

    <script src="../js/bank.js"></script> <!-- Inclui o JavaScript externo -->
</body>

</html>