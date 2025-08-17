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
        <h2>Identification</h2>

        <div class="form-container">
            <label for="identification_name">Identification:</label>
            <input type="text" id="identification_name" placeholder="Digite o nome da identificação">
            <!-- Container com os botões lado a lado -->
            <div class="button-group">
                <button id="saveIdentificationBtn" onclick="saveIdentification()">Add Identification</button>
            </div>
        </div>

        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr>
                        <th>Edit</th>
                        <th>Id</th>
                        <th>Identification name</th>
                        <th>Del</th>
                    </tr>
                </thead>
                <tbody id="identification_data"></tbody>
            </table>
        </div>
    </div>

    <script src="../js/identification.js"></script>

</body>

</html>