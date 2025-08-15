<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Classes de Clientes</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Customer Class</h2>

        <div class="form-container">

            <div class="cad-group">
                <div class="input-container">
                    <label for="description">Descrição</label>
                    <input type="text" id="description" placeholder="Digite a descrição">
                </div>

                <div class="input-container">
                    <label for="seeincompany">Visível na Empresa</label>
                    <select id="seeincompany">
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
            </div>


            <div class="button-group">
                <button id="saveBtn" onclick="saveClassCustomer()">Adicionar Classe</button>
            </div>

        </div>

        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição</th>
                        <th>Visível na Empresa</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="classcustomer_data"></tbody>
            </table>
        </div>
    </div>

    <script src="../js/classcustomer.js"></script>
</body>

</html>