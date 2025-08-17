<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h2>Customer</h2>

        <div class="form-container">
            <div class="cad-group">
                <div class="input-container">
                    <label for="name">Name</label>
                    <input type="text" id="name" placeholder="Digite o nome">
                </div>
                <div class="input-container">
                    <label for="fk_idclasscustomer">Class</label>
                    <select id="fk_idclasscustomer"></select>
                </div>
                <div class="input-container">
                    <label for="andress">Address</label>
                    <input type="text" id="andress" placeholder="Address">
                </div>
                <div class="input-container">
                    <label for="zipcode">ZipCode</label>
                    <input type="text" id="zipcode" placeholder="ZipCode">
                </div>
                <div class="input-container">
                    <label for="fk_idcity">City</label>
                    <select id="fk_idcity"></select>
                </div>
            </div>
            <div class="cad-group">
                <div class="input-container">
                    <label for="state">State</label>
                    <input type="text" id="state" placeholder="State">
                </div>
                <div class="input-container">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" placeholder="Phone">
                </div>
                <div class="input-container">
                    <label for="phone2">Phone2</label>
                    <input type="text" id="phone2" placeholder="Phone">
                </div>
                <div class="input-container">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="Email">
                </div>
                <div class="input-container">
                    <label for="dtbirth">Birthday</label>
                    <input type="date" id="dtbirth">
                </div>
            </div>
            <div class="cad-group">
                <div class="input-container">
                    <label for="fk_ididentification">Identification</label>
                    <select id="fk_ididentification"></select>
                </div>
                <div class="input-container">
                    <label for="numidentification">Num Id</label>
                    <input type="text" id="numidentification" placeholder="Num id">
                </div>
                <div class="input-container">
                    <label for="fk_idcustomer">Company</label>
                    <select id="fk_idcustomer"></select>
                </div>
                <div class="input-container">
                    <label for="comissionpercent">% Comission</label>
                    <input type="number" id="comissionpercent" placeholder="%">
                </div>
                <div class="input-container">
                    <label for="active">Active</label>
                    <select id="active">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="cad-group">
                <div class="input-container">
                    <label for="restriction">Restriction</label>
                    <select id="restriction">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="input-container full-width">
                    <label for="attention">Obs</label>
                    <input type="text" id="attention" placeholder="Description">
                </div>
            </div>
            <input type="hidden" id="idcustomer">
            <div class="button-group">
                <button id="saveBtn" onclick="saveCustomer()">Add Cliente</button>
            </div>
        </div>

        <div>
            <h3>Buscar Cliente</h3>
            <input
                type="text"
                id="searchInput"
                placeholder="Enter the customer's name..."
                style="width: 300px; padding: 8px; margin-bottom: 20px;">

        </div>
        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr>
                        <th>Edit</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Active'</th>
                        <th>Del</th>
                    </tr>
                </thead>
                <tbody id="customer_data"></tbody>
            </table>
        </div>
    </div>

    <script src="../js/customer.js"></script>
</body>

</html>