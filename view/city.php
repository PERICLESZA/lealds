<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Cidades</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Cities</h2>

        <div class="form-container">
            <label for="city_name">Nome da Cidade:</label>
            <input type="text" id="city_name" placeholder="Digite o nome da cidade">
            <div class="button-group">
                <button id="saveBtn" onclick="saveCity()">Adicionar Cidade</button>
            </div>
        </div>

        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome da Cidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="city_data"></tbody>
            </table>
        </div>
    </div>

    <script src="../js/city.js"></script>
    
</body>

</html>