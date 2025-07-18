<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Identificações</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <!-- Adicionar Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Exibir os botões lado a lado, com o botão de ação ocupando o espaço restante */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .button-group #saveIdentificationBtn {
            flex: 1;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <h1>Gerenciamento de Identificações</h1>

        <div class="form-container">
            <label for="identification_name">Nome da Identificação:</label>
            <input type="text" id="identification_name" placeholder="Digite o nome da identificação">
            <!-- Container com os botões lado a lado -->
            <div class="button-group">
                <button id="saveIdentificationBtn" onclick="saveIdentification()">Adicionar Identificação</button>
                <button id="backButton">Voltar ao Menu Principal</button>
            </div>
        </div>

        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome da Identificação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="identification_data"></tbody>
            </table>
        </div>
    </div>

    <script src="../js/identification.js"></script>

</body>

</html>