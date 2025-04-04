<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Usuários</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h1>Gerenciamento de Usuários</h1>

        <div class="form-container">
            <div class="cad-group">
                <div class="input-container">
                    <label for="login">Login</label>
                    <input type="text" id="login" placeholder="Digite o login">
                </div>
                <div class="input-container">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" placeholder="Digite a senha">
                </div>
                <div class="input-container">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" placeholder="Digite o nome">
                </div>
                <div class="input-container">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="Digite o email">
                </div>
                <div class="input-container">
                    <label for="perfil">Perfil</label>
                    <select id="perfil">
                        <option value="A">Administrador</option>
                        <option value="U">Usuário</option>
                    </select>
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
                <button id="saveBtn" onclick="saveUser()">Adicionar Usuário</button>
                <button id="backButton">Voltar ao Menu Principal</button>
            </div>
        </div>

        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th>Ativo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="user_data"></tbody>
            </table>
        </div>
    </div>

    <script src="../js/user.js"></script>
</body>

</html>