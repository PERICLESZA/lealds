<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="../css/rule.css">
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
                        <th>Rule</th>
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
    <!-- MODAL DE EDIÇÃO DE RULE -->
    <div id="editRuleModal" class="modal-overlay hidden">
        <div class="modal-box">
            <div class="form-container">
                <h2>Permissões</h2>

                <div class="modalrule-body">
                    
                    <input type="hidden" id="rule_idlogin" name="rule_idlogin">

                    <div class="cad-group">
                        
                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" id="rule_exchange" name="rule_exchange" value="0">Exchange
                            </label>
                        </div>

                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" id="rule_city" name="rule_city" value="1"> City
                            </label>
                        </div>

                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" id="rule_bank" name="rule_bank" value="1"> Bank
                            </label>
                        </div>

                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" id="rule_overview" name="rule_overview" value="1"> Overview
                            </label>
                        </div>

                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" id="rule_monthly" name="rule_monthly"
                                    value="1">MonthlyOverview
                            </label>
                        </div>

                    </div>


                    <div class="cad-group">

                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" id="rule_classcustomer" name="rule_classcustomer">ClassCustomer
                            </label>
                        </div>

                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" id="rule_customer" name="rule_customer"> Customer
                            </label>
                        </div>

                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" id="rule_identification" name="rule_identification" value="1">
                                Identification
                            </label>
                        </div>
                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" id="rule_user" name="rule_user" value="1"> User
                            </label>
                        </div>

                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" id="rule_report" name="rule_report" value="1"> Report
                            </label>
                        </div>

                    </div>

                </div>

                <div class="modalrule-footer">
                    <button onclick="updateRule()">Save</button>
                    <button onclick="closeEditRuleModal()">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/user.js"></script>
    <script src="../js/usermodal.js"></script>
</body>

</html>