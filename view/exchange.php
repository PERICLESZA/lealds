<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Exchange</title>
    <link rel="stylesheet" href="../css/exchange.css" />
    <link rel="stylesheet" href="../css/cadastro.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body>
    <div class="dashboard-container">

        <!-- Campo de busca -->
        <div class="cad-group">
            <div class="input-container">
                <label for="searchInput">Phone No/name:</label>
                <input type="text" id="searchInput" name="searchInput" autocomplete="off" list="customerList" />
                <input type="hidden" id="idcustomer" name="idcustomer" />
                <datalist id="customerList"></datalist>
                <span id="selectedCustomerName" style="display: none;"></span>
            </div>
        </div>

        <!-- Formulário + Imagem -->
        <div class="form-image-wrapper">
            <div class="form-container">
                <div class="cad-group">
                    <div class="input-container">
                        <label for="fk_idcustomer">Company</label>
                        <select id="fk_idcustomer"></select>
                    </div>
                    <div class="input-container">
                        <label>Date</label>
                        <input type="date" id="dtcashflow" />
                    </div>
                    <div class="input-container">
                        <label>Time</label>
                        <input type="text" id="tchalow" />
                    </div>
                    <div class="input-container">
                        <label for="fk_idbankmaster">Our Bank</label>
                        <select id="fk_idbankmaster"></select>
                    </div>
                    <div class="input-container">
                        <label>Pay</label>
                        <input type="text" />
                    </div>
                    <div class="input-container">
                        <label>Receive</label>
                        <input type="text" />
                    </div>
                </div>
                <div class="input-container">
                    <label>Value</label>
                    <input type="text" />
                </div>
                <!-- Imagem
                <div class="image-container">
                    <img src="../cutomer_pic/5269caa7f7.JPG" alt="Foto do cliente" class="customer-image" />
                </div> -->
            </div>

            <!-- Tabela -->
            <div class="table-container">
                <table border="0" class="table">
                    <thead>
                        <tr>
                            <th>Value</th>
                            <th>Cents 1</th>
                            <th>%</th>
                            <th>Value %</th>
                            <th>Subtotal</th>
                            <th>Cents 2</th>
                            <th>Wire</th>
                            <th>Ok</th>
                        </tr>
                    </thead>
                    <tbody id="customer_data"></tbody>
                </table>
            </div>

        </div>

        <script src="../js/exchange.js"></script>
</body>

</html>