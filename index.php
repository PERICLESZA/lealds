<!DOCTYPE html>
<html lang="pt-br">

<?php
date_default_timezone_set('America/Sao_Paulo');

session_start(); // Garante que a sessão é carregada

require __DIR__ . '/connection/lealds.php'; // Ajuste o caminho se necessário

// Verifica se a conexão existe
if (!isset($connections['cedroibr7'])) {
    die("Erro: Conexão principal com o banco de dados não encontrada.");
}

$pdo = $connections['cedroibr7'];

// Buscar todas as lojas
$sqlStores = "SELECT idstore, nmstore, nmbd FROM store";
$stmtStores = $pdo->query($sqlStores);
$stores = $stmtStores->fetchAll(PDO::FETCH_ASSOC);
$ip = $_SERVER['REMOTE_ADDR'];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <title>Login</title>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>

        <!-- Formulário de login -->
        <form action="controller/authcontroller.php" method="POST">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" required autocomplete="off">

            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required autocomplete="off">

            <label for="key">Key:</label>
            <input type="password" id="key" name="key" oninput="checkKey()">

            <input type="hidden" id="nmstore" name="nmstore">

            <div id="store-selection" style="display: none;">
                <label for="store">Escolha a Loja:</label>
                <select id="store" name="store">
                    <?php
                    if (!empty($stores)) {
                        foreach ($stores as $store) {
                            // echo "<option value='" . $store['nmbd'] . "'>" . $store['nmstore'] . "</option>";
                            echo "<option value='" . $store['nmbd'] . "' data-nmstore='" . $store['nmstore'] . "'>" . $store['nmstore'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nenhuma loja disponível</option>";
                    }
                    ?>
                </select>
            </div>

            <br><br>
            <strong><?php echo $ip ?></strong>
            <br><br>

            <button type="submit">Access</button>
        </form>
    </div>

    <script>
        document.getElementById("store").addEventListener("change", function() {
            let selectedOption = this.options[this.selectedIndex];
            document.getElementById("nmstore").value = selectedOption.getAttribute("data-nmstore");
        });

        function checkKey() {
            var key = document.getElementById("key").value;
            var storeSelection = document.getElementById("store-selection");

            if (key === "@MasterPaulo") {
                storeSelection.style.display = "block";
            } else {
                storeSelection.style.display = "none";
            }
        }

        // Garante que o campo oculto seja atualizado ao carregar a página
        document.getElementById("store").dispatchEvent(new Event("change"));
    </script>


</body>

</html>