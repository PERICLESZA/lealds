<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Users</h2>

        <div class="form-container">
            <div class="cad-group">
                <div class="input-container">
                    <label for="login">Login</label>
                    <input type="text" id="login" placeholder="Digite o login">
                </div>
                <div class="input-container">
                    <label for="senha">Password</label>
                    <input type="password" id="senha" placeholder="Digite a senha">
                </div>
                <div class="input-container">
                    <label for="nome">Name</label>
                    <input type="text" id="nome" placeholder="Digite o nome">
                </div>
                <div class="input-container">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="Digite o email">
                </div>
                <div class="input-container">
                    <label for="perfil">Profile</label>
                    <select id="perfil">
                        <option value="A">Admin</option>
                        <option value="U">User</option>
                    </select>
                </div>
                <div class="input-container">
                    <label for="active">Active</label>
                    <select id="active">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <button id="saveBtn" onclick="saveUser()">Add user</button>
            </div>
        </div>

        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr>
                        <th>Edit</th>
                        <th>Id</th>
                        <th>Login</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Profile</th>
                        <th>Active</th>
                        <th>Del</th>
                    </tr>
                </thead>
                <tbody id="user_data"></tbody>
            </table>
        </div>
    </div>

    <script src="../js/user.js"></script>
</body>

</html>