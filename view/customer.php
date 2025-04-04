<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Clientes</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h1>Gerenciamento de Clientes</h1>

        <div class="form-container">
            <div class="cad-group">
                <div class="input-container">
                    <label for="name">Nome</label>
                    <input type="text" id="name" placeholder="Digite o nome">
                </div>
                <div class="input-container">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="Digite o email">
                </div>
                <div class="input-container">
                    <label for="phone">Telefone</label>
                    <input type="text" id="phone" placeholder="Digite o telefone">
                </div>
                <div class="input-container">
                    <label for="zipcode">CEP</label>
                    <input type="text" id="zipcode" placeholder="Digite o CEP">
                </div>
                <div class="input-container">
                    <label for="fk_idcity">Cidade</label>
                    <select id="fk_idcity"></select>
                </div>
                <div class="input-container">
                    <label for="state">Estado</label>
                    <input type="text" id="state" placeholder="Digite o estado">
                </div>
                <div class="input-container">
                    <label for="dtbirth">Data de Nascimento</label>
                    <input type="date" id="dtbirth">
                </div>
                <div class="input-container">
                    <label for="active">Ativo</label>
                    <select id="active">
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <button id="saveBtn" onclick="saveCustomer()">Adicionar Cliente</button>
                <button id="backButton">Voltar ao Menu Principal</button>
            </div>
        </div>

        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Cidade</th>
                        <th>Estado</th>
                        <th>Ativo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="customer_data"></tbody>
            </table>
        </div>
    </div>

    <script src="../js/customer.js"></script>
</body>

</html>